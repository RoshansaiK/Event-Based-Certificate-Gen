<?php
$servername = "localhost";
$username = "roshan";
$password = "password";
$dbname = "dashboardDB";
$port = 4306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    function sanitizeInput($conn, $data) {
        $data = trim($data);
        $data = htmlspecialchars($data);
        $data = $conn->real_escape_string($data);
        return $data;
    }

    // Retrieve form data
    $username = sanitizeInput($conn, $_POST['username']);
    $email = sanitizeInput($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $course = sanitizeInput($conn, $_POST['course']); // Assuming course is posted from form

    // Prepare and bind SQL statement
    $sql = "INSERT INTO users (username, email, password, course) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if prepare() succeeded
    if ($stmt === false) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }

    // Bind parameters and execute statement
    $stmt->bind_param("ssss", $username, $email, $password, $course);

    if ($stmt->execute()) {
        // Registration successful
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    } else {
        // Registration failed
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
} else {
    // Redirect to signup page if accessed directly without POST data
    header("Location: register.php");
    exit();
}

$conn->close();
?>
