<?php
// Therapist Controller
if (isset($_POST['action'])) {
    date_default_timezone_set('Asia/Manila');
    require_once '../config/connection.php';
    
    $action = trim($_POST['action']);
    
    switch ($action) {
        case 'get_therapists_by_service':
            // Get all therapists from users table where role is 'Therapist'
            // The service_id parameter is available but we'll return all therapists for simplicity
            $service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : null;
            
            try {
                // Fetch all users with role 'Therapist' from the users table
                $therapists_data = $php_fetch('users', '*', ['role' => 'Therapist', 'order' => 'full_name.asc']);
                
                if (!empty($therapists_data) && !isset($therapists_data['error'])) {
                    // Map the users table fields to match the expected format in the frontend
                    $formatted_therapists = [];
                    
                    // Handle both single result and array of results
                    $therapists_array = isset($therapists_data['id']) ? [$therapists_data] : $therapists_data;
                    
                    foreach ($therapists_array as $therapist) {
                        $formatted_therapists[] = [
                            'therapistid' => $therapist['user_id'] ?? $therapist['id'],
                            'therapist_name' => $therapist['full_name'],
                            'therapist_desc' => $therapist['bio'] ?? 'Professional therapist',
                            'contact_number' => $therapist['contact_number'] ?? '',
                            'email' => $therapist['email'] ?? '',
                            'is_active' => $therapist['is_active'] ?? true
                        ];
                    }
                    
                    // Return formatted therapists data
                    echo json_encode($formatted_therapists);
                } else {
                    // No therapists found
                    echo json_encode('nodata');
                }
            } catch (Exception $e) {
                error_log('Error fetching therapists: ' . $e->getMessage());
                echo json_encode(['error' => 'Failed to fetch therapists: ' . $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
}