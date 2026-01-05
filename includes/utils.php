<?php
function upload_file($file, $allowed_types, $max_size) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $file['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        return null;
    }

    if ($file['size'] > $max_size) {
        return null;
    }

    $filename = uniqid() . '-' . basename($file['name']);
    $path = 'uploads/' . $filename;
    if (move_uploaded_file($file['tmp_name'], dirname(__DIR__) . '/' . $path)) {
        return $path;
    }

    return null;
}
