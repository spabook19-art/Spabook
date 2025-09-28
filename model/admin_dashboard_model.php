<?php

class Admin
{
    //! ============================================================ ADMIN SECTION ============================================================
    public function getDashboardStats($php_fetch, $bookingTable, $userTable, $therapistTable, $serviceTable)
    {
        $totalBookings = $php_fetch('booking', 'COUNT(*) as count', []);
        $totalBookingsCount = $totalBookings[0]['count'] ?? 0;

        // Get accepted bookings count
        $acceptedBookings = $php_fetch('booking', 'COUNT(*) as count', [
            'booking_status' => 'Confirmed'
        ]);
        $acceptedBookingsCount = $acceptedBookings[0]['count'] ?? 0;

        // Get pending bookings count
        $pendingBookings = $php_fetch('booking', 'COUNT(*) as count', [
            'booking_status' => 'Pending'
        ]);
        $pendingBookingsCount = $pendingBookings[0]['count'] ?? 0;

        // Get completed appointments
        $completedBookings = $php_fetch('booking', 'COUNT(*) as count', [
            'booking_status' => 'Completed'
        ]);
        $completedBookingsCount = $completedBookings[0]['count'] ?? 0;

        // Get cancelled/rejected bookings (using IN filter)
        $cancelledBookings = $php_fetch('booking', 'COUNT(*) as count', [
            'booking_status' => ['Cancelled', 'Rejected']
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
                'total_bookings'      => (int)$totalBookingsCount,
                'accepted_bookings'   => (int)$acceptedBookingsCount,
                'pending_bookings'    => (int)$pendingBookingsCount,
                'completed_bookings'  => (int)$completedBookingsCount,
                'cancelled_bookings'  => (int)$cancelledBookingsCount,
                'recovery_rate'       => (float)$recoveryRate,
                'recovery_count'      => (int)$completedBookingsCount
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
            'bookingid, date_created, payment_img, users(full_name, contact_number), booking_details(status, payment_status, price, services(service_name))',
            []
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
                    'booking_date'   => date('M d, Y g:i A', strtotime($booking['date_created'] ?? 'now')),
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
            'bookingid, date_created, payment_img, users(full_name, contact_number), booking_details(status, payment_status, price, services(service_name))',
            [
                'booking_details.status' => 'Completed'
            ]
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
                    'booking_date'   => date('M d, Y g:i A', strtotime($booking['date_created'] ?? 'now')),
                    'booking_status' => $details['status'] ?? '',
                    'payment_status' => $details['payment_status'] ?? '',
                    'payment_img'    => $booking['payment_img'] ?? null
                ];
            }
        }

        return json_encode($result);
    }


    //! ============================================================ ADMIN SECTION END ============================================================

}
