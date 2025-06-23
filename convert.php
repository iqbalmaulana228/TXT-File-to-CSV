<?php
header('Content-Type: application/json');

$uploadDir = __DIR__ . '/uploads/';
$csvDir = __DIR__ . '/csv/';

if (!is_dir($csvDir)) {
    mkdir($csvDir, 0755, true);
}

function log_error($message) {
    error_log("[convert.php] " . $message);
}

if (!isset($_GET['file'])) {
    $errorMsg = 'No file specified.';
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

$txtFile = basename($_GET['file']);
$txtFilePath = $uploadDir . $txtFile;

if (!file_exists($txtFilePath)) {
    $errorMsg = 'File not found: ' . $txtFilePath;
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

// Read TXT file content
$content = file_get_contents($txtFilePath);
if ($content === false) {
    $errorMsg = 'Failed to read TXT file: ' . $txtFilePath;
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

// Detect delimiter by analyzing first few lines
function detectDelimiter($text) {
    $delimiters = ["\t", ",", ";", "|"];
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $lineCount = min(5, count($lines));
    $scores = [];

    foreach ($delimiters as $delimiter) {
        $score = 0;
        $prevCount = null;
        for ($i = 0; $i < $lineCount; $i++) {
            $count = substr_count($lines[$i], $delimiter);
            if ($prevCount !== null && $count === $prevCount) {
                $score++;
            }
            $prevCount = $count;
        }
        $scores[$delimiter] = $score;
    }

    arsort($scores);
    return key($scores);
}

$delimiter = detectDelimiter($content);

if (!$delimiter) {
    $errorMsg = 'Failed to detect delimiter.';
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

// Parse lines
$lines = preg_split('/\r\n|\r|\n/', $content);
$data = [];
foreach ($lines as $line) {
    if (trim($line) === '') continue;
    $parsed = str_getcsv($line, $delimiter);
    if ($parsed === false) {
        $errorMsg = 'Failed to parse line: ' . $line;
        log_error($errorMsg);
        echo json_encode(['success' => false, 'error' => $errorMsg]);
        exit;
    }
    $data[] = $parsed;
}

// Convert to CSV string
function arrayToCsvString($array, $delimiter = ',') {
    $fh = fopen('php://temp', 'r+');
    foreach ($array as $row) {
        fputcsv($fh, $row, $delimiter);
    }
    rewind($fh);
    $csv = stream_get_contents($fh);
    fclose($fh);
    return $csv;
}

$csvContent = arrayToCsvString($data, ',');

// Save CSV file
$csvFilename = uniqid('csv_', true) . '.csv';
$csvFilePath = $csvDir . $csvFilename;

if (file_put_contents($csvFilePath, $csvContent) === false) {
    $errorMsg = 'Failed to save CSV file: ' . $csvFilePath;
    log_error($errorMsg);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

// Return CSV file URL and filename
$csvUrl = 'csv/' . $csvFilename;

echo json_encode([
    'success' => true,
    'csv_url' => $csvUrl,
    'csv_filename' => 'converted.csv'
]);

// Optional: Implement file cleanup logic here or rely on server cron jobs
?>
