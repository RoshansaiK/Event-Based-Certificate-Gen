<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);

    $questions = getTestQuestions($conn, $course_id);
    
    echo json_encode($questions);
}

function getTestQuestions($conn, $course_id) {
    $stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d, correct_option FROM test_questions WHERE course_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        $stmt->close();
        return $questions;
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}
?>
