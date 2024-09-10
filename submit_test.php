<?php
session_start();
require_once 'config.php'; // Adjust path as necessary

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['userid'];
$course_id = $_POST['course_id'];
$answers = $_POST['answers'];

// Fetch correct answers
$stmt = $conn->prepare("SELECT id, correct_option FROM test_questions WHERE course_id = ?");
if ($stmt) {
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $correct_answers = [];
    while ($row = $result->fetch_assoc()) {
        $correct_answers[$row['id']] = $row['correct_option'];
    }
    $stmt->close();
} else {
    die("Error fetching correct answers: " . $conn->error);
}

// Calculate score
$score = 0;
foreach ($answers as $question_id => $selected_option) {
    if (isset($correct_answers[$question_id]) && $correct_answers[$question_id] == $selected_option) {
        $score++;
    }
}

// Determine pass/fail
$total_questions = count($correct_answers);
$pass_percentage = 70;
$pass_score = ($total_questions * $pass_percentage) / 100;

if ($score >= $pass_score) {
    // Fetch user and course details for certificate
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($username);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Error fetching user details: " . $conn->error);
    }

    $stmt = $conn->prepare("SELECT title FROM courses WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $stmt->bind_result($course_title);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Error fetching course details: " . $conn->error);
    }

    // Generate a unique certificate ID
    $certificate_id = uniqid('cert_', true);

    // Insert certificate details into the database
    $stmt = $conn->prepare("INSERT INTO certificates (user_id, course_id, certificate_id) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("iis", $user_id, $course_id, $certificate_id);
        if ($stmt->execute()) {
            // Redirect to certificate generation page
            header("Location: generate_certificate.php?certificate_id=$certificate_id");
            exit();
        } else {
            die("Error inserting certificate details: " . $stmt->error);
        }
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }
} else {
    echo "You did not pass the test. Please try again.";
}
?>