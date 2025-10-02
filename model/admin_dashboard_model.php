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
                'recovery_count'      => $completedBookingsCount
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
            'bookingid, date_created, payment_img, users(full_name, contact_number), booking_details(status, payment_status, price,date_modified, services(service_name))',
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

                $result[] = [
                    'bookingid'      => $booking['bookingid'],
                    'user_name'      => $fullname,
                    'user_phone'     => $contact,
                    'services_name'  => $serviceName,
                    'price'          => $details['price'] ?? 0,
                    'booking_date'   => date('M d, Y g:i A', strtotime($details['date_modified'] ?? 'now')),
                    'booking_status' => $details['status'] ?? '',
                    'payment_status' => $details['payment_status'] ?? '',
                    'payment_img'    => $booking['payment_img'] ?? null
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
            'bookingid, users(profile_picture,full_name), booking_details(bookingdetailsid,status, price,date_modified, services(service_name))',
            [
                'booking_details.status' => $status // Only Pending bookings
            ],
        );

        foreach ($bookings as $booking) {
            // Handle nested arrays safely
            $user       = $booking['users'] ?? null;
            $fullname   = $user['full_name'] ?? 'Unknown User';
            $profilePic = $user['profile_picture'] ?? '';

            $detailsArr = $booking['booking_details'] ?? [];
            foreach ($detailsArr as $details) {
                $service    = $details['services'] ?? null;
                $serviceName = $service['service_name'] ?? 'Unknown Service';

                $result[] = [
                    'bookingdetailsid'     => $details['bookingdetailsid'],
                    'user_name'      => $fullname,
                    'services_name'  => $serviceName,
                    'price'          => $details['price'] ?? 0,
                    'booking_date'   => date('M d, Y g:i A', strtotime($details['date_modified'] ?? 'now')),
                    'booking_status' => $details['status'] ?? '',
                    'profile_picture' => $profilePic ?? null
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
            'bookingid, payment_img, users(full_name, contact_number,email), booking_details(bookingdetailsid,status, price, quantity,schedule_start, services(service_name, description, per_minute,price))',
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

    //! ============================================================ ADMIN SECTION END ============================================================

}
