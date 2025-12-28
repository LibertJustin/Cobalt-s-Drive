<?php
// File: /var/www/html/download.php
session_start(); // Start the session to access $_SESSION variables

// Check if the session token and filePath parameter are provided
if (!isset($_SESSION['user_token']) || !isset($_GET['filePath'])) {
    http_response_code(400); // Bad Request
    echo "Invalid request.";
    exit;
}
$filePath = isset($_GET['filePath']) ? $_GET['filePath'] : '';
$isTokenPresent = isset($_SESSION['user_token']) && strpos($filePath, $_SESSION['user_token']) !== false;
/*
if (!$isTokenPresent) {
    http_response_code(403); // Forbidden
    echo "Invalid token.";
    exit;
}
*/
// Sanitize the file path to prevent directory traversal attacks
$file = $_GET['filePath'];
//$file = basename($_GET['filePath']);
$filePath = __DIR__ . "/DATA/" . $_SESSION['user_token'] . '/' . $file;
//die($filePath);
// Check if the file exists
if (!file_exists($filePath)) {
    http_response_code(404); // Not Found
    echo "File not found.";
    exit;
}

// Set headers to initiate the file download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

// Read the file and output its contents
readfile($filePath);
ignore_user_abort(true); // Allow the script to continue running if the user aborts the connection
fastcgi_finish_request(); // End the request and flush all output to the client
header("Location: ../files.php");
exit();
?>
