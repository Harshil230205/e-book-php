<?php
// Cloudinary Configuration
$CLOUDINARY_CLOUD_NAME = 'dwdn4ftes';
$CLOUDINARY_API_KEY    = '847294721912442';
$CLOUDINARY_API_SECRET = 'Gb5poslZ88q7DbHBEr7rJDuZ9Q4';

function uploadToCloudinary($file_path, $resource_type = 'image', $folder = '') {
    global $CLOUDINARY_CLOUD_NAME, $CLOUDINARY_API_KEY, $CLOUDINARY_API_SECRET;
    
    $timestamp = time();
    
    // Build parameters for signature (only include non-empty values)
    $params_to_sign = array();
    $params_to_sign['timestamp'] = $timestamp;
    
    // Only add folder if it's not empty
    if (!empty($folder)) {
        $params_to_sign['folder'] = $folder;
    }
    
    // Sort parameters alphabetically
    ksort($params_to_sign);
    
    // Build signature string
    $signature_params = array();
    foreach ($params_to_sign as $key => $value) {
        $signature_params[] = $key . '=' . $value;
    }
    $params_string = implode('&', $signature_params);
    
    // Generate signature
    $signature = sha1($params_string . $CLOUDINARY_API_SECRET);
    
    // Prepare upload data
    $upload_data = array(
        'file' => new CURLFile($file_path),
        'api_key' => $CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    );
    
    // Add folder only if provided
    if (!empty($folder)) {
        $upload_data['folder'] = $folder;
    }
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$CLOUDINARY_CLOUD_NAME}/{$resource_type}/upload");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $upload_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for testing
    
    // Execute upload
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Check for cURL errors
    if (curl_error($ch)) {
        $curl_error = curl_error($ch);
        curl_close($ch);
        return array(
            'success' => false,
            'error' => 'CURL Error: ' . $curl_error
        );
    }
    
    curl_close($ch);
    
    // Process response
    if ($http_code == 200) {
        $result = json_decode($response, true);
        
        if ($result) {
            return array(
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id']
            );
        } else {
            return array(
                'success' => false,
                'error' => 'Invalid JSON response: ' . $response
            );
        }
    } else {
        return array(
            'success' => false,
            'error' => 'Upload failed (HTTP ' . $http_code . '): ' . $response,
            'debug' => array(
                'params_string' => $params_string,
                'signature' => $signature,
                'http_code' => $http_code
            )
        );
    }
}

// Simple upload function without folder (backup option)
function uploadToCloudinarySimple($file_path, $resource_type = 'image') {
    global $CLOUDINARY_CLOUD_NAME, $CLOUDINARY_API_KEY, $CLOUDINARY_API_SECRET;
    
    $timestamp = time();
    
    // Minimal signature with just timestamp
    $params_string = 'timestamp=' . $timestamp;
    $signature = sha1($params_string . $CLOUDINARY_API_SECRET);
    
    // Upload data
    $upload_data = array(
        'file' => new CURLFile($file_path),
        'api_key' => $CLOUDINARY_API_KEY,
        'timestamp' => $timestamp,
        'signature' => $signature
    );
    
    // Upload
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$CLOUDINARY_CLOUD_NAME}/{$resource_type}/upload");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $upload_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        $result = json_decode($response, true);
        return array(
            'success' => true,
            'url' => $result['secure_url'],
            'public_id' => $result['public_id']
        );
    } else {
        return array(
            'success' => false,
            'error' => 'Upload failed: ' . $response
        );
    }
}

function getCloudinaryUrl($public_id, $resource_type = 'image', $transformation = '') {
    global $CLOUDINARY_CLOUD_NAME;
    
    $base_url = "https://res.cloudinary.com/{$CLOUDINARY_CLOUD_NAME}/{$resource_type}/upload/";
    
    if (!empty($transformation)) {
        $base_url .= $transformation . '/';
    }
    
    return $base_url . $public_id;
}

// PDF specific functions
function getPDFViewUrl($pdf_url) {
    // Browser માં view માટે - download force નહીં કરે
    return str_replace('/upload/', '/upload/fl_attachment:false/', $pdf_url);
}

function getPDFDownloadUrl($pdf_url) {
    // Download force કરવા માટે
    return str_replace('/upload/', '/upload/fl_attachment/', $pdf_url);
}

function getPDFDownloadUrlByPublicId($public_id) {
    global $CLOUDINARY_CLOUD_NAME;
    return "https://res.cloudinary.com/{$CLOUDINARY_CLOUD_NAME}/raw/upload/fl_attachment/{$public_id}";
}

function getPDFViewUrlByPublicId($public_id) {
    global $CLOUDINARY_CLOUD_NAME;
    return "https://res.cloudinary.com/{$CLOUDINARY_CLOUD_NAME}/raw/upload/{$public_id}";
}
?>