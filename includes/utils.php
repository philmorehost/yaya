<?php
function upload_file($file, $allowed_types, $max_size) {
    $upload_dir = dirname(__DIR__) . '/uploads/';

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $_SESSION['errors'][] = "Upload directory could not be created. Please check permissions.";
            error_log("Failed to create upload directory: " . $upload_dir, 3, dirname(__DIR__) . '/logs/errors.log');
            return null;
        }
    }

    if (!is_writable($upload_dir)) {
        $_SESSION['errors'][] = "Upload directory is not writable.";
        error_log("Upload directory is not writable: " . $upload_dir, 3, dirname(__DIR__) . '/logs/errors.log');
        return null;
    }

    if (!isset($file['error']) || is_array($file['error'])) {
        $_SESSION['errors'][] = "Invalid parameters for file upload.";
        return null;
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return null; // This is not an error, just no file.
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $_SESSION['errors'][] = "Exceeded filesize limit.";
            return null;
        default:
            $_SESSION['errors'][] = "An unknown error occurred during file upload.";
            return null;
    }

    if ($file['size'] > $max_size) {
        $_SESSION['errors'][] = "Exceeded filesize limit ({$max_size} bytes).";
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    if (false === array_search($mime_type, $allowed_types, true)) {
        $_SESSION['errors'][] = "Invalid file format. Allowed types: " . implode(', ', $allowed_types);
        return null;
    }

    $filename = uniqid() . '-' . basename($file['name']);
    $path = 'uploads/' . $filename;
    $destination = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $path;
    } else {
        $_SESSION['errors'][] = "Failed to move uploaded file.";
        error_log("Failed to move uploaded file to: " . $destination, 3, dirname(__DIR__) . '/logs/errors.log');
        return null;
    }
}

function mask_string($string, $visible_start = 3, $visible_end = 4) {
    if (empty($string)) {
        return '';
    }
    $length = strlen($string);
    $masked_length = $length - ($visible_start + $visible_end);
    if ($masked_length <= 0) {
        // Not long enough to mask, return a generic mask
        return '****';
    }
    return substr($string, 0, $visible_start) . str_repeat('*', $masked_length) . substr($string, -$visible_end);
}
