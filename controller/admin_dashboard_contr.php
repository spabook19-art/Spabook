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
    $User = new Admin();
    $action = trim($_POST['action']);
    $current_date = date('Y-m-d');
    $timestamp = new DateTime('now');
    $current_datetimestamp = $timestamp->format('Y-m-d H:i:s');
    switch ($action) {
        case 'get_dashboard_stats':
            echo $User->getDashboardStats($php_fetch, 'booking', 'users', 'therapists', 'services');
            break;

        case 'get_total_bookings':
            echo $User->getTotalBookings($php_fetch, 'bookings');
            break;

            case 'get_appointment_history':
                echo $User->getAppointmentHistory($php_fetch);
                break;
    }
}
