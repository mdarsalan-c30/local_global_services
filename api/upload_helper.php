<?php
/**
 * Cloudinary & Local Image Upload Helper
 * Handles uploading files to Cloudinary (using API URL) or falling back to local storage.
 */

function upload_service_image($file) {
    // 1. Load Cloudinary config if exists
    $cloudinaryUrl = null;
    $configPath = __DIR__ . '/cloudinary_config.php';
    if (file_exists($configPath)) {
        include_once $configPath;
        if (defined('CLOUDINARY_URL') && !empty(CLOUDINARY_URL)) {
            $cloudinaryUrl = CLOUDINARY_URL;
        }
    }
    
    // 2. If Cloudinary URL is defined, try uploading to Cloudinary
    if ($cloudinaryUrl) {
        $parsed = parse_url($cloudinaryUrl);
        if ($parsed && isset($parsed['host']) && isset($parsed['user']) && isset($parsed['pass'])) {
            $cloudName = $parsed['host'];
            $apiKey = $parsed['user'];
            $apiSecret = $parsed['pass'];
            
            $timestamp = time();
            // Generate signature: parameters must be sorted alphabetically
            $params = [
                'timestamp' => $timestamp,
                'folder' => 'localglobals/uploads',
                'upload_preset' => 'localglobals'
            ];
            ksort($params);
            $signParts = [];
            foreach ($params as $k => $v) {
                $signParts[] = "$k=$v";
            }
            $signString = implode('&', $signParts) . $apiSecret;
            $signature = sha1($signString);
            
            $postFields = [
                'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
                'api_key' => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature,
                'folder' => 'localglobals/uploads',
                'upload_preset' => 'localglobals'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local development robustness
            
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            
            if (!$err) {
                $result = json_decode($response, true);
                if (isset($result['secure_url'])) {
                    return $result['secure_url']; // Return the Cloudinary HTTPS URL
                }
            }
        }
    }
    
    // 3. Fallback to Local Upload
    $uploadDir = dirname(__DIR__) . '/resources/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'service_' . uniqid() . '.' . $extension;
    $destination = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return 'resources/uploads/' . $fileName; // Return relative path
    }
    
    return null;
}
?>
