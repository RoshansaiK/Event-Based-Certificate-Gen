<?php
session_start();
require_once 'config.php'; // Adjust path to config.php if necessary

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle profile photo upload
    $uploadDir = 'uploads/profile/';
    $profilePhotoPath = '';

    if (!empty($_FILES['profile-photo']['name'])) {
        $fileName = basename($_FILES['profile-photo']['name']);
        $targetFilePath = $uploadDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Check if file is an image
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES['profile-photo']['tmp_name'], $targetFilePath)) {
                $profilePhotoPath = $targetFilePath;
            }
        }
    }

    // Sanitize and update username (assuming you have a User class or function)
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password']; // Password should be hashed before storing in real application

    // Update user profile in database (assuming you have a function to update profile)
    // Example: updateUserProfile($userId, $username, $profilePhotoPath);

    // Display success message
    $alertMessage = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Profile updated successfully!
                        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>";
}

// Function to safely fetch user profile data
function getUserProfile($conn, $userid) {
    $stmt = $conn->prepare("SELECT username, email, name, photo FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $stmt->bind_result($username, $email, $name, $photo);
        $stmt->fetch();
        $stmt->close();
        return [
            'username' => $username,
            'email' => $email,
            'name' => $name,
            'photo' => $photo
        ];
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

// Function to fetch courses
function getCourses($conn) {
    $stmt = $conn->prepare("SELECT id, title, description, image FROM courses");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        $stmt->close();
        return $courses;
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

// Function to fetch active events
function getActiveEvents($conn) {
    $stmt = $conn->prepare("SELECT id, title, description, date, status, image FROM events");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $events = [];
        while ($row = $result->fetch_assoc()) {
            // Determine status class based on 'status' field
            $status_class = ($row['status'] == 1) ? 'active-event' : 'inactive-event';

            // Add the status class to the event data
            $row['status_class'] = $status_class;

            $events[] = $row;
        }
        $stmt->close();
        return $events;
    } else {
        die("Error preparing statement: " . $conn->error);
    }
}

// Function to fetch course completion percentage
// Function to fetch course completion percentage
function getCourseCompletion($conn, $userid) {
    // Get the total number of courses
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_courses FROM courses");
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($total_courses);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }

    // Get the number of completed courses for the user
    $stmt = $conn->prepare("SELECT COUNT(*) AS completed_courses FROM course_progress WHERE user_id = ? AND read_at IS NOT NULL");
    if ($stmt) {
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $stmt->bind_result($completed_courses);
        $stmt->fetch();
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }

    // Calculate the completion percentage
    $completion_percentage = ($total_courses > 0) ? ($completed_courses / $total_courses) * 100 : 0;
    return $completion_percentage;
}

// Fetch user profile data
$user_profile = getUserProfile($conn, $_SESSION['userid']);

// Fetch active events
$events = getActiveEvents($conn);

// Calculate course completion percentage
$course_progress = getCourseCompletion($conn, $_SESSION['userid']);

// Fetch courses
$courses = getCourses($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    /* Existing CSS styles */
    body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #00416A, #E4E5E6);
    color: #fff;
    padding-top: 70px;
    margin-bottom: 60px;
}

.container {
    margin-top: 20px;
}

.profile-box, .course-progress-box, .notifications-box, .events-box, .courses-box, .settings-box {
    background-color: rgba(0, 0, 0, 0.7);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

.active-event {
    border: 2px solid green;
    background-color: rgba(0, 128, 0, 0.2);
}

.inactive-event {
    border: 2px solid grey;
    background-color: rgba(128, 128, 128, 0.2);
}

.profile-box img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}

.progress {
    height: 20px;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.notification-item {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    color: #fff;
}

.notification-item:last-child {
    border-bottom: none;
}

.event-item {
    padding: 20px;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ddd;
    color: #fff;
}

.event-item:last-child {
    border-bottom: none;
}

.event-item img {
    width: 70px;
    height: 70px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 10px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.header {
    background-color: rgba(0, 0, 0, 0.8);
    padding: 4px 0;
    color: #fff;
    text-align: center;
    width: 100%;
    position: fixed;
    top: 0;
    z-index: 1000;
}

.header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
}

.header nav {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}

.header nav a {
    color: #fff;
    text-decoration: none;
    margin: 0 10px;
    font-size: 16px;
    transition: color 0.3s ease;
}

.header nav a:hover {
    color: #007bff;
}

.courses-box .course-item {
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ddd;
    color: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    background-color: #607d8b;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.courses-box .course-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
}

.courses-box .course-item img {
    width: 100px;
    height: 100px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 20px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
}

.courses-box .course-item h4 {
    margin: 0 0 10px 0;
    font-size: 18px;
    font-weight: bold;
}

.courses-box .course-item p {
    margin: 0 0 10px 0;
}

.courses-box .course-item .btn {
    margin-right: 10px;
}

.settings-box {
    display: none;
    background-color: rgba(0, 0, 0, 0.9);
    color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1001;
    width: 80%;
    max-width: 500px;
}

.settings-box input[type="text"], .settings-box input[type="password"], .settings-box input[type="file"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

.settings-box button[type="submit"] {
    display: block;
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.settings-box .close-settings {
    display: block;
    text-align: right;
    cursor: pointer;
    color: #fff;
}

footer {
    background-color: rgba(0, 0, 0, 0.8);
    padding: 10px 0;
    color: #fff;
    text-align: center;
    width: 100%;
    position: fixed;
    bottom: 0;
    z-index: 1000;
}

    .container {
        margin-top: 20px;
    }
    </style>
</head>
<body>
    <header class="header">
        <h1>Student Dashboard</h1>
        <nav>
            <a href="#profile">Profile</a>
            <a href="#events">Events</a>
            <a href="#courses">Courses</a>
            <a href="#settings" id="settings-link">Settings</a>
            <a href="login.php">Logout</a>
        </nav>
    </header>

    <div class="container">
    <div class="profile-box">
    <h3>Profile</h3>
    <?php 
    $profilePhoto = !empty($user_profile['photo']) ? htmlspecialchars($user_profile['photo']) : "3237472.png"; 
    ?>
    <img src="<?php echo $profilePhoto; ?>" alt="Profile Photo">
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user_profile['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_profile['email']); ?></p>
</div>


        <div class="course-progress-box">
            <h3>Course Progress</h3>
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $course_progress; ?>%;" aria-valuenow="<?php echo $course_progress; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($course_progress); ?>%</div>
            </div>
        </div>

        <div class="events-box">
            <h3>Events</h3>
            <?php foreach ($events as $event): ?>
                <div class="event-item <?php echo $event['status_class']; ?>">
                    <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="Event Image">
                    <div>
                        <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="courses-box">
    <h3>Courses</h3>
    <?php foreach ($courses as $course): ?>
        <div class="course-item">
            <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="Course Image">
            <div>
                <h4><?php echo htmlspecialchars($course['title']); ?></h4>
                <p><?php echo htmlspecialchars($course['description']); ?></p>
                
                <!-- Test Button/Link -->
                <a href="course_details.php?course_id=<?php echo htmlspecialchars($course['id']); ?>" class="btn btn-primary">View</a>
                <a href="take_test.php?course_id=<?php echo htmlspecialchars($course['id']); ?>" class="btn btn-primary" onclick="enterFullscreen()">Take Test (Full Screen)</a>

            </div>
        </div>
    <?php endforeach; ?>
</div>

 <div class="settings-box" id="settings-box">
            <h3>Settings</h3>
            <form action="update_profile.php" method="post" enctype="multipart/form-data">
                <label for="profile-photo">Change Profile Photo</label>
                <input type="file" id="profile-photo" name="profile-photo">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_profile['username']); ?>">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <button type="submit">Update Profile</button>
            </form>
            <span class="close-settings" onclick="closeSettings()">Close</span>
        </div>
    </div>

    
    <script>
        document.getElementById('settings-link').addEventListener('click', function() {
            document.getElementById('settings-box').style.display = 'block';
        });

        function closeSettings() {
            document.getElementById('settings-box').style.display = 'none';
        }
    
    function enterFullscreen() {
        var element = document.documentElement; // Get the HTML element
        if (element.requestFullscreen) {
            element.requestFullscreen(); // Standard
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen(); // Webkit browsers
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen(); // IE
        }
    }


    </script>
</body>
</html>
