<?php
session_start(); // Start session if not already started
require_once 'config.php'; // Adjust path to config.php if necessary

// Check if user is logged in and has admin role
if (!isset($_SESSION['userid']) || $_SESSION['role'] != 0) {
    header("Location: login.php");
    exit();
}

// Ensure course_id is set and is a valid integer
if (!isset($_GET['course_id']) || !filter_var($_GET['course_id'], FILTER_VALIDATE_INT)) {
    // Redirect if course_id is not provided or invalid
    header("Location: dashboard.php"); // Adjust to your actual dashboard or courses page
    exit();
}

// Function to safely fetch course details including document
function getCourseDetails($conn, $course_id) {
    $stmt = $conn->prepare("SELECT id, course_id, description, overview, document FROM syllabuses WHERE course_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $stmt->bind_result($id, $course_id, $description, $overview, $document);
        $stmt->fetch();
        $stmt->close();
        return [
            'id' => $id,
            'course_id' => $course_id,
            'description' => $description,
            'overview' => $overview,
            'document' => $document
        ];
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

// Function to check if the course is marked as read by the user
function isCourseMarkedAsRead($conn, $course_id, $user_id) {
    $stmt = $conn->prepare("SELECT id FROM course_progress WHERE course_id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $course_id, $user_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0; // Return true if there are rows (course is marked as read)
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

// Fetch course details based on course_id from query string
$course_id = $_GET['course_id'];
$user_id = $_SESSION['userid'];
$course_details = getCourseDetails($conn, $course_id);

// Check if the course is marked as read by the user
$is_course_read = isCourseMarkedAsRead($conn, $course_id, $user_id);

// If course_details is empty (no course found), handle accordingly (redirect or error message)
if (!$course_details) {
    header("Location: dashboard.php"); // Adjust to your actual dashboard or courses page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Include the CSS styles provided above */
        body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #00416A, #E4E5E6); /* Dark blue gradient */
    color: #fff;
    padding-top: 70px; /* Adjusted for fixed header */
    margin-bottom: 60px; /* Adjusted for sticky footer */
}

.container {
    margin-top: 20px;
}

.course-details {
    background-color: rgba(0, 0, 0, 0.7);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

.course-details h2 {
    color: #fff;
    margin-bottom: 20px;
}

.course-details p {
    color: #fff;
}

.btn-mark-read {
    margin-top: 10px;
}

.document-container {
    margin-top: 20px;
}

.document-container a {
    color: #fff;
    text-decoration: none;
}

.document-container a:hover {
    text-decoration: underline;
}

.btn {
    color: #fff;
    background-color: #007BFF;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #007BFF;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.icon {
    margin-right: 5px;
}

    </style>
</head>
<body>

<div class="container">
    <div class="course-details">
        <h2><?php echo htmlspecialchars($course_details['description']); ?></h2>
        <p><?php echo htmlspecialchars($course_details['overview']); ?></p>

        <?php if ($course_details['document']): ?>
            <div class="document-container">
                <h4>Course Document</h4>
                <a href="<?php echo htmlspecialchars($course_details['document']); ?>" target="_blank">
                    <i class="fas fa-file-pdf icon"></i> View Document
                </a>
            </div>
        <?php endif; ?>

        <?php if (!$is_course_read): ?>
            <form action="mark_course_read.php" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <button type="submit" class="btn btn-primary btn-mark-read">
                    <i class="fas fa-check-circle icon"></i> Mark as Read
                </button>
            </form>
        <?php else: ?>
            <p><i class="fas fa-check-circle icon"></i> You have marked this course as read.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
