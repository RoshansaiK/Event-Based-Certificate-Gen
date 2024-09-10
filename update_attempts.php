<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['userid']) && isset($_POST['attempts'])) {
    $user_id = $_POST['userid'];
    $attempts = $_POST['attempts'];

    $stmt = $conn->prepare("UPDATE users SET attempts = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $attempts, $user_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error updating attempts: " . $conn->error;
    }
}
?>
