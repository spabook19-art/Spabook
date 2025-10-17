<?php
require_once '../model/sales_report_model.php';
require_once '../config/connection.php';
require_once '../utils/cache.php';
date_default_timezone_set('Asia/Manila');

header('Content-Type: application/json');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Initialize cache
Cache::init();

// Initialize model
$SalesReportModel = new SalesReportModel();

// Commission rate (₱50 per hour)
define('COMMISSION_RATE', 50.0);

/**
 * Send JSON response and exit
 * 
 * @param array $data Response data
 */
function response($data) {
    echo json_encode($data);
    exit;
}

/**
 * Format currency to Philippine Peso
 * 
 * @param float $amount Amount to format
 * @return string Formatted currency string
 */
function formatPeso($amount) {
    return '₱' . number_format((float)$amount, 2);
}

/**
 * Format datetime to readable format
 * 
 * @param string $datetime Datetime string
 * @return string Formatted datetime
 */
function formatDateTime($datetime) {
    if (!$datetime) return 'N/A';
    return date('Y-m-d H:i', strtotime($datetime));
}

/**
 * Format date only
 * 
 * @param string $datetime Datetime string
 * @return string Formatted date
 */
function formatDate($datetime) {
    if (!$datetime) return 'N/A';
    return date('F j, Y', strtotime($datetime));
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    
    if (!$action) {
        response(['status' => 'error', 'message' => 'No action specified']);
    }
    
    switch ($action) {
        case 'get_sales_data':
            try {
                // Get sales report from model
                $result = $SalesReportModel->getSalesReport($php_raw_sql);
                
                if ($result['status'] === 'error') {
                    response(['status' => 'error', 'message' => $result['message']]);
                }
                
                if ($result['status'] === 'nodata') {
                    response([
                        'status' => 'success',
                        'total_sales' => 0,
                        'total_bookings' => 0,
                        'net_revenue' => 0,
                        'sales' => []
                    ]);
                }
                
                $sales = $result['sales'];
                
                // Calculate totals
                $totalSales = 0;
                $bookingIds = [];
                
                foreach ($sales as &$sale) {
                    // Calculate total amount for this line item
                    $totalAmount = (float)$sale['quantity'] * (float)$sale['price'];
                    $sale['total_amount'] = $totalAmount;
                    $totalSales += $totalAmount;
                    
                    // Track unique booking IDs
                    if (!in_array($sale['booking_id'], $bookingIds)) {
                        $bookingIds[] = $sale['booking_id'];
                    }
                    
                    // Format dates
                    $sale['date_created_formatted'] = formatDateTime($sale['date_created']);
                    $sale['schedule_start_formatted'] = formatDateTime($sale['schedule_start']);
                    $sale['schedule_end_formatted'] = formatDateTime($sale['schedule_end']);
                    
                    // Format payment status
                    if ($sale['payment_status'] === true || $sale['payment_status'] === 't' || $sale['payment_status'] === '1') {
                        $sale['payment_status_text'] = 'Paid';
                    } else {
                        $sale['payment_status_text'] = 'Unpaid';
                    }
                }
                
                $totalBookings = count($bookingIds);
                
                // Get commission summary to calculate net revenue
                $commissionSummary = $SalesReportModel->getCommissionSummary($php_raw_sql, COMMISSION_RATE);
                $totalCommission = 0;
                if ($commissionSummary['status'] === 'success') {
                    $totalCommission = (float)$commissionSummary['summary']['total_commission'];
                }
                
                $netRevenue = $totalSales - $totalCommission;
                
                response([
                    'status' => 'success',
                    'total_sales' => (float)$totalSales,
                    'total_bookings' => $totalBookings,
                    'total_commission' => (float)$totalCommission,
                    'net_revenue' => (float)$netRevenue,
                    'sales' => $sales
                ]);
            } catch (Exception $e) {
                error_log("Error in get_sales_data: " . $e->getMessage());
                response(['status' => 'error', 'message' => $e->getMessage()]);
            }
            break;
            
        case 'get_commission_data':
            try {
                // Get commission report from model
                $result = $SalesReportModel->getCommissionReport($php_raw_sql);
                
                if ($result['status'] === 'error') {
                    response(['status' => 'error', 'message' => $result['message']]);
                }
                
                if ($result['status'] === 'nodata') {
                    response([
                        'status' => 'success',
                        'total_commission' => 0,
                        'total_hours' => 0,
                        'commission_rate' => COMMISSION_RATE,
                        'commissions' => []
                    ]);
                }
                
                $commissions = $result['commissions'];
                
                // Calculate totals
                $totalCommission = 0;
                $totalHours = 0;
                
                foreach ($commissions as &$comm) {
                    // Get hours logged
                    $hoursLogged = isset($comm['hours_logged']) ? (float)$comm['hours_logged'] : 1.0;
                    $comm['hours_logged'] = round($hoursLogged, 2);
                    
                    // Calculate commission amount (hours * rate)
                    $comm['commission_amount'] = $hoursLogged * COMMISSION_RATE;
                    
                    $totalHours += $hoursLogged;
                    $totalCommission += $comm['commission_amount'];
                    
                    // Format dates
                    $comm['date'] = formatDate($comm['schedule_start']);
                    $comm['date_time'] = formatDateTime($comm['schedule_start']);
                    $comm['schedule_start_formatted'] = formatDateTime($comm['schedule_start']);
                    $comm['schedule_end_formatted'] = formatDateTime($comm['schedule_end']);
                }
                
                response([
                    'status' => 'success',
                    'total_commission' => (float)$totalCommission,
                    'total_hours' => round((float)$totalHours, 2),
                    'commission_rate' => COMMISSION_RATE,
                    'commissions' => $commissions
                ]);
            } catch (Exception $e) {
                error_log("Error in get_commission_data: " . $e->getMessage());
                response(['status' => 'error', 'message' => $e->getMessage()]);
            }
            break;
            
        case 'get_sales_summary':
            try {
                $result = $SalesReportModel->getSalesSummary($php_raw_sql);
                
                if ($result['status'] === 'error') {
                    response(['status' => 'error', 'message' => $result['message']]);
                }
                
                $summary = $result['summary'];
                
                // Format numbers
                $summary['total_sales'] = (float)$summary['total_sales'];
                $summary['average_sale'] = (float)$summary['average_sale'];
                
                response(['status' => 'success', 'summary' => $summary]);
            } catch (Exception $e) {
                error_log("Error in get_sales_summary: " . $e->getMessage());
                response(['status' => 'error', 'message' => $e->getMessage()]);
            }
            break;
            
        case 'get_commission_summary':
            try {
                $result = $SalesReportModel->getCommissionSummary($php_raw_sql, COMMISSION_RATE);
                
                if ($result['status'] === 'error') {
                    response(['status' => 'error', 'message' => $result['message']]);
                }
                
                $summary = $result['summary'];
                
                // Format numbers
                $summary['total_hours'] = round((float)$summary['total_hours'], 2);
                $summary['total_commission'] = (float)$summary['total_commission'];
                
                response(['status' => 'success', 'summary' => $summary]);
            } catch (Exception $e) {
                error_log("Error in get_commission_summary: " . $e->getMessage());
                response(['status' => 'error', 'message' => $e->getMessage()]);
            }
            break;
            
        case 'get_therapist_performance':
            try {
                $result = $SalesReportModel->getTherapistPerformance($php_raw_sql, COMMISSION_RATE);
                
                if ($result['status'] === 'error') {
                    response(['status' => 'error', 'message' => $result['message']]);
                }
                
                if ($result['status'] === 'nodata') {
                    response([
                        'status' => 'success',
                        'therapists' => []
                    ]);
                }
                
                $therapists = $result['therapists'];
                
                // Format numbers
                foreach ($therapists as &$therapist) {
                    $therapist['total_sales'] = (float)$therapist['total_sales'];
                    $therapist['total_hours'] = round((float)$therapist['total_hours'], 2);
                    $therapist['total_commission'] = (float)$therapist['total_commission'];
                }
                
                response(['status' => 'success', 'therapists' => $therapists]);
            } catch (Exception $e) {
                error_log("Error in get_therapist_performance: " . $e->getMessage());
                response(['status' => 'error', 'message' => $e->getMessage()]);
            }
            break;
            
        case 'get_complete_report':
            try {
                // Get all data at once
                $salesResult = $SalesReportModel->getSalesReport($php_raw_sql);
                $commissionResult = $SalesReportModel->getCommissionReport($php_raw_sql);
                $salesSummary = $SalesReportModel->getSalesSummary($php_raw_sql);
                $commissionSummary = $SalesReportModel->getCommissionSummary($php_raw_sql, COMMISSION_RATE);
                $therapistPerformance = $SalesReportModel->getTherapistPerformance($php_raw_sql, COMMISSION_RATE);
                
                response([
                    'status' => 'success',
                    'sales' => $salesResult,
                    'commissions' => $commissionResult,
                    'sales_summary' => $salesSummary,
                    'commission_summary' => $commissionSummary,
                    'therapist_performance' => $therapistPerformance,
                    'commission_rate' => COMMISSION_RATE
                ]);
            } catch (Exception $e) {
                error_log("Error in get_complete_report: " . $e->getMessage());
                response(['status' => 'error', 'message' => $e->getMessage()]);
            }
            break;
            
        default:
            response(['status' => 'error', 'message' => 'Unknown action']);
    }
} else {
    response(['status' => 'error', 'message' => 'Invalid request method. Use POST.']);
}
?>