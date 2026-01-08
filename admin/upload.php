<?php
require_once 'init.php';
require_once '../includes/csrf_check.php';

// Function to send a JSON error response
function send_error($message) {
    echo json_encode(['error' => ['message' => $message]]);
    exit;
}

// Check if a file was uploaded
if (!isset($_FILES['upload']) || !is_uploaded_file($_FILES['upload']['tmp_name'])) {
    send_error('No file was uploaded.');
}

$file = $_FILES['upload'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_error = $file['error'];

// Check for upload errors
if ($file_error !== UPLOAD_ERR_OK) {
    send_error('An error occurred during file upload. Error code: ' . $file_error);
}

// Check file size (e.g., 2MB limit)
if ($file_size > 2097152) {
    send_error('The uploaded file is too large. Maximum size is 2MB.');
}

// Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_tmp);
finfo_close($finfo);

$allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($mime_type, $allowed_mime_types)) {
    send_error('Invalid file type. Only JPG, PNG, and GIF images are allowed.');
}

// Generate a unique filename
$file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$file_name_new = uniqid('img_', true) . '.' . $file_ext;
$file_destination = '../uploads/' . $file_name_new;

// Ensure the uploads directory exists and is writable
$uploads_dir = '../uploads';
if (!is_dir($uploads_dir)) {
    if (!mkdir($uploads_dir, 0755, true)) {
        send_error('Failed to create the uploads directory.');
    }
} elseif (!is_writable($uploads_dir)) {
    send_error('The uploads directory is not writable.');
}

// Move the file
if (move_uploaded_file($file_tmp, $file_destination)) {
    // More reliable URL construction
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    // Correctly determine the base URL by removing /admin from the current script's path
    $script_dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    $base_url = rtrim(str_replace('/admin', '', $script_dir), '/');
    $url = $protocol . $host . $base_url . '/uploads/' . $file_name_new;

    echo json_encode(['url' => $url]);
} else {
    send_error('Failed to move the uploaded file. Check directory permissions.');
}
?>