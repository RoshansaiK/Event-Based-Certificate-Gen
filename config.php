<?php
$servername = "localhost";
$username = "roshan";
$password = "password";
$dbname = "dashboardDB";
$port=4306;

$conn = new mysqli($servername, $username, $password, $dbname,$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
