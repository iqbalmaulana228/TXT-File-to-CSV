<?php
header('Content-Type: application/json');

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function log_error($message) {
    error_log("[upload.php] " . $message);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file'])) {
        $errorMsg = 'No file uploaded.';
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }

    $file = $_FILES['file'];
    $allowedTypes = ['text/plain'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'File upload error: ' . $file['error'];
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }

    if (!in_array($file['type'], $allowedTypes) && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'txt') {
        $errorMsg = 'Invalid file type. Only TXT files are allowed.';
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }

    if ($file['size'] > $maxFileSize) {
        $errorMsg = 'File size exceeds limit of 5MB.';
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }

    $safeName = uniqid('txt_', true) . '.txt';
    $uploadFilePath = $uploadDir . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
        $errorMsg = 'Failed to save uploaded file.';
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }

    // Call convert.php to convert the file
    $convertUrl = 'http://localhost/txt_tocsv/convert.php?file=' . urlencode($safeName);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $convertUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false || $curlError) {
        unlink($uploadFilePath);
        $errorMsg = 'Conversion service unavailable. Curl error: ' . $curlError;
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }

    $result = json_decode($response, true);
    if (!$result || !isset($result['success']) || !$result['success']) {
        unlink($uploadFilePath);
        $errorMsg = $result['error'] ?? 'Conversion failed.';
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }

    // Delete the uploaded TXT file after conversion
    unlink($uploadFilePath);

    echo json_encode([
        'success' => true,
        'csv_url' => $result['csv_url'],
        'csv_filename' => $result['csv_filename']
    ]);
} else {
    $errorMsg = 'Invalid request method.';
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}
