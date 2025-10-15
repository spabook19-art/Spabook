<?php
// Ensure session is started for all user operations
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Run the test
if (isset($_POST['action'])) {
    date_default_timezone_set('Asia/Manila');
    require_once '../config/connection.php';
    require_once '../model/admin_dashboard_model.php';
    $Admin = new Admin();
    $action = trim($_POST['action']);
    $current_date = date('Y-m-d');
    $timestamp = new DateTime('now');
    $current_datetimestamp = $timestamp->format('Y-m-d H:i:s');
    switch ($action) {
        case 'get_dashboard_stats':
            echo $Admin->getDashboardStats($php_fetch);
            break;

        case 'get_total_bookings':
            echo $Admin->getTotalBookings($php_fetch, 'bookings');
            break;

        case 'get_appointment_history':
            echo $Admin->getAppointmentHistory($php_fetch);
            break;

        case 'get_recoverable_bookings':
            echo $Admin->getRecoverableBookings($php_fetch);
            break;

        case 'recover_booking':
            $bookingid = trim($_POST['bookingid']);
            echo $Admin->recoverBooking($php_update, $bookingid, $current_datetimestamp);
            break;

        case 'load_booking_requests':
            $status = trim($_POST['status']);
            echo $Admin->loadBookingRequests($php_fetch, $status);
            break;

        case 'get_booking_details':
            $bookingdetailsid = trim($_POST['bookingdetailsid']);
            echo $Admin->getBookingDetails($php_fetch, $bookingdetailsid);
            break;

        case 'decline_booking_request':
            $bookingdetailsid = trim($_POST['bookingdetailsid']);
            echo $Admin->declineBookingRequest($php_update, $bookingdetailsid, $current_datetimestamp);
            break;

        case 'accept_booking_request':
            $bookingdetailsid = trim($_POST['bookingdetailsid']);
            echo $Admin->acceptBookingRequest($php_update, $bookingdetailsid, $current_datetimestamp);
            break;

        case 'update_booking_status':
            $bookingdetailsid = trim($_POST['bookingdetailsid']);
            $new_status = trim($_POST['new_status']);
            echo $Admin->updateBookingStatus($php_update, $bookingdetailsid, $new_status, $current_datetimestamp);
        case 'reschedule_booking':
            $bookingdetailsid = trim($_POST['bookingdetailsid']);
            $schedule_start = trim($_POST['schedule_start']);
            $schedule_end = trim($_POST['schedule_end']);
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;
            echo $Admin->rescheduleBooking($php_update, $bookingdetailsid, $schedule_start, $schedule_end, $reason, $current_datetimestamp);
            break;
    }
}
