<?php

class SalesReportModel
{
    private function formatDate($datetime) {
        if (!$datetime) return 'N/A';
        try {
            return date('M d, Y', strtotime($datetime));
        } catch (Exception $e) {
            return 'N/A';
        }
    }
    
    private function formatDateTime($datetime) {
        if (!$datetime) return 'N/A';
        try {
            return date('M d, Y H:i', strtotime($datetime));
        } catch (Exception $e) {
            return 'N/A';
        }
    }
    /**
     * Get all sales data from completed bookings
     * Follows booking model pattern: fetch main records, loop through, fetch related data per record
     * 
     * @param callable $php_fetch Database fetch function
     * @return array Sales data with all booking details information
     */
    public function getSalesReport($php_raw_sql)
    {
        try {
            global $php_fetch;
            
            $booking_details = $php_fetch('booking_details', '*', ['status' => 'Completed']);
            
            if (!$booking_details || isset($booking_details['error']) || count($booking_details) === 0) {
                return ['status' => 'nodata', 'sales' => []];
            }
            
            $sales = [];
            
            foreach ($booking_details as $detail) {
                try {
                    $booking = $php_fetch('booking', '*', ['bookingid' => $detail['booking_id']]);
                    if (!$booking || isset($booking['error']) || count($booking) === 0) {
                        continue;
                    }
                    
                    $user = null;
                    if (isset($booking[0]['user_id']) && !empty($booking[0]['user_id'])) {
                        $userResult = $php_fetch('users', '*', ['user_id' => $booking[0]['user_id']]);
                        if ($userResult && !isset($userResult['error']) && is_array($userResult) && count($userResult) > 0) {
                            $user = $userResult;
                        }
                    }
                    
                    $service = null;
                    if (isset($detail['service_id']) && !empty($detail['service_id'])) {
                        $serviceResult = $php_fetch('services', '*', ['id' => $detail['service_id']]);
                        if ($serviceResult && !isset($serviceResult['error']) && is_array($serviceResult) && count($serviceResult) > 0) {
                            $service = $serviceResult;
                        }
                    }
                    
                    $therapist = null;
                    if (isset($detail['therapist_id']) && !empty($detail['therapist_id'])) {
                        $therapistResult = $php_fetch('users', '*', ['user_id' => $detail['therapist_id']]);
                        if ($therapistResult && !isset($therapistResult['error']) && is_array($therapistResult) && count($therapistResult) > 0) {
                            $therapist = $therapistResult;
                        }
                    }
                    
                    if (!$detail['quantity'] || !$detail['price']) {
                        continue;
                    }
                    
                    $sales[] = [
                        'bookingdetailsid' => $detail['bookingdetailsid'],
                        'booking_id' => $detail['booking_id'],
                        'service_id' => $detail['service_id'],
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                        'therapist_id' => $detail['therapist_id'],
                        'schedule_start' => $detail['schedule_start'],
                        'schedule_end' => $detail['schedule_end'],
                        'schedule_start_formatted' => $this->formatDateTime($detail['schedule_start']),
                        'schedule_end_formatted' => $this->formatDateTime($detail['schedule_end']),
                        'status' => $detail['status'],
                        'patient_name' => $detail['patient_name'],
                        'patient_age' => $detail['patient_age'],
                        'patient_gender' => $detail['patient_gender'],
                        'patient_notes' => $detail['patient_notes'],
                        'bookingid' => $booking[0]['bookingid'] ?? null,
                        'user_id' => $booking[0]['user_id'] ?? null,
                        'booking_total_price' => $booking[0]['total_price'] ?? 0,
                        'payment_status' => $booking[0]['payment_status'] ?? false,
                        'payment_img' => $booking[0]['payment_img'] ?? null,
                        'booking_status' => $booking[0]['booking_status'] ?? null,
                        'date_created' => $booking[0]['date_created'] ?? null,
                        'date_created_formatted' => $this->formatDate($booking[0]['date_created'] ?? null),
                        'customer_name' => ($user && count($user) > 0 ? $user[0]['full_name'] ?? ($user[0]['first_name'] . ' ' . $user[0]['last_name']) : 'N/A'),
                        'customer_email' => ($user && count($user) > 0 ? $user[0]['email'] : 'N/A'),
                        'customer_phone' => ($user && count($user) > 0 ? $user[0]['contact_number'] : 'N/A'),
                        'service_name' => ($service && count($service) > 0 ? $service[0]['service_name'] : 'N/A'),
                        'service_duration' => ($service && count($service) > 0 ? $service[0]['duration'] : 0),
                        'therapist_name' => ($therapist && count($therapist) > 0 ? $therapist[0]['full_name'] ?? ($therapist[0]['first_name'] . ' ' . $therapist[0]['last_name']) : 'N/A'),
                        'total_amount' => $detail['quantity'] * $detail['price']
                    ];
                } catch (Exception $e) {
                    error_log("Error processing sales detail {$detail['bookingdetailsid']}: " . $e->getMessage());
                    continue;
                }
            }
            
            usort($sales, function($a, $b) {
                $dateA = strtotime($a['date_created'] ?? 0);
                $dateB = strtotime($b['date_created'] ?? 0);
                return $dateB - $dateA;
            });
            
            if (!$sales) {
                return ['status' => 'nodata', 'sales' => []];
            }
            
            return ['status' => 'success', 'sales' => $sales];
        } catch (Exception $e) {
            error_log("Error in getSalesReport: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage(), 'sales' => []];
        }
    }

    /**
     * Get commission report for therapists
     * Only includes completed services with assigned therapists
     * 
     * @param callable $php_fetch Database fetch function
     * @return array Commission data with therapist information
     */
    public function getCommissionReport($php_raw_sql)
    {
        try {
            global $php_fetch;
            
            $booking_details = $php_fetch('booking_details', '*', ['status' => 'Completed']);
            
            if (!$booking_details || isset($booking_details['error']) || count($booking_details) === 0) {
                return ['status' => 'nodata', 'commissions' => []];
            }
            
            $commissions = [];
            
            foreach ($booking_details as $detail) {
                try {
                    if (!isset($detail['therapist_id']) || empty($detail['therapist_id'])) {
                        continue;
                    }
                    
                    if (!isset($detail['service_id']) || empty($detail['service_id'])) {
                        continue;
                    }
                    
                    $booking = $php_fetch('booking', '*', ['bookingid' => $detail['booking_id']]);
                    if (!$booking || isset($booking['error']) || count($booking) === 0) {
                        continue;
                    }
                    
                    $therapistResult = $php_fetch('users', '*', ['user_id' => $detail['therapist_id']]);
                    if (!$therapistResult || isset($therapistResult['error']) || count($therapistResult) === 0) {
                        continue;
                    }
                    $therapist = $therapistResult;
                    
                    $customer = null;
                    if (isset($booking[0]['user_id']) && !empty($booking[0]['user_id'])) {
                        $customerResult = $php_fetch('users', '*', ['user_id' => $booking[0]['user_id']]);
                        if ($customerResult && !isset($customerResult['error']) && is_array($customerResult) && count($customerResult) > 0) {
                            $customer = $customerResult;
                        }
                    }
                    
                    $service = null;
                    if (isset($detail['service_id']) && !empty($detail['service_id'])) {
                        $serviceResult = $php_fetch('services', '*', ['id' => $detail['service_id']]);
                        if ($serviceResult && !isset($serviceResult['error']) && is_array($serviceResult) && count($serviceResult) > 0) {
                            $service = $serviceResult;
                        }
                    }
                    
                    $hoursLogged = 1.0;
                    if ($detail['schedule_start'] && $detail['schedule_end']) {
                        try {
                            $start = new \DateTime($detail['schedule_start']);
                            $end = new \DateTime($detail['schedule_end']);
                            $interval = $end->diff($start);
                            $hoursLogged = ($interval->h + ($interval->i / 60) + ($interval->s / 3600));
                        } catch (Exception $e) {
                            $hoursLogged = 1.0;
                        }
                    }
                    
                    $commissions[] = [
                        'bookingdetailsid' => $detail['bookingdetailsid'],
                        'booking_id' => $detail['booking_id'],
                        'service_id' => $detail['service_id'],
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                        'therapist_id' => $detail['therapist_id'],
                        'schedule_start' => $detail['schedule_start'],
                        'schedule_end' => $detail['schedule_end'],
                        'schedule_start_formatted' => $this->formatDateTime($detail['schedule_start']),
                        'schedule_end_formatted' => $this->formatDateTime($detail['schedule_end']),
                        'status' => $detail['status'],
                        'patient_name' => $detail['patient_name'],
                        'patient_age' => $detail['patient_age'],
                        'patient_gender' => $detail['patient_gender'],
                        'patient_notes' => $detail['patient_notes'],
                        'bookingid' => $booking[0]['bookingid'] ?? null,
                        'date_created' => $booking[0]['date_created'] ?? null,
                        'date_formatted' => $this->formatDate($detail['schedule_start']),
                        'therapist_name' => $therapist[0]['full_name'] ?? ($therapist[0]['first_name'] . ' ' . $therapist[0]['last_name']),
                        'therapist_email' => $therapist[0]['email'] ?? 'N/A',
                        'therapist_phone' => $therapist[0]['contact_number'] ?? 'N/A',
                        'service_name' => ($service && count($service) > 0 ? $service[0]['service_name'] : 'N/A'),
                        'service_duration' => ($service && count($service) > 0 ? $service[0]['duration'] : 0),
                        'customer_name' => ($customer && count($customer) > 0 ? $customer[0]['full_name'] ?? ($customer[0]['first_name'] . ' ' . $customer[0]['last_name']) : 'N/A'),
                        'hours_logged' => $hoursLogged,
                        'service_total' => $detail['quantity'] * $detail['price']
                    ];
                } catch (Exception $e) {
                    error_log("Error processing commission detail {$detail['bookingdetailsid']}: " . $e->getMessage());
                    continue;
                }
            }
            
            if (!$commissions) {
                return ['status' => 'nodata', 'commissions' => []];
            }
            
            return ['status' => 'success', 'commissions' => $commissions];
        } catch (Exception $e) {
            error_log("Error in getCommissionReport: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage(), 'commissions' => []];
        }
    }

    /**
     * Get sales summary statistics
     * 
     * @param callable $php_fetch Database fetch function
     * @return array Summary statistics
     */
    public function getSalesSummary($php_raw_sql)
    {
        try {
            global $php_fetch;
            
            $booking_details = $php_fetch('booking_details', '*', ['status' => 'Completed']);
            
            if (!$booking_details || isset($booking_details['error']) || count($booking_details) === 0) {
                return ['status' => 'success', 'summary' => [
                    'total_bookings' => 0,
                    'total_services' => 0,
                    'total_sales' => 0,
                    'average_sale' => 0
                ]];
            }
            
            $bookingIds = [];
            $totalSales = 0;
            $services = 0;
            $amounts = [];
            
            foreach ($booking_details as $detail) {
                if (!$detail['service_id'] || !$detail['quantity'] || !$detail['price']) {
                    continue;
                }
                
                $amount = $detail['quantity'] * $detail['price'];
                $amounts[] = $amount;
                $totalSales += $amount;
                $services++;
                
                if (!in_array($detail['booking_id'], $bookingIds)) {
                    $bookingIds[] = $detail['booking_id'];
                }
            }
            
            $averageSale = count($amounts) > 0 ? array_sum($amounts) / count($amounts) : 0;
            
            return ['status' => 'success', 'summary' => [
                'total_bookings' => count($bookingIds),
                'total_services' => $services,
                'total_sales' => $totalSales,
                'average_sale' => $averageSale
            ]];
        } catch (Exception $e) {
            error_log("Error in getSalesSummary: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get commission summary statistics
     * 
     * @param callable $php_fetch Database fetch function
     * @param float $commissionRate Commission rate per hour (default: 50)
     * @return array Commission summary
     */
    public function getCommissionSummary($php_raw_sql, $commissionRate = 50.0)
    {
        try {
            global $php_fetch;
            
            $booking_details = $php_fetch('booking_details', '*', ['status' => 'Completed']);
            
            if (!$booking_details || isset($booking_details['error']) || count($booking_details) === 0) {
                return ['status' => 'success', 'summary' => [
                    'total_services' => 0,
                    'total_therapists' => 0,
                    'total_hours' => 0,
                    'total_commission' => 0,
                    'commission_rate' => $commissionRate
                ]];
            }
            
            $therapistIds = [];
            $totalHours = 0;
            $serviceCount = 0;
            
            foreach ($booking_details as $detail) {
                if (!isset($detail['therapist_id']) || empty($detail['therapist_id'])) {
                    continue;
                }
                
                $serviceCount++;
                
                if (!in_array($detail['therapist_id'], $therapistIds)) {
                    $therapistIds[] = $detail['therapist_id'];
                }
                
                if ($detail['schedule_start'] && $detail['schedule_end']) {
                    try {
                        $start = new \DateTime($detail['schedule_start']);
                        $end = new \DateTime($detail['schedule_end']);
                        $interval = $end->diff($start);
                        $hours = ($interval->h + ($interval->i / 60) + ($interval->s / 3600));
                        $totalHours += $hours;
                    } catch (Exception $e) {
                        $totalHours += 1.0;
                    }
                } else {
                    $totalHours += 1.0;
                }
            }
            
            $totalCommission = $totalHours * $commissionRate;
            
            return ['status' => 'success', 'summary' => [
                'total_services' => $serviceCount,
                'total_therapists' => count($therapistIds),
                'total_hours' => $totalHours,
                'total_commission' => $totalCommission,
                'commission_rate' => $commissionRate
            ]];
        } catch (Exception $e) {
            error_log("Error in getCommissionSummary: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get therapist performance statistics
     * 
     * @param callable $php_fetch Database fetch function
     * @param float $commissionRate Commission rate per hour
     * @return array Therapist performance data
     */
    public function getTherapistPerformance($php_raw_sql, $commissionRate = 50.0)
    {
        try {
            global $php_fetch;
            
            $booking_details = $php_fetch('booking_details', '*', ['status' => 'Completed']);
            
            if (!$booking_details || isset($booking_details['error']) || count($booking_details) === 0) {
                return ['status' => 'nodata', 'therapists' => []];
            }
            
            $therapists = [];
            
            foreach ($booking_details as $detail) {
                if (!isset($detail['therapist_id']) || empty($detail['therapist_id'])) {
                    continue;
                }
                
                $therapistId = $detail['therapist_id'];
                
                if (!isset($therapists[$therapistId])) {
                    $therapistUserResult = $php_fetch('users', '*', ['user_id' => $therapistId]);
                    if (!$therapistUserResult || isset($therapistUserResult['error']) || count($therapistUserResult) === 0) {
                        continue;
                    }
                    $therapistUser = $therapistUserResult;
                    
                    $therapists[$therapistId] = [
                        'therapist_id' => $therapistId,
                        'therapist_name' => $therapistUser[0]['full_name'] ?? ($therapistUser[0]['first_name'] . ' ' . $therapistUser[0]['last_name']),
                        'therapist_email' => $therapistUser[0]['email'] ?? null,
                        'total_services' => 0,
                        'total_sales' => 0,
                        'total_hours' => 0,
                        'total_commission' => 0
                    ];
                }
                
                $therapists[$therapistId]['total_services']++;
                $therapists[$therapistId]['total_sales'] += ($detail['quantity'] * $detail['price']);
                
                if ($detail['schedule_start'] && $detail['schedule_end']) {
                    try {
                        $start = new \DateTime($detail['schedule_start']);
                        $end = new \DateTime($detail['schedule_end']);
                        $interval = $end->diff($start);
                        $hours = ($interval->h + ($interval->i / 60) + ($interval->s / 3600));
                        $therapists[$therapistId]['total_hours'] += $hours;
                    } catch (Exception $e) {
                        $therapists[$therapistId]['total_hours'] += 1.0;
                    }
                } else {
                    $therapists[$therapistId]['total_hours'] += 1.0;
                }
            }
            
            foreach ($therapists as &$t) {
                $t['total_commission'] = $t['total_hours'] * $commissionRate;
            }
            
            $result = array_values($therapists);
            
            if (!$result) {
                return ['status' => 'nodata', 'therapists' => []];
            }
            
            return ['status' => 'success', 'therapists' => $result];
        } catch (Exception $e) {
            error_log("Error in getTherapistPerformance: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage(), 'therapists' => []];
        }
    }
}
?>
