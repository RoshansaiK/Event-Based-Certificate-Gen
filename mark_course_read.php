<?php
session_start();
require_once 'config.php'; // Adjust path to config.php if necessary

// Check if user is logged in and has admin role
if (!isset($_SESSION['userid']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit();
}

// Ensure course_id is set and is a valid integer
if (!isset($_POST['course_id']) || !filter_var($_POST['course_id'], FILTER_VALIDATE_INT)) {
    // Redirect if course_id is not provided or invalid
    header("Location: dashboard.php"); // Adjust to your actual dashboard or courses page
    exit();
}

$user_id = $_SESSION['userid'];
$course_id = $_POST['course_id'];

// Check if the course is already marked as read
$stmt = $conn->prepare("SELECT id FROM course_progress WHERE user_id = ? AND course_id = ?");
if ($stmt) {
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Course is already marked as read
        $stmt->close();
        header("Location: course_details.php?course_id=$course_id");
        exit();
    }
    $stmt->close();
} else {
    die("Error preparing statement: " . $conn->error);
}

// Insert new progress record
$insert_stmt = $conn->prepare("INSERT INTO course_progress (user_id, course_id) VALUES (?, ?)");
if ($insert_stmt) {
    $insert_stmt->bind_param("ii", $user_id, $course_id);
    if ($insert_stmt->execute()) {
        // Successfully marked as read
        $insert_stmt->close();
        header("Location: course_details.php?course_id=$course_id");
        exit();
    } else {
        die("Error executing statement: " . $insert_stmt->error);
    }
} else {
    die("Error preparing statement: " . $conn->error);
}
?>
