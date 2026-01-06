<?php
require_once 'init.php';

// 1. Authentication Check (Generic)
if (!is_admin_loggedin()) {
    echo json_encode(['error' => ['message' => 'Authentication required.']]);
    http_response_code(401);
    exit;
}

header('Content-Type: application/json');

// 2. CSRF Token Validation (from the GET parameter)
if (!isset($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
    echo json_encode(['error' => ['message' => 'Invalid CSRF token.']]);
    http_response_code(403);
    exit;
}

if (isset($_FILES['upload'])) {
    $file = $_FILES['upload'];

    // 3. File Type Validation
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileMimeType = mime_content_type($file['tmp_name']);
    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        echo json_encode(['error' => ['message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']]);
        http_response_code(400);
        exit;
    }

    // 4. File Size Limit (e.g., 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['error' => ['message' => 'File is too large. Maximum size is 5MB.']]);
        http_response_code(400);
        exit;
    }

    // Sanitize the filename
    $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\._-]/', '', basename($file['name']));
    $fileDestination = '../uploads/' . $fileName;

    if (move_uploaded_file($file['tmp_name'], $fileDestination)) {
        $url = '/uploads/' . $fileName;
        echo json_encode(['url' => $url]);
    } else {
        // More specific error for debugging
        echo json_encode(['error' => ['message' => 'Failed to move the uploaded file. Check directory permissions.']]);
        http_response_code(500);
    }
} else {
    echo json_encode(['error' => ['message' => 'No file was uploaded.']]);
    http_response_code(400);
}
