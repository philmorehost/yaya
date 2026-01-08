<?php
require_once 'init.php';
require_once '../includes/csrf_check.php';

// Function to send a JSON error response
function send_error($message) {
    echo json_encode(['error' => ['message' => $message]]);
    exit;
}

// Check if a file was uploaded
if (!isset($_FILES['upload']) || !is_uploaded_file($_FILES['upload']['tmp_name']) || $_FILES['upload']['error'] != UPLOAD_ERR_OK) {
    send_error('No file was uploaded or an upload error occurred.');
}

$file = $_FILES['upload'];
$file_tmp = $file['tmp_name'];

// --- Start: Logic mirrored from homepage_settings.php ---

$target_dir = "../uploads/";
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0755, true)) {
        send_error('Failed to create the uploads directory.');
    }
}

// Generate a unique filename to prevent overwrites
$imageFileType = strtolower(pathinfo(basename($file["name"]), PATHINFO_EXTENSION));
$unique_filename = "img_" . uniqid() . "." . $imageFileType;
$target_file = $target_dir . $unique_filename;

// Check if image file is a actual image or fake image
$check = getimagesize($file_tmp);
if($check === false) {
    send_error('File is not a valid image.');
}

// Allow only specific file formats
$allowed_formats = ["jpg", "png", "jpeg", "gif"];
if (!in_array($imageFileType, $allowed_formats)) {
    send_error('Invalid file type. Only JPG, PNG, and GIF images are allowed.');
}

// Move the uploaded file
if (move_uploaded_file($file_tmp, $target_file)) {
    // Path stored should be relative to the root for the URL
    $url = "/uploads/" . $unique_filename;
    echo json_encode(['url' => $url]);
} else {
    send_error('Failed to move the uploaded file. Check directory permissions.');
}

// --- End: Logic mirrored from homepage_settings.php ---
?>
