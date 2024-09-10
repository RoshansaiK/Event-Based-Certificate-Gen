<?php
session_start();
require_once 'config.php'; // Adjust the path as necessary

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Check if certificate_id is provided
if (!isset($_GET['certificate_id'])) {
    die("Certificate ID is required.");
}

$certificate_id = $_GET['certificate_id'];
$user_id = $_SESSION['userid'];

// Fetch the certificate details to ensure it belongs to the logged-in user
$stmt = $conn->prepare("
    SELECT file_path
    FROM certificates
    WHERE certificate_id = ? AND user_id = ?
");

if ($stmt) {
    $stmt->bind_param("ii", $certificate_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    if ($file_path) {
        // Specify the absolute file path on your server
        $absolute_file_path = 'R:/back/htdocs/t2/uploads/document/' . basename($file_path);

        // Check if the file exists
        if (file_exists($absolute_file_path)) {
            // Set headers to initiate file download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($absolute_file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($absolute_file_path));

            // Clear output buffer
            ob_clean();
            flush();

            // Read the file and output its contents
            readfile($absolute_file_path);
            exit();
        } else {
            die("Certificate file not found.");
        }
    } else {
        die("Certificate not found or you do not have permission to access it.");
    }
} else {
    die("Error fetching certificate details: " . $conn->error);
}
?>
