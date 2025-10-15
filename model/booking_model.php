<?php

class BookingModel
{
    //! ============================== BOOKING ==============================

    //! ============================== ADMIN BOOKING MANAGEMENT ==============================

    public function getAdminBookingRequests($php_fetch)
    {
        try {
            // Get all pending bookings directly
            $bookings = $php_fetch('booking', '*', ['booking_status' => 'Pending']);

            // Log the result for debugging (disabled in production)
            // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Pending bookings count: " . count($bookings) . "\n", FILE_APPEND);

            if (!$bookings || count($bookings) === 0) {
                return ['status' => 'nodata'];
            }

            // Process the bookings
            return $this->processBookings($bookings, $php_fetch);
        } catch (Exception $e) {
            error_log("Error fetching pending bookings: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Helper method to format payment image data
    private function formatPaymentImage($imageData)
    {
        if (!$imageData) {
            return null;
        }

        // If it's already a URL, return as is
        if (is_string($imageData) && (strpos($imageData, 'http://') === 0 || strpos($imageData, 'https://') === 0)) {
            return $imageData;
        }

        // Fix double data URL prefix issue
        if (is_string($imageData) && strpos($imageData, 'data:image/png;base64,data:image/') === 0) {
            // Extract the second data URL which is the correct one
            $secondDataUrlIndex = strpos($imageData, 'data:', 5);
            if ($secondDataUrlIndex !== false) {
                $imageData = substr($imageData, $secondDataUrlIndex);
            }
        }

        // If it's already a data URL, return as is
        if (is_string($imageData) && strpos($imageData, 'data:') === 0) {
            return $imageData;
        }

        // If it's a base64 string without the data URL prefix, add the prefix
        if (is_string($imageData) && !empty($imageData)) {
            // Simple check if it looks like base64
            if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $imageData)) {
                return $imageData; // Return just the base64 string, the frontend will add the data URL prefix
            }
        }

        // For other cases, return as is
        return $imageData;
    }

    // Helper method to format datetime from ISO 8601 to readable format
    private function formatDateTime($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        try {
            // Parse ISO 8601 format (e.g., "2025-10-03T17:00:27.244088")
            $timestamp = strtotime($dateTime);
            if ($timestamp === false) {
                return $dateTime; // Return original if parsing fails
            }
            
            // Format as "Y-m-d H:i" (e.g., "2025-10-03 17:00")
            return date('Y-m-d H:i', $timestamp);
        } catch (Exception $e) {
            return $dateTime; // Return original if error occurs
        }
    }

    public function processBookings($bookings, $php_fetch)
    {
        $result = [];

        foreach ($bookings as $booking) {
            try {
                // Get user details
                $user = null;
                if (isset($booking['user_id']) && !empty($booking['user_id'])) {
                    $user = $php_fetch('users', '*', ['user_id' => $booking['user_id']]);
                }

                // Get booking details and services
                $booking_details = $php_fetch('booking_details', '*', ['booking_id' => $booking['bookingid']]);

                $services = [];
                if ($booking_details) {
                    foreach ($booking_details as $detail) {
                        $service = $php_fetch('services', '*', ['id' => $detail['service_id']]);
                        if ($service) {
                            $services[] = [
                                'name' => $service[0]['service_name'],
                                'quantity' => $detail['quantity'],
                                'price' => $detail['price']
                            ];
                        }
                    }
                }

                // Get the booking date from the first booking detail if available
                $booking_date = $booking['date_created'] ?? date('Y-m-d H:i:s');
                $booking_time = null;

                if ($booking_details && count($booking_details) > 0 && isset($booking_details[0]['booking_date'])) {
                    $booking_date = $booking_details[0]['booking_date'];
                    $booking_time = $booking_details[0]['booking_time'] ?? null;
                }

                $result[] = [
                    'bookingid' => $booking['bookingid'],
                    'user_name' => ($user && count($user) > 0 ? $user[0]['full_name'] : 'Unknown User'),
                    'user_email' => ($user && count($user) > 0 ? $user[0]['email'] : 'No email provided'),
                    'user_phone' => ($user && count($user) > 0 ? $user[0]['contact_number'] : 'No phone provided'),
                    'total_price' => $booking['total_price'] ?? 0,
                    'booking_date' => $booking_date,
                    'booking_time' => $booking_time,
                    'booking_status' => $booking['booking_status'] ?? 'Unknown',
                    'payment_img' => $this->formatPaymentImage($booking['payment_img'] ?? null),
                    'services' => $services
                ];
            } catch (Exception $e) {
                error_log("Error processing booking {$booking['bookingid']}: " . $e->getMessage());
                continue;
            }
        }

        return $result;
    }

    public function getAdminBookingAccepted($php_fetch)
    {
        try {
            // Get all confirmed bookings directly
            $bookings = $php_fetch('booking', '*', ['booking_status' => 'Confirmed']);

            // Log the result for debugging (disabled in production)
            // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Confirmed bookings count: " . count($bookings) . "\n", FILE_APPEND);

            if (!$bookings || count($bookings) === 0) {
                return ['status' => 'nodata'];
            }

            // Process the bookings
            return $this->processBookings($bookings, $php_fetch);
        } catch (Exception $e) {
            error_log("Error fetching accepted bookings: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getBookingDetailsForAdmin($php_fetch, $bookingid)
    {
        try {
            // Log the function call (disabled in production)
            // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - getBookingDetailsForAdmin called for booking ID: $bookingid\n", FILE_APPEND);

            // Validate the booking ID
            if (!$bookingid || !is_numeric($bookingid)) {
                // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Invalid booking ID: $bookingid\n", FILE_APPEND);
                return ['status' => 'error', 'message' => 'Invalid booking ID'];
            }

            // Get booking data directly
            $booking_data = $php_fetch('booking', '*', ['bookingid' => $bookingid]);

            // Log the booking data result (disabled in production)
            // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Booking data result: " . json_encode($booking_data) . "\n", FILE_APPEND);

            if (!$booking_data || count($booking_data) === 0) {
                // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Booking not found with ID: $bookingid\n", FILE_APPEND);
                return ['status' => 'error', 'message' => 'Booking not found'];
            }

            // Process the booking using the same method as other booking functions
            $processed = $this->processBookings($booking_data, $php_fetch);

            // Log the processed result (disabled in production)
            // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Processed booking result: " . json_encode($processed) . "\n", FILE_APPEND);

            if (!$processed || count($processed) === 0) {
                // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Failed to process booking with ID: $bookingid\n", FILE_APPEND);
                return ['status' => 'error', 'message' => 'Failed to process booking details'];
            }

            // Return the first (and only) booking from the processed results
            return $processed[0];
        } catch (Exception $e) {
            error_log("Error fetching booking details for admin: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    //! ============================== END ADMIN BOOKING MANAGEMENT ==============================

    public function createBooking($php_insert, $table, $data)
    {
        // Format payment image if present
        if (isset($data['payment_img'])) {
            $data['payment_img'] = $this->formatPaymentImage($data['payment_img']);
        }

        $insert = $php_insert($table, $data);

        // Debug output
        file_put_contents('c:/xampp/htdocs/SpaBook_2/debug_insert.txt', print_r($insert, true));
        error_log('createBooking insert result: ' . print_r($insert, true));

        // Check if $insert is an array or not
        if (!is_array($insert)) {
            $errorMsg = "Insert returned non-array: " . print_r($insert, true);
            file_put_contents('c:/xampp/htdocs/SpaBook_2/debug_error.txt', $errorMsg);
            error_log('createBooking ERROR: ' . $errorMsg);
            return ['status' => 'error', 'message' => 'Insert failed: non-array result', 'debug' => $insert];
        }

        if (isset($insert['error'])) {
            $errorDetails = $insert['response'] ?? $insert['error'] ?? 'Unknown error';
            $errorMsg = is_string($errorDetails) ? $errorDetails : json_encode($errorDetails);
            error_log('createBooking ERROR: ' . $errorMsg);
            file_put_contents('c:/xampp/htdocs/SpaBook_2/debug_error.txt', "Insert error:\n" . print_r($insert, true));
            return ['status' => 'error', 'message' => 'Database error: ' . $errorMsg, 'debug' => $insert];
        }

        // Return the array directly, not JSON encoded
        error_log('createBooking SUCCESS: Booking created with ID ' . ($insert[0]['bookingid'] ?? 'unknown'));
        return $insert[0] ?? $insert; // Supabase returns array of inserted records
    }



    public function getBookingsByUser($php_fetch, $table, $user_id)
    {
        try {
            // Get all bookings for this user (using same approach as getUserRecentServices)
            $bookings = $php_fetch('booking', '*', ['user_id' => $user_id]);

            if (!$bookings || count($bookings) === 0) {
                return ['status' => 'nodata'];
            }

            $result = [];
            foreach ($bookings as $booking) {
                // Get booking details and services for this booking
                $booking_details = $php_fetch('booking_details', '*', ['booking_id' => $booking['bookingid']]);

                if ($booking_details) {
                    // Return one row per service (from booking_details)
                    foreach ($booking_details as $detail) {
                        $service = $php_fetch('services', 'service_name', ['id' => $detail['service_id']]);
                        if ($service && count($service) > 0) {
                            $result[] = [
                                'bookingid' => $booking['bookingid'],
                                'bookingdetailsid' => $detail['bookingdetailsid'],
                                'service_name' => $service[0]['service_name'],
                                'quantity' => $detail['quantity'],
                                'status' => $detail['status'] ?? 'Pending', // Get status from booking_details
                                'booking_date' => $this->formatDateTime($booking['date_created']),
                                'schedule_start' => $this->formatDateTime($detail['schedule_start'] ?? null),
                                'schedule_end' => $this->formatDateTime($detail['schedule_end'] ?? null),
                                'price' => $detail['price'],
                                'total_amount' => $detail['price'] * $detail['quantity'],
                                'payment_status' => $booking['payment_status'] ?? false,
                                'therapist_id' => $detail['therapist_id'] ?? null,
                                'patient_name' => $detail['patient_name'] ?? null
                            ];
                        }
                    }
                }
            }

            // Sort by date (most recent first)
            usort($result, function ($a, $b) {
                return strtotime($b['booking_date']) - strtotime($a['booking_date']);
            });

            if (count($result) > 0) {
                return ['status' => 'success', 'bookings' => $result];
            } else {
                return ['status' => 'nodata'];
            }
        } catch (Exception $e) {
            error_log("Error in getBookingsByUser: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    public function updateBookingStatus($php_update, $table, $bookingid, $status)
    {
        // The correct order for $php_update is: $table, $data, $filters
        $update = $php_update($table, ['booking_status' => $status], ['bookingid' => $bookingid]);
        return isset($update['error']) ? json_encode(['status' => 'error', 'message' => $update['error']]) : json_encode(['status' => 'success']);
    }

    public function uploadPayment($php_update, $table, $bookingid, $image)
    {
        // Format the image data before saving
        $formattedImage = $this->formatPaymentImage($image);

        $update = $php_update($table, ['bookingid' => $bookingid], [
            'payment_status' => true,
            'payment_img' => $formattedImage,
            'booking_status' => 'Pending'
        ]);
        return isset($update['error']) ? json_encode(['status' => 'error']) : json_encode(['status' => 'success']);
    }

    //! ============================== BOOKING DETAILS ==============================

    public function addBookingDetail($php_insert, $table, $data)
    {
        // Use service role for booking_details inserts to avoid sequence permission errors
        global $projectUrl, $serviceRoleKey;
        
        $url = "https://rijeyetpxumyxzggihre.supabase.co/rest/v1/$table";
        $headers = [
            "apikey: $serviceRoleKey",
            "Authorization: Bearer $serviceRoleKey",
            "Content-Type: application/json",
            "Prefer: return=representation"
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Enhanced debugging - write to file
        $debugInfo = "=== BOOKING_DETAIL INSERT DEBUG ===\n";
        $debugInfo .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        $debugInfo .= "HTTP Code: $httpCode\n";
        $debugInfo .= "Data Sent: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
        $debugInfo .= "Response: $response\n";
        $debugInfo .= "cURL Error: $curlError\n";
        $debugInfo .= "=====================================\n\n";
        file_put_contents(__DIR__ . '/../debug_booking_details.txt', $debugInfo, FILE_APPEND);
        
        if ($curlError) {
            error_log('addBookingDetail cURL Error: ' . $curlError);
            return json_encode(['status' => 'error', 'message' => 'cURL Error: ' . $curlError, 'data' => $data]);
        }
        
        if ($httpCode >= 400) {
            error_log('addBookingDetail HTTP Error ' . $httpCode . ': ' . $response);
            return json_encode(['status' => 'error', 'message' => 'HTTP Error: ' . $httpCode, 'response' => $response, 'data' => $data]);
        }
        
        $insert = json_decode($response, true);
        
        if ($insert === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log('addBookingDetail JSON Parse Error: ' . json_last_error_msg());
            return json_encode(['status' => 'error', 'message' => 'JSON Parse Error', 'data' => $data]);
        }
        
        error_log('addBookingDetail SUCCESS: ' . print_r($insert, true));
        return json_encode(['status' => 'success', 'data' => $insert]);
    }

    public function getBookingDetails($php_fetch, $table, $bookingid)
    {
        $details = $php_fetch($table, '*', ['booking_id' => $bookingid]);
        return !empty($details) ? json_encode($details) : json_encode(['status' => 'nodata']);
    }

    public function updateBookingDetailStatus($php_update, $table, $detailid, $status)
    {
        // The correct order for $php_update is: $table, $data, $filters
        $update = $php_update($table, ['status' => $status], ['bookingdetailsid' => $detailid]);
        return isset($update['error']) ? json_encode(['status' => 'error', 'message' => $update['error']]) : json_encode(['status' => 'success']);
    }

    //! ============================== BOOKING DETAILS TRANSACTION ==============================

    public function addBookingDetailTransaction($php_insert, $table, $data)
    {
        $insert = $php_insert($table, $data);
        return isset($insert['error']) ? json_encode(['status' => 'error']) : json_encode(['status' => 'success']);
    }

    public function getDetailTransactions($php_fetch, $table, $bookingdetailsid)
    {
        $data = $php_fetch($table, '*', ['bookingdetails_id' => $bookingdetailsid]);
        return !empty($data) ? json_encode($data) : json_encode(['status' => 'nodata']);
    }

    //! ============================== USER BOOKING METHODS ==============================

    public function getUserBookingStatus($php_fetch, $bookings_table, $booking_details_table, $services_table, $user_id)
    {
        try {
            // Get all bookings from booking table only (header data)
            $bookings = $php_fetch($bookings_table, '*', ['user_id' => $user_id]);

            if (!empty($bookings) && isset($bookings[0])) {
                $result = [];
                
                foreach ($bookings as $booking) {
                    // Get all booking details (services) for this booking
                    $booking_details = $php_fetch($booking_details_table, '*', ['booking_id' => $booking['bookingid']]);
                    
                    if ($booking_details) {
                        foreach ($booking_details as $detail) {
                            // Only include active services (not completed, cancelled, or rejected)
                            $status = strtolower($detail['status'] ?? 'pending');
                            
                            if (!in_array($status, ['completed', 'cancelled', 'rejected'])) {
                                // Get service name
                                $service = $php_fetch($services_table, 'service_name', ['id' => $detail['service_id']]);
                                
                                // Return ONE row per service with status from booking_details
                                $result[] = [
                                    'bookingid' => $booking['bookingid'],
                                    'bookingdetailsid' => $detail['bookingdetailsid'],
                                    'service_name' => $service && count($service) > 0 ? $service[0]['service_name'] : 'Unknown Service',
                                    'status' => $detail['status'] ?? 'Pending', // From booking_details
                                    'booking_date' => $this->formatDateTime($booking['date_created']),
                                    'schedule_start' => $this->formatDateTime($detail['schedule_start'] ?? null),
                                    'schedule_end' => $this->formatDateTime($detail['schedule_end'] ?? null),
                                    'quantity' => $detail['quantity'],
                                    'price' => $detail['price'],
                                    'total_amount' => $detail['price'] * $detail['quantity'],
                                    'payment_status' => $booking['payment_status']
                                ];
                            }
                        }
                    }
                }
                
                return json_encode($result);
            } else {
                return json_encode('nodata');
            }
        } catch (Exception $e) {
            error_log("Error in getUserBookingStatus: " . $e->getMessage());
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getUserRecentServices($php_fetch, $bookings_table, $booking_details_table, $services_table, $user_id)
    {
        try {
            // Get all bookings for this user
            $bookings = $php_fetch($bookings_table, '*', ['user_id' => $user_id]);

            if (!$bookings || count($bookings) === 0) {
                return json_encode('nodata');
            }

            $result = [];
            foreach ($bookings as $booking) {
                // Get booking details and services for this booking
                $booking_details = $php_fetch($booking_details_table, '*', ['booking_id' => $booking['bookingid']]);

                if ($booking_details) {
                    foreach ($booking_details as $detail) {
                        // Only include completed services from booking_details
                        $detailStatus = strtolower($detail['status'] ?? '');
                        if ($detailStatus === 'completed') {
                            $service = $php_fetch($services_table, 'service_name', ['id' => $detail['service_id']]);
                            if ($service && count($service) > 0) {
                                $result[] = [
                                    'bookingid' => $booking['bookingid'],
                                    'bookingdetailsid' => $detail['bookingdetailsid'],
                                    'service_name' => $service[0]['service_name'],
                                    'status' => $detail['status'], // From booking_details
                                    'booking_date' => $this->formatDateTime($booking['date_created']),
                                    'schedule_start' => $this->formatDateTime($detail['schedule_start'] ?? null),
                                    'schedule_end' => $this->formatDateTime($detail['schedule_end'] ?? null),
                                    'total_amount' => $detail['price'] * $detail['quantity'],
                                    'quantity' => $detail['quantity']
                                ];
                            }
                        }
                    }
                }
            }

            // Sort by date (most recent first)
            usort($result, function ($a, $b) {
                return strtotime($b['booking_date']) - strtotime($a['booking_date']);
            });

            // Limit to last 10 services
            $result = array_slice($result, 0, 10);

            return json_encode(count($result) > 0 ? $result : 'nodata');
        } catch (Exception $e) {
            error_log("Error in getUserRecentServices: " . $e->getMessage());
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // public function getBookingServicesForCompletion($php_fetch, $bookingid)
    // {
    //     try {
    //         // Get booking information
    //         $booking = $php_fetch('booking', '*', ['bookingid' => $bookingid]);
    //         if (empty($booking)) {
    //             return ['status' => 'error', 'message' => 'Booking not found'];
    //         }

    //         // Get user information
    //         $user = $php_fetch('users', 'full_name', ['user_id' => $booking[0]['user_id']]);
    //         $booking[0]['user_name'] = $user[0]['full_name'] ?? 'Unknown User';

    //         // Get booking details with services
    //         $query = "
    //             SELECT 
    //                 bd.bookingdetailsid,
    //                 bd.booking_id,
    //                 bd.service_id,
    //                 bd.quantity,
    //                 bd.price,
    //                 bd.therapist_id,
    //                 bd.person_number,
    //                 bd.booking_date,
    //                 bd.booking_time,
    //                 bd.status,
    //                 bd.therapist_notes,
    //                 bd.pain_level,
    //                 bd.mobility_level,
    //                 bd.overall_progress,
    //                 bd.completed_at,
    //                 bd.updated_at,
    //                 s.service_name,
    //                 s.description as service_description,
    //                 s.price as service_base_price,
    //                 s.per_minute
    //             FROM 
    //                 $booking_details_table bd
    //             LEFT JOIN 
    //                 $services_table s ON bd.service_id = s.id
    //             WHERE 
    //                 bd.booking_id = :bookingid
    //             ORDER BY 
    //                 bd.bookingdetailsid ASC
    //         ";

    //         // Log the query for debugging
    //         error_log("Booking services query: " . $query . " with bookingid: " . $bookingid);

    //         // Use parameters to avoid SQL injection and formatting issues
    //         $services = $php_fetch('', '', [], $query, [':bookingid' => $bookingid]);

    //         if (empty($services)) {
    //             return ['status' => 'error', 'message' => 'No services found for this booking'];
    //         }

    //         // Add default status if not set
    //         foreach ($services as &$service) {
    //             if (empty($service['status'])) {
    //                 $service['status'] = 'pending';
    //             }
    //         }

    //         return [
    //             'status' => 'success',
    //             'data' => [
    //                 'booking' => $booking[0],
    //                 'services' => $services
    //             ]
    //         ];
    //     } catch (Exception $e) {
    //         error_log("Error in getBookingServicesForCompletion: " . $e->getMessage());
    //         return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    //     }
    // }

    public function updateServiceCompletion($php_fetch, $php_update, $booking_details_table, $bookings_table, $bookingDetailId, $therapistNotes, $progressData, $action)
    {
        try {
            // Prepare update data
            $updateData = [
                'therapist_notes' => $therapistNotes,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Add progress data for stroke treatments
            if ($progressData) {
                if (isset($progressData['pain_level'])) {
                    $updateData['pain_level'] = intval($progressData['pain_level']);
                }
                if (isset($progressData['mobility_level'])) {
                    $updateData['mobility_level'] = intval($progressData['mobility_level']);
                }
                if (isset($progressData['overall_progress'])) {
                    $updateData['overall_progress'] = intval($progressData['overall_progress']);
                }
            }

            // If completing the service, set status and completion time
            if ($action === 'complete') {
                $updateData['status'] = 'completed';
                $updateData['completed_at'] = date('Y-m-d H:i:s');
            }

            // Update the service
            $result = $php_update($booking_details_table, $updateData, ['bookingdetailsid' => $bookingDetailId]);

            if (isset($result['error'])) {
                return ['status' => 'error', 'message' => 'Failed to update service: ' . $result['error']];
            }

            // Check if all services in this booking are completed
            $allCompleted = false;
            if ($action === 'complete') {
                // Get booking ID from the service detail
                $serviceDetail = $php_fetch($booking_details_table, 'booking_id', ['bookingdetailsid' => $bookingDetailId]);
                if (!empty($serviceDetail)) {
                    $bookingId = $serviceDetail[0]['booking_id'];

                    // Check if all services are completed
                    $allServices = $php_fetch($booking_details_table, 'status', ['booking_id' => $bookingId]);
                    $pendingCount = 0;
                    foreach ($allServices as $service) {
                        if (($service['status'] ?? 'pending') === 'pending') {
                            $pendingCount++;
                        }
                    }

                    if ($pendingCount === 0) {
                        // All services completed - move booking to history
                        $php_update($bookings_table, [
                            'booking_status' => 'completed',
                            'updated_at' => date('Y-m-d H:i:s')
                        ], ['bookingid' => $bookingId]);

                        $allCompleted = true;
                    }
                }
            }

            $message = $action === 'complete' ? 'Service completed successfully!' : 'Service progress updated successfully!';

            return [
                'status' => 'success',
                'message' => $message,
                'all_completed' => $allCompleted
            ];
        } catch (Exception $e) {
            error_log("Error in updateServiceCompletion: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function getUserProgressData($php_fetch, $bookings_table, $booking_details_table, $services_table, $userId)
    {
        try {
            // Get completed services - using only columns that exist in the database
            $query = "
                SELECT 
                    bd.bookingdetailsid,
                    bd.service_id,
                    bd.status,
                    bd.therapist_id,
                    bd.booking_id,
                    b.date_created,
                    s.service_name,
                    s.description as service_description
                FROM 
                    $booking_details_table bd
                LEFT JOIN 
                    $services_table s ON bd.service_id = s.id
                INNER JOIN 
                    $bookings_table b ON bd.booking_id = b.bookingid
                WHERE 
                    b.user_id = $userId 
                    AND bd.status = 'completed'
                ORDER BY 
                    b.date_created DESC
            ";

            $completedServices = $php_fetch('', '', [], $query);

            if (empty($completedServices)) {
                return ['status' => 'no_data', 'message' => 'No completed services found'];
            }

            // Check if user has stroke treatment services
            $hasStrokeTreatment = false;
            $therapistNotes = [];

            foreach ($completedServices as $service) {
                $isStroke = stripos($service['service_name'], 'stroke') !== false ||
                    stripos($service['service_name'], 'special treatment for stroke') !== false;

                if ($isStroke) {
                    $hasStrokeTreatment = true;
                }

                // Create a basic note entry (since therapist_notes column doesn't exist yet)
                $therapistNotes[] = [
                    'service_name' => $service['service_name'],
                    'therapist_notes' => 'Session completed successfully. Detailed notes feature coming soon.',
                    'pain_level' => null,
                    'mobility_level' => null,
                    'overall_progress' => null,
                    'completed_at' => $service['date_created'],
                    'updated_at' => $service['date_created']
                ];
            }

            // For stroke treatments, provide sample progress data
            $latestStrokeProgress = null;
            if ($hasStrokeTreatment) {
                $latestStrokeProgress = [
                    'pain_level' => null, // Will be implemented when columns are added
                    'mobility_level' => null,
                    'overall_progress' => null,
                    'last_updated' => $completedServices[0]['date_created'] ?? null
                ];
            }

            return [
                'status' => 'success',
                'data' => [
                    'sessions' => count($completedServices),
                    'hasStrokeTreatment' => $hasStrokeTreatment,
                    'latestProgress' => $latestStrokeProgress,
                    'therapistNotes' => $therapistNotes
                ]
            ];
        } catch (Exception $e) {
            error_log("Error in getUserProgressData: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    //! ============================== OPTIONAL ENHANCED METHODS ==============================

    public function getBookingsWithDetails($conn, $user_id)
    {
        $sql = "
            SELECT 
                b.bookingid,
                b.total_price,
                b.booking_status,
                b.date_created,
                bd.service_id,
                bd.quantity,
                bd.price,
                s.service_name
            FROM booking b
            LEFT JOIN booking_details bd ON b.bookingid = bd.booking_id
            LEFT JOIN services s ON bd.service_id = s.service_id
            WHERE b.user_id = ?
            ORDER BY b.date_created DESC
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get booked time slots for a specific date
     */
    public function getBookedTimes($php_fetch, $date)
    {
        try {
            global $connection;

            // Get all booked time slots for the given date
            // Only include confirmed/pending bookings (not cancelled/declined)
            $query = "
                SELECT DISTINCT TIME(bd.booking_time) as booked_time,
                       COUNT(*) as bookings_count
                FROM booking_details bd
                INNER JOIN booking b ON bd.booking_id = b.bookingid
                WHERE DATE(bd.booking_date) = ?
                AND b.booking_status NOT IN ('Cancelled', 'Declined')
                GROUP BY TIME(bd.booking_time)
                HAVING COUNT(*) >= (
                    SELECT COUNT(*) FROM therapist WHERE active = 1
                )
                ORDER BY booked_time
            ";

            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $date);
            $stmt->execute();
            $result = $stmt->get_result();

            $booked_times = [];
            while ($row = $result->fetch_assoc()) {
                $booked_times[] = $row['booked_time'];
            }

            return json_encode($booked_times);
        } catch (Exception $e) {
            error_log("Error in getBookedTimes: " . $e->getMessage());
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
