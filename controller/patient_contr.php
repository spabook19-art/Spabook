<?php
session_start();
require_once '../config/connection.php';

header('Content-Type: application/json');

// Get POST data
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'save_patient_info':
            savePatientInfo($php_insert, $php_fetch);
            break;
            
        case 'get_patient_info':
            getPatientInfo($php_fetch);
            break;
            
        case 'update_patient_info':
            updatePatientInfo($php_update);
            break;
            
        case 'get_patient_by_user':
            getPatientByUser($php_fetch);
            break;
            
        case 'delete_patient_info':
            deletePatientInfo($php_delete);
            break;
            
        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid action'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}

/**
 * Save patient information
 */
function savePatientInfo($php_insert, $php_fetch) {
    $user_id = $_POST['user_id'] ?? null;
    $full_name = $_POST['full_name'] ?? '';
    $age = $_POST['age'] ?? 0;
    $gender = $_POST['gender'] ?? '';
    $date_started = $_POST['date_started'] ?? date('Y-m-d');
    $notes = $_POST['notes'] ?? '';
    
    // Validate required fields
    if (empty($user_id) || empty($full_name) || empty($age) || empty($gender)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please fill in all required fields'
        ]);
        return;
    }
    
    try {
        // Check if patient already exists for this user
        $existingPatient = $php_fetch('patient', '*', ['user_id' => $user_id], []);
        
        if (!empty($existingPatient) && !isset($existingPatient['error'])) {
            // Update existing patient record (use the most recent one)
            $patientId = $existingPatient[0]['patient_id'];
            
            // For Supabase, we use php_update
            global $php_update;
            $updateData = [
                'full_name' => $full_name,
                'age' => intval($age),
                'gender' => $gender,
                'date_started' => $date_started
            ];
            
            $result = $php_update('patient', $updateData, ['patient_id' => $patientId]);
            
            if (!isset($result['error'])) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Patient information updated successfully',
                    'patient_id' => $patientId
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update patient information: ' . ($result['error'] ?? 'Unknown error')
                ]);
            }
        } else {
            // Insert new patient record
            $insertData = [
                'user_id' => $user_id,
                'full_name' => $full_name,
                'age' => intval($age),
                'gender' => $gender,
                'date_started' => $date_started,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $php_insert('patient', $insertData);
            
            if (!isset($result['error']) && !empty($result)) {
                $patientId = $result[0]['patient_id'] ?? null;
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Patient information saved successfully',
                    'patient_id' => $patientId
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to save patient information: ' . ($result['error'] ?? 'Unknown error')
                ]);
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get patient information by patient ID
 */
function getPatientInfo($php_fetch) {
    $patient_id = $_POST['patient_id'] ?? null;
    
    if (empty($patient_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Patient ID is required'
        ]);
        return;
    }
    
    try {
        $result = $php_fetch('patient', '*', ['patient_id' => $patient_id], []);
        
        if (!empty($result) && !isset($result['error'])) {
            echo json_encode([
                'status' => 'success',
                'data' => $result[0]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Patient not found'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Update patient information
 */
function updatePatientInfo($php_update) {
    $patient_id = $_POST['patient_id'] ?? null;
    $full_name = $_POST['full_name'] ?? '';
    $age = $_POST['age'] ?? 0;
    $gender = $_POST['gender'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $date_started = $_POST['date_started'] ?? null;
    $date_completed = $_POST['date_completed'] ?? null;
    
    if (empty($patient_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Patient ID is required'
        ]);
        return;
    }
    
    try {
        $updateData = [
            'full_name' => $full_name,
            'age' => intval($age),
            'gender' => $gender,
            'diagnosis' => $diagnosis,
            'date_started' => $date_started,
            'date_completed' => $date_completed
        ];
        
        $result = $php_update('patient', $updateData, ['patient_id' => $patient_id]);
        
        if (!isset($result['error'])) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Patient information updated successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update patient information: ' . ($result['error'] ?? 'Unknown error')
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get patient information by user ID
 */
function getPatientByUser($php_fetch) {
    $user_id = $_POST['user_id'] ?? null;
    
    if (empty($user_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User ID is required'
        ]);
        return;
    }
    
    try {
        $result = $php_fetch('patient', '*', ['user_id' => $user_id], []);
        
        if (!empty($result) && !isset($result['error'])) {
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
        } else {
            echo json_encode('nodata');
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Delete patient information
 */
function deletePatientInfo($php_delete) {
    $patient_id = $_POST['patient_id'] ?? null;
    
    if (empty($patient_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Patient ID is required'
        ]);
        return;
    }
    
    try {
        $result = $php_delete('patient', ['patient_id' => $patient_id]);
        
        if (!isset($result['error'])) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Patient information deleted successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to delete patient information: ' . ($result['error'] ?? 'Unknown error')
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}
?>