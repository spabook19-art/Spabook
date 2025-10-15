<?php

class SalesReportModel
{
    /**
     * Get all sales data from completed bookings
     * Fetches all data from booking_details table where status is 'Completed'
     * 
     * @param callable $php_fetch Database fetch function
     * @return array Sales data with all booking details information
     */
    public function getSalesReport($php_fetch)
    {
        try {
            // Get all completed booking details with all required fields
            $query = "SELECT 
                        bd.bookingdetailsid,
                        bd.booking_id,
                        bd.service_id,
                        bd.quantity,
                        bd.price,
                        bd.therapist_id,
                        bd.schedule_start,
                        bd.schedule_end,
                        bd.status,
                        bd.patient_name,
                        bd.patient_age,
                        bd.patient_gender,
                        bd.patient_notes,
                        b.bookingid,
                        b.user_id,
                        b.total_price as booking_total_price,
                        b.payment_status,
                        b.payment_img,
                        b.booking_status,
                        b.date_created,
                        CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                        u.email as customer_email,
                        u.contact_number as customer_phone,
                        s.service_name,
                        s.duration as service_duration,
                        CASE 
                            WHEN bd.therapist_id IS NOT NULL THEN CONCAT(t.first_name, ' ', t.last_name)
                            ELSE NULL
                        END as therapist_name,
                        (bd.quantity * bd.price) as total_amount
                      FROM booking_details bd
                      INNER JOIN booking b ON b.bookingid = bd.booking_id
                      INNER JOIN users u ON u.user_id = b.user_id
                      INNER JOIN services s ON s.id = bd.service_id
                      LEFT JOIN users t ON t.user_id = bd.therapist_id AND t.role = 'Therapist'
                      WHERE bd.status = 'Completed'
                        AND bd.service_id IS NOT NULL
                        AND bd.quantity IS NOT NULL
                        AND bd.price IS NOT NULL
                      ORDER BY b.date_created DESC, bd.bookingdetailsid ASC";
            
            $result = $php_fetch($query);
            
            if (!$result || count($result) === 0) {
                return ['status' => 'nodata', 'sales' => []];
            }
            
            return ['status' => 'success', 'sales' => $result];
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
    public function getCommissionReport($php_fetch)
    {
        try {
            // Get all completed booking details with assigned therapists
            $query = "SELECT 
                        bd.bookingdetailsid,
                        bd.booking_id,
                        bd.service_id,
                        bd.quantity,
                        bd.price,
                        bd.therapist_id,
                        bd.schedule_start,
                        bd.schedule_end,
                        bd.status,
                        bd.patient_name,
                        bd.patient_age,
                        bd.patient_gender,
                        bd.patient_notes,
                        b.bookingid,
                        b.date_created,
                        CONCAT(t.first_name, ' ', t.last_name) as therapist_name,
                        t.email as therapist_email,
                        t.contact_number as therapist_phone,
                        s.service_name,
                        s.duration as service_duration,
                        CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                        -- Calculate hours logged from schedule_start to schedule_end
                        CASE 
                            WHEN bd.schedule_start IS NOT NULL AND bd.schedule_end IS NOT NULL 
                            THEN EXTRACT(EPOCH FROM (bd.schedule_end - bd.schedule_start))/3600
                            ELSE 1.0
                        END as hours_logged,
                        (bd.quantity * bd.price) as service_total
                      FROM booking_details bd
                      INNER JOIN booking b ON b.bookingid = bd.booking_id
                      INNER JOIN users u ON u.user_id = b.user_id
                      INNER JOIN services s ON s.id = bd.service_id
                      INNER JOIN users t ON t.user_id = bd.therapist_id
                      WHERE bd.status = 'Completed'
                        AND bd.therapist_id IS NOT NULL
                        AND bd.service_id IS NOT NULL
                        AND t.role = 'Therapist'
                      ORDER BY bd.schedule_start DESC, t.first_name ASC";
            
            $result = $php_fetch($query);
            
            if (!$result || count($result) === 0) {
                return ['status' => 'nodata', 'commissions' => []];
            }
            
            return ['status' => 'success', 'commissions' => $result];
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
    public function getSalesSummary($php_fetch)
    {
        try {
            $query = "SELECT 
                        COUNT(DISTINCT bd.booking_id) as total_bookings,
                        COUNT(bd.bookingdetailsid) as total_services,
                        COALESCE(SUM(bd.quantity * bd.price), 0) as total_sales,
                        COALESCE(AVG(bd.quantity * bd.price), 0) as average_sale
                      FROM booking_details bd
                      WHERE bd.status = 'Completed'
                        AND bd.service_id IS NOT NULL
                        AND bd.quantity IS NOT NULL
                        AND bd.price IS NOT NULL";
            
            $result = $php_fetch($query);
            
            if ($result && count($result) > 0) {
                return ['status' => 'success', 'summary' => $result[0]];
            }
            
            return ['status' => 'success', 'summary' => [
                'total_bookings' => 0,
                'total_services' => 0,
                'total_sales' => 0,
                'average_sale' => 0
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
    public function getCommissionSummary($php_fetch, $commissionRate = 50.0)
    {
        try {
            $query = "SELECT 
                        COUNT(DISTINCT bd.therapist_id) as total_therapists,
                        COUNT(bd.bookingdetailsid) as total_services,
                        COALESCE(SUM(
                            CASE 
                                WHEN bd.schedule_start IS NOT NULL AND bd.schedule_end IS NOT NULL 
                                THEN EXTRACT(EPOCH FROM (bd.schedule_end - bd.schedule_start))/3600
                                ELSE 1.0
                            END
                        ), 0) as total_hours
                      FROM booking_details bd
                      INNER JOIN users t ON t.user_id = bd.therapist_id
                      WHERE bd.status = 'Completed'
                        AND bd.therapist_id IS NOT NULL
                        AND t.role = 'Therapist'";
            
            $result = $php_fetch($query);
            
            if ($result && count($result) > 0) {
                $summary = $result[0];
                $summary['total_commission'] = (float)$summary['total_hours'] * $commissionRate;
                $summary['commission_rate'] = $commissionRate;
                return ['status' => 'success', 'summary' => $summary];
            }
            
            return ['status' => 'success', 'summary' => [
                'total_therapists' => 0,
                'total_services' => 0,
                'total_hours' => 0,
                'total_commission' => 0,
                'commission_rate' => $commissionRate
            ]];
        } catch (Exception $e) {
            error_log("Error in getCommissionSummary: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get sales and commission data grouped by therapist
     * 
     * @param callable $php_fetch Database fetch function
     * @param float $commissionRate Commission rate per hour (default: 50)
     * @return array Therapist performance data
     */
    public function getTherapistPerformance($php_fetch, $commissionRate = 50.0)
    {
        try {
            $query = "SELECT 
                        bd.therapist_id,
                        CONCAT(t.first_name, ' ', t.last_name) as therapist_name,
                        COUNT(bd.bookingdetailsid) as total_services,
                        COALESCE(SUM(bd.quantity * bd.price), 0) as total_sales,
                        COALESCE(SUM(
                            CASE 
                                WHEN bd.schedule_start IS NOT NULL AND bd.schedule_end IS NOT NULL 
                                THEN EXTRACT(EPOCH FROM (bd.schedule_end - bd.schedule_start))/3600
                                ELSE 1.0
                            END
                        ), 0) as total_hours
                      FROM booking_details bd
                      INNER JOIN users t ON t.user_id = bd.therapist_id
                      WHERE bd.status = 'Completed'
                        AND bd.therapist_id IS NOT NULL
                        AND t.role = 'Therapist'
                      GROUP BY bd.therapist_id, t.first_name, t.last_name
                      ORDER BY total_sales DESC";
            
            $result = $php_fetch($query);
            
            if ($result && count($result) > 0) {
                // Calculate commission for each therapist
                foreach ($result as &$therapist) {
                    $therapist['total_commission'] = (float)$therapist['total_hours'] * $commissionRate;
                    $therapist['commission_rate'] = $commissionRate;
                }
                return ['status' => 'success', 'therapists' => $result];
            }
            
            return ['status' => 'nodata', 'therapists' => []];
        } catch (Exception $e) {
            error_log("Error in getTherapistPerformance: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage(), 'therapists' => []];
        }
    }
}