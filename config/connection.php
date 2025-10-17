<?php
// connection.php
require_once __DIR__ . '/credentials.php';

// Shared function to call Supabase API
function supabaseRequest($method, $endpoint, $data = null)
{
    global $baseUrl, $apiKey;
    $url = "$baseUrl/$endpoint";

    // Log the request details (disabled in production)
    // $logMessage = date('Y-m-d H:i:s') . " - Supabase Request: Method=$method, Endpoint=$endpoint\n";
    // file_put_contents(__DIR__ . '/../logs/debug.log', $logMessage, FILE_APPEND);

    $headers = [
        "apikey: $apiKey",
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ];

    if ($method === 'GET' && $data) {
        $url .= '?' . http_build_query($data);
    }

    if ($method === 'POST' || $method === 'PATCH') {
        $headers[] = "Prefer: return=representation";
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST' || $method === 'PATCH') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    if ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Debug logging (disabled in production)
    // file_put_contents('debug_curl.txt', "URL: $url\nHTTP Code: $httpCode\nCURL Error: $curlError\nResponse: $response\n\n", FILE_APPEND);

    // Log the response details (disabled in production)
    // $responseLogMessage = date('Y-m-d H:i:s') . " - Supabase Response: HTTP Code=$httpCode, Error=$curlError\n";
    // file_put_contents(__DIR__ . '/../logs/debug.log', $responseLogMessage, FILE_APPEND);

    if ($curlError) {
        $errorMessage = 'cURL Error: ' . $curlError;
        // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - $errorMessage\n", FILE_APPEND);
        return ['error' => $errorMessage];
    }

    if ($httpCode >= 400) {
        $errorMessage = 'HTTP Error: ' . $httpCode;
        // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - $errorMessage, Response: $response\n", FILE_APPEND);
        return ['error' => $errorMessage, 'response' => $response];
    }

    // Parse the JSON response
    $decodedResponse = json_decode($response, true);

    // Check if JSON parsing failed
    if ($response && $decodedResponse === null && json_last_error() !== JSON_ERROR_NONE) {
        $errorMessage = 'JSON Parse Error: ' . json_last_error_msg();
        // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - $errorMessage, Response: $response\n", FILE_APPEND);
        return ['error' => $errorMessage, 'raw_response' => $response];
    }

    // Return empty array if response is null or empty
    if ($decodedResponse === null) {
        // file_put_contents(__DIR__ . '/../logs/debug.log', date('Y-m-d H:i:s') . " - Empty response converted to empty array\n", FILE_APPEND);
        return [];
    }

    return $decodedResponse;
}

// Raw SQL query execution via Supabase REST API
$php_raw_sql = function ($query) {
    global $baseUrl, $apiKey;
    
    $query = trim($query);
    
    if (preg_match('/^SELECT/i', $query)) {
        $selectMatch = [];
        if (preg_match('/SELECT\s+(.*?)\s+FROM\s+(\w+)/i', $query, $selectMatch)) {
            $selectFields = $selectMatch[1];
            $table = $selectMatch[2];
            
            $url = "$baseUrl/$table?select=" . urlencode($selectFields);
            
            if (preg_match('/WHERE\s+(.*?)(?:ORDER|GROUP|LIMIT|$)/i', $query, $whereMatch)) {
                $whereClause = $whereMatch[1];
                
                if (preg_match('/LOWER\((\w+)\)\s*=\s*[\'"]?(\w+)[\'"]?/i', $whereClause, $lowerMatch)) {
                    $field = $lowerMatch[1];
                    $value = $lowerMatch[2];
                    $url .= "&$field=ilike." . urlencode("%$value%");
                }
            }
            
            $headers = [
                "apikey: $apiKey",
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json"
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                return ['error' => 'cURL Error: ' . $curlError];
            }
            
            if ($httpCode >= 400) {
                return ['error' => 'HTTP Error: ' . $httpCode];
            }
            
            $result = json_decode($response, true);
            return $result ?: [];
        }
    }
    
    return ['error' => 'Unsupported query format'];
};

// Fetch (GET)
$php_fetch = function ($table, $select = '*', $filters = [], $joins = []) {
    // Special case: UPDATE
    if ($select === 'UPDATE') {
        $query = [];
        foreach ($filters as $key => $value) {
            $query[] = "$key=eq.$value";
        }
        $endpoint = "$table?" . implode('&', $query);
        return supabaseRequest('PATCH', $endpoint, $filters);
    }

    // Special case: COUNT
    if (is_string($select) && stripos($select, 'COUNT') !== false) {
        $query = [];

        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                // IN query
                $query[$key] = 'in.(' . implode(',', $value) . ')';
            } elseif ($key !== null && is_string($key) && str_contains($key, '!=')) {
                $field = trim(str_replace('!=', '', $key));
                $query[$field] = "neq.$value";
            } elseif ($value !== null && is_string($value) && !str_contains($value, '.')) {
                $query[$key] = "eq.$value";
            } elseif ($value !== null && !is_string($value)) {
                $query[$key] = "eq.$value";
            } else {
                $query[$key] = $value;
            }
        }

        // Always pick primary key for counting
        // if ($table === 'booking_details') {
        //     $query['select'] = 'booking_id';
        // } else {
        //     $query['select'] = 'id';
        // }

        $result = supabaseRequest('GET', $table, $query);
        if (isset($result['error'])) {
            return $result;
        }
        return [['count' => count($result)]];
    }

    // Normal GET + Join support
    $query = ['select' => $select];

    // Add joins if provided
    if (!empty($joins)) {
        $joinParts = [];
        foreach ($joins as $joinTable => $joinFields) {
            if (is_array($joinFields)) {
                $joinParts[] = $joinTable . '(' . implode(',', $joinFields) . ')';
            } else {
                $joinParts[] = $joinTable . '(' . $joinFields . ')';
            }
        }
        $query['select'] .= ',' . implode(',', $joinParts);
    }

    // Apply filters
    foreach ($filters as $key => $value) {
        if (is_array($value)) {
            $query[$key] = 'in.(' . implode(',', $value) . ')';
        } elseif ($key !== null && is_string($key) && str_contains($key, '!=')) {
            $field = trim(str_replace('!=', '', $key));
            $query[$field] = "neq.$value";
        } elseif ($value !== null && is_string($value) && !str_contains($value, '.')) {
            $query[$key] = "eq.$value";
        } elseif ($value !== null && !is_string($value)) {
            $query[$key] = "eq.$value";
        } else {
            $query[$key] = $value;
        }
    }

    return supabaseRequest('GET', $table, $query);
};



// Insert (POST)
$php_insert = function ($table, $data) {
    $result = supabaseRequest('POST', $table, $data);
    // file_put_contents('debug_supabase_insert.txt', "Table: $table\nData: " . print_r($data, true) . "\nResult: " . print_r($result, true));
    return $result;
};

// Update (PATCH)
$php_update = function ($table, $data, $filters = []) {
    $query = [];
    foreach ($filters as $key => $value) {
        $query[] = "$key=eq.$value";
    }
    $endpoint = "$table?" . implode('&', $query);
    return supabaseRequest('PATCH', $endpoint, $data);
};

// Delete (DELETE)
$php_delete = function ($table, $filters = []) {
    // If filters is not an array (old format), convert it
    if (!is_array($filters)) {
        $filters = ['id' => $filters];
    }

    $query = [];
    foreach ($filters as $key => $value) {
        $query[] = "$key=eq.$value";
    }
    $endpoint = "$table?" . implode('&', $query);
    return supabaseRequest('DELETE', $endpoint);
};


function uploadProfileImage($base64Image, $uuid, $folder, $bucket = 'services-images')
{
    global $projectUrl, $serviceRoleKey;

    // Handle data URL format (data:image/png;base64,...)
    if (strpos($base64Image, 'data:') === 0) {
        // Split by comma to get just the base64 part
        $parts = explode(',', $base64Image, 2);
        if (count($parts) === 2) {
            $base64Image = $parts[1];
        }
    }

    // Decode base64 image
    $imageData = base64_decode($base64Image);
    if ($imageData === false) {
        return false;
    }

    // Detect MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);

    // Determine file extension
    $extension = match ($mimeType) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'bin',
    };

    $filename = "$folder/$uuid.$extension";

    // Step 1: Delete existing image if it exists
    $deleteUrl = "$projectUrl/storage/v1/object/$bucket/$filename";
    $deleteHeaders = [
        "Authorization: Bearer $serviceRoleKey"
    ];

    $deleteCh = curl_init($deleteUrl);
    curl_setopt_array($deleteCh, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => $deleteHeaders
    ]);

    curl_exec($deleteCh);
    curl_close($deleteCh);
    // (ignore delete errors; continue to upload)

    // Step 2: Upload new image
    $uploadUrl = "$projectUrl/storage/v1/object/$bucket/$filename";
    $uploadHeaders = [
        "Authorization: Bearer $serviceRoleKey",
        "Content-Type: $mimeType"
    ];

    $uploadCh = curl_init($uploadUrl);
    curl_setopt_array($uploadCh, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST', // POST: create new; PUT: overwrite
        CURLOPT_POSTFIELDS => $imageData,
        CURLOPT_HTTPHEADER => $uploadHeaders
    ]);

    $response = curl_exec($uploadCh);
    $httpCode = curl_getinfo($uploadCh, CURLINFO_HTTP_CODE);
    curl_close($uploadCh);

    if ($httpCode >= 200 && $httpCode < 300) {
        return "$projectUrl/storage/v1/object/public/$bucket/$filename";
    }

    return "../vendor/images/default_profile.png"; // Return default image on failure
}
