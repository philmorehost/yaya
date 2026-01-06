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

// Ensure the uploads directory exists
if (!is_dir('../uploads')) {
    if (!mkdir('../uploads', 0777, true)) {
        send_error('Failed to create the uploads directory.');
    }
}

// Move the file
if (move_uploaded_file($file_tmp, $file_destination)) {
    // Respond with the URL of the uploaded file
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $base_path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Get the script's directory
    $url = $protocol . $host . str_replace('/admin', '', $base_path) . '/uploads/' . $file_name_new;

    echo json_encode(['url' => $url]);
} else {
    send_error('Failed to move the uploaded file.');
}
?>