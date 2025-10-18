<?php

class Admin
{
    //! ============================================================ ADMIN SECTION ============================================================
    public function getDashboardStats($php_fetch)
    {
        $responseData = [];

        $totalBookings = $php_fetch('booking_details', 'COUNT(*) as count', []);

        $totalBookingsCount = $totalBookings[0]['count'] ?? 0;

        // Get accepted bookings count
        $acceptedBookings = $php_fetch('booking_details', 'COUNT(*) as count', [
            'status' => 'Confirmed'
        ]);
        $acceptedBookingsCount = $acceptedBookings[0]['count'] ?? 0;

        // Get pending bookings count
        $pendingBookings = $php_fetch('booking_details', 'COUNT(*) as count', [
            'status' => ['Pending']
        ]);
        $pendingBookingsCount = $pendingBookings[0]['count'] ?? 0;

        // Get pending bookings count
        $ongoingBookings = $php_fetch('booking_details', 'COUNT(*) as count', [
            'status' => ['On-Going']
        ]);
        $ongoingBookingsCount = $ongoingBookings[0]['count'] ?? 0;

        // Get completed appointments
        $completedBookings = $php_fetch('booking_details', 'COUNT(*) as count', [
            'status' => 'Completed'
        ]);
        $completedBookingsCount = $completedBookings[0]['count'] ?? 0;

        // Get cancelled/rejected bookings (using IN filter)
        $cancelledBookings = $php_fetch('booking_details', 'COUNT(*) as count', [
            'status' => ['Cancelled', 'Rejected']
        ]);
        $cancelledBookingsCount = $cancelledBookings[0]['count'] ?? 0;

        // Calculate recovery rate (completed vs total)
        $recoveryRate = $totalBookingsCount > 0
            ? round(($completedBookingsCount / $totalBookingsCount) * 100, 1)
            : 0;

        // Create the response data
        $responseData = [
            'status' => 'success',
            'data' => [
                'total_bookings'      => $totalBookingsCount,
                'accepted_bookings'   => $acceptedBookingsCount,
                'pending_bookings'    => $pendingBookingsCount,
                'completed_bookings'  => $completedBookingsCount,
                'cancelled_bookings'  => $cancelledBookingsCount,
                'recovery_rate'       => $recoveryRate,
                'recovery_count'      => $completedBookingsCount,
                'ongoing_bookings'    => $ongoingBookingsCount
            ]
        ];

        // Return the response
        return json_encode($responseData);
    }


    public function getTotalBookings($php_fetch)
    {
        $result = [];

        // Fetch bookings with joins
        $bookings = $php_fetch(
            'booking',
            'bookingid, date_created, payment_img, users(full_name, contact_number), booking_details(bookingdetailsid, status, payment_status, price, schedule_start, schedule_end, date_modified, services(id, service_name), therapist_id)',
            [],
        );

        foreach ($bookings as $booking) {
            // Handle nested arrays safely
            $user       = $booking['users'] ?? null;
            $fullname   = $user['full_name'] ?? 'Unknown User';
            $contact    = $user['contact_number'] ?? '';

            $detailsArr = $booking['booking_details'] ?? [];
            foreach ($detailsArr as $details) {
                $service    = $details['services'] ?? null;
                $serviceName = $service['service_name'] ?? 'Unknown Service';
                $serviceId = $service['id'] ?? null;
                $scheduleStart = $details['schedule_start'] ?? null;
                $scheduleEnd = $details['schedule_end'] ?? null;
                $displayDate = $scheduleStart ? date('M d, Y g:i A', strtotime($scheduleStart)) : date('M d, Y g:i A', strtotime($details['date_modified'] ?? 'now'));

                $result[] = [
                    'bookingid'      => $booking['bookingid'],
                    'user_name'      => $fullname,
                    'user_phone'     => $contact,
                    'services_name'  => $serviceName,
                    'service_id'     => $serviceId,
                    'price'          => $details['price'] ?? 0,
                    'booking_date'   => $displayDate,
                    'booking_status' => $details['status'] ?? '',
                    'payment_status' => $details['payment_status'] ?? '',
                    'payment_img'    => $booking['payment_img'] ?? null,
                    'schedule_start' => $scheduleStart,
                    'schedule_end'   => $scheduleEnd
                ];
            }
        }

        return json_encode($result);
    }

    public function getAppointmentHistory($php_fetch)
    {
        $result = [];

        // Fetch bookings with joins
        $bookings = $php_fetch(
            'booking',
            'bookingid, date_created, payment_img, users(full_name, contact_number), booking_details(status, payment_status, price, schedule_start,schedule_end, services(service_name))',
            [
                'booking_details.status' => ['Completed', 'Cancelled', 'Rejected'] // ✅ Proper IN condition
            ]
        );

        $result = [];

        foreach ($bookings as $booking) {
            // Handle nested arrays safely
            $user     = $booking['users'] ?? null;
            $fullname = $user['full_name'] ?? 'Unknown User';
            $contact  = $user['contact_number'] ?? '';

            $detailsArr = $booking['booking_details'] ?? [];
            foreach ($detailsArr as $details) {
                $service     = $details['services'] ?? null;
                $serviceName = $service['service_name'] ?? 'Unknown Service';
                $duration   = (strtotime($details['schedule_end'] ?? 'now') - strtotime($details['schedule_start'] ?? 'now')) / 60;

                $result[] = [
                    'bookingid'      => $booking['bookingid'],
                    'user_name'      => $fullname,
                    'user_phone'     => $contact,
                    'services_name'  => $serviceName,
                    'price'          => $details['price'] ?? 0,
                    'schedule_end'   => date('M d, Y g:i A', strtotime($booking['schedule_end'] ?? 'now')),
                    'duration'       => $duration . ' mins',
                    'booking_status' => $details['status'] ?? '',
                    'payment_status' => $details['payment_status'] ?? '',
                    'payment_img'    => $booking['payment_img'] ?? null
                ];
            }
        }

        return json_encode($result);
    }

    public function getRecoverableBookings($php_fetch)
    {
        $result = [];

        // Fetch bookings with joins
        $bookings = $php_fetch(
            'booking',
            'bookingid, date_created, payment_img, users(full_name, contact_number), booking_details(status, payment_status, price, schedule_start,schedule_end,date_modified , services(service_name))',
            [
                'booking_details.status' => 'Cancelled' // Only Cancelled bookings
            ]
        );

        $result = [];

        foreach ($bookings as $booking) {
            // Handle nested arrays safely
            $user     = $booking['users'] ?? null;
            $fullname = $user['full_name'] ?? 'Unknown User';
            $contact  = $user['contact_number'] ?? '';

            $detailsArr = $booking['booking_details'] ?? [];
            foreach ($detailsArr as $details) {
                $service     = $details['services'] ?? null;
                $serviceName = $service['service_name'] ?? 'Unknown Service';
                $duration   = (strtotime($details['schedule_end'] ?? 'now') - strtotime($details['schedule_start'] ?? 'now')) / 60;

                $result[] = [
                    'bookingid'      => $booking['bookingid'],
                    'user_name'      => $fullname,
                    'user_phone'     => $contact,
                    'services_name'  => $serviceName,
                    'price'          => $details['price'] ?? 0,
                    'date_created'   => date('M d, Y g:i A', strtotime($details['date_modified'] ?? 'now')),
                    'duration'       => $duration . ' mins',
                    'booking_status' => $details['status'] ?? '',
                    'payment_status' => $details['payment_status'] ?? '',
                    'payment_img'    => $booking['payment_img'] ?? null
                ];
            }
        }

        return json_encode($result);
    }


    public function recoverBooking($php_update, $bookingid, $current_datetimestamp)
    {
        // Update booking_details status to 'Confirmed' for the given bookingid
        $updateData = [
            'status' => 'Pending',
            'date_modified' => $current_datetimestamp
        ];

        $filters = [
            'booking_id' => $bookingid
        ];

        $updateResult = $php_update('booking_details',  $updateData, $filters);

        if (isset($updateResult['error'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Failed to recover booking: ' . $updateResult['error']
            ]);
        }

        return json_encode([
            'status' => 'success',
            'message' => 'Booking recovered successfully.'
        ]);
    }

    public function loadBookingRequests($php_fetch, $status)
    {
        $result = [];

        // Fetch bookings with joins
        $bookings = $php_fetch(
            'booking',
            'bookingid, users(profile_picture, full_name), booking_details(bookingdetailsid, bookingdetails_id, status, price, date_modified, schedule_start, schedule_end, therapist_id, services(id, service_name))',
            [
                'booking_details.status' => $status
            ],
        );

        foreach ($bookings as $booking) {
            // Safely access user info
            $user        = $booking['users'] ?? [];
            $fullname    = $user['full_name'] ?? 'Unknown User';
            $profilePic  = $user['profile_picture'] ?? '';

            // Safely access booking details
            $detailsArr = $booking['booking_details'] ?? [];

            foreach ($detailsArr as $details) {
                $service      = $details['services'] ?? [];
                $serviceName  = $service['service_name'] ?? 'Unknown Service';
                $serviceId    = $service['id'] ?? null;
                $scheduleStart = $details['schedule_start'] ?? null;
                $scheduleEnd   = $details['schedule_end'] ?? null;
                $therapistId   = $details['therapist_id'] ?? null;

                $result[] = [
                    'bookingdetailsid'   => $details['bookingdetailsid'] ?? null,
                    'bookingdetails_id'  => $details['bookingdetails_id'] ?? null,
                    'user_name'          => $fullname,
                    'services_name'      => $serviceName,
                    'service_id'         => $serviceId,
                    'price'              => $details['price'] ?? 0,
                    'booking_date'       => date('M d, Y g:i A', strtotime($details['date_modified'] ?? 'now')),
                    'booking_status'     => $details['status'] ?? '',
                    'profile_picture'    => $profilePic,
                    'schedule_start'     => $scheduleStart,
                    'schedule_end'       => $scheduleEnd,
                    'therapist_id'       => $therapistId
                ];
            }
        }

        return json_encode($result);
    }

    public function getBookingDetails($php_fetch, $bookingdetailsid)
    {
        $result = [];
        // Fetch booking details with joins
        $bookings = $php_fetch(
            'booking',
            'bookingid, payment_img, users(full_name, contact_number,email), booking_details(bookingdetailsid,bookingdetails_id,status, price, quantity,schedule_start, services(service_name, description, per_minute,price))',
            [
                'booking_details.bookingdetailsid' => $bookingdetailsid
            ],

        );
        foreach ($bookings as $booking) {
            // Handle nested arrays safely
            $user       = $booking['users'] ?? null;
            $fullname   = $user['full_name'] ?? 'Unknown User';
            $contact = $user['contact_number'] ?? 'Not Provided';
            $email = $user['email'] ?? 'Not Provided';

            $detailsArr = $booking['booking_details'] ?? [];
            foreach ($detailsArr as $details) {
                $service = $details['services'] ?? null;
                $serviceName = $service['service_name'] ?? 'Unknown Service';
                $servicDesc = $service['description'] ?? 'Unknown Service';
                $serviceDuration = $service['per_minute'] ?? 'Unknown Service';
                $servicePrice = $service['price'] ?? 'Unknown Service';

                $result = [
                    'booking_id'     => $details['bookingdetailsid'],
                    'bookingdetails_id'     => $details['bookingdetails_id'],
                    'date_schedule'   => date('M d, Y ', strtotime($details['schedule_start']) ?? ''),
                    'time_schedule'   => date('g:i A', strtotime($details['schedule_start']) ?? ''),
                    'booking_status' => $details['status'] ?? '',
                    'totalprice'     => $details['price'] ?? 0,
                    'quantity'       => $details['quantity'] ?? 0,
                    'serviceprice'   => $servicePrice ?? 0,
                    'user_name'      => $fullname,
                    'contact'        => $contact,
                    'email'          => $email,
                    'services_name'  => $serviceName,
                    'service_description' => $servicDesc,
                    'service_duration' => $serviceDuration,
                    'payment_img'    => $booking['payment_img'] ?? null
                ];
            }
        }
        return json_encode($result);
    }

    public function declineBookingRequest($php_update, $bookingdetailsid, $current_datetimestamp)
    {
        // Update booking_details status to 'Rejected' for the given bookingdetailsid
        $updateData = [
            'status' => 'Cancelled',
            'date_modified' => $current_datetimestamp
        ];

        $filters = [
            'bookingdetailsid' => $bookingdetailsid
        ];

        $updateResult = $php_update('booking_details',  $updateData, $filters);

        if (isset($updateResult['error'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Failed to decline booking request: ' . $updateResult['error']
            ]);
        }

        return json_encode([
            'status' => 'success',
            'message' => 'Booking request declined successfully.'
        ]);
    }

    public function acceptBookingRequest($php_update, $bookingdetailsid, $current_datetimestamp)
    {
        // Update booking_details status to 'Confirmed' for the given bookingdetailsid
        $updateData = [
            'status' => 'Confirmed',
            'date_modified' => $current_datetimestamp
        ];

        $filters = [
            'bookingdetailsid' => $bookingdetailsid
        ];

        $updateResult = $php_update('booking_details',  $updateData, $filters);

        if (isset($updateResult['error'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Failed to accept booking request: ' . $updateResult['error']
            ]);
        }

        return json_encode([
            'status' => 'success',
            'message' => 'Booking request accepted successfully.'
        ]);
    }


    public function updateBookingStatus($php_update, $bookingdetailsid, $new_status, $current_datetimestamp)
    {
        // Update booking_details status to the new status for the given bookingdetailsid
        // if ($new_status == 'On-Going') {
        //     $updateData = [
        //         'status' => $new_status,
        //         'date_modified' => $current_datetimestamp
        //     ];
        // } else {
        //     $updateData = [
        //         'status' => $new_status,
        //         'date_modified' => $current_datetimestamp
        //     ];
        // }
        $updateData = [
            'status' => $new_status,
            'date_modified' => $current_datetimestamp
        ];


        $filters = [
            'bookingdetailsid' => $bookingdetailsid
        ];

        $updateResult = $php_update('booking_details',  $updateData, $filters);

        if (isset($updateResult['error'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Failed to update booking status: ' . $updateResult['error']
            ]);
        }

        return json_encode([
            'status' => 'success',
            'message' => 'Booking status updated successfully.'
        ]);
    }


    public function rescheduleBooking($php_update, $bookingdetailsid, $schedule_start, $schedule_end, $reason, $therapist_id, $current_datetimestamp)
    {
        // Update booking_details schedule_start and schedule_end for the given bookingdetailsid
        $updateData = [
            'schedule_start' => $schedule_start,
            'schedule_end' => $schedule_end,
            'reschedule_reason' => $reason,
            'date_modified' => $current_datetimestamp
        ];

        if ($therapist_id !== null) {
            $updateData['therapist_id'] = $therapist_id;
        }

        $filters = [
            'bookingdetailsid' => $bookingdetailsid
        ];

        $updateResult = $php_update('booking_details',  $updateData, $filters);

        if (isset($updateResult['error'])) {
            return json_encode([
                'status' => 'error',
                'message' => 'Failed to reschedule booking: ' . $updateResult['error']
            ]);
        }

        return json_encode([
            'status' => 'success',
            'message' => 'Booking rescheduled successfully.'
        ]);
    }
    //! ============================================================ ADMIN SECTION END ============================================================

}
