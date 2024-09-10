<?php
session_start();
require 'config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['userid']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch courses from the database
function getCourses() {
    global $conn;
    $sql = "SELECT * FROM courses";
    $result = mysqli_query($conn, $sql);
    $courses = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $courses[] = $row;
        }
    } else {
        echo "Error fetching courses: " . mysqli_error($conn);
    }
    return $courses;
}

// Fetch events from the database
function getEvents() {
    global $conn;
    $sql = "SELECT * FROM events";
    $result = mysqli_query($conn, $sql);
    $events = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
    } else {
        echo "Error fetching events: " . mysqli_error($conn);
    }
    return $events;
}

// Fetch test questions from the database
function getTestQuestions() {
    global $conn;
    $sql = "SELECT * FROM test_questions";
    $result = mysqli_query($conn, $sql);
    $test_questions = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $test_questions[] = $row;
        }
    } else {
        echo "Error fetching test questions: " . mysqli_error($conn);
    }
    return $test_questions;
}

// Fetch syllabuses from the database
function getSyllabuses() {
    global $conn;
    $sql = "SELECT * FROM syllabuses";
    $result = mysqli_query($conn, $sql);
    $syllabuses = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $syllabuses[] = $row;
        }
    } else {
        echo "Error fetching syllabuses: " . mysqli_error($conn);
    }
    return $syllabuses;
}

// Handle form submissions for adding courses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            echo "Error moving uploaded file.";
            exit();
        }
    }

    $sql = "INSERT INTO courses (title, description, image) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $title, $description, $image);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle form submissions for editing courses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_course'])) {
    $id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_POST['existing_image'];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            echo "Error moving uploaded file.";
            exit();
        }
    }

    $sql = "UPDATE courses SET title=?, description=?, image=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $image, $id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle form submissions for deleting courses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_course'])) {
    $id = $_POST['course_id'];

    $sql = "DELETE FROM courses WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle form submissions for adding test questions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_test_question'])) {
    $course_id = $_POST['course_id'];
    $question = $_POST['question'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $sql = "INSERT INTO test_questions (course_id, question, option_a, option_b, option_c, option_d, correct_option) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issssss", $course_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_option);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle form submissions for adding events
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    $image = '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            echo "Error moving uploaded file.";
            exit();
        }
    }

    $sql = "INSERT INTO events (title, description, date, status, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $date, $status, $image);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle form submissions for adding syllabuses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_syllabus'])) {
    $course_id = $_POST['course_id'];
    $description = $_POST['description'];
    $overview = $_POST['overview'];
    $document = '';

    // Handle file upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $target_dir = "uploads/syllabuses/";
        $document = $target_dir . basename($_FILES["document"]["name"]);
        if (!move_uploaded_file($_FILES["document"]["tmp_name"], $document)) {
            echo "Error moving uploaded file.";
            exit();
        }
    }

    $sql = "INSERT INTO syllabuses (course_id, description, overview, document) 
            VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isss", $course_id, $description, $overview, $document);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle form submissions for deleting syllabuses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_syllabus'])) {
    $id = $_POST['syllabus_id'];

    $sql = "DELETE FROM syllabuses WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    body {
        background-color: #1a1a2e;
        color: #fff; /* Default content color */
    }
    .navbar {
        background-color: #162447;
    }
    .card {
        background-color: #1f4068;
        border: none;
        margin-bottom: 1rem;
    }
    .card-header {
        background-color: #fff; /* White background for card headers */
        border: none;
        color: #000; /* Black text color for card headers */
    }
    .btn-primary {
        background-color: #fff; /* White background for button */
        color: #007bff; /* Blue text color for button */
        border: none;
    }
    .nav-tabs .nav-link.active {
        background-color: #fff; /* White background for active tab */
        border: none;
        color: #007bff; /* Blue text color for active tab */
    }
    .custom-file-label::after {
        background-color: #007bff; /* Blue color for custom file input button */
        border-color: #007bff;
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-book"></i> Total Courses
                </div>
                <div class="card-body">
                    <h5 class="card-title" id="totalCourses">1</h5>
                    <p class="card-text">Number of available courses.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-graduate"></i> Total Users
                </div>
                <div class="card-body">
                    <h5 class="card-title" id="totalUsers">5</h5>
                    <p class="card-text">Number of registered users.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i> User Attempts
                </div>
                <div class="card-body">
                    <canvas id="userAttemptsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#courses">Courses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#test_questions">Test Questions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#events">Events</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#syllabuses">Syllabuses</a>
            </li>
        </ul>
        <div class="tab-content mt-3">
            <!-- Courses Tab -->
            <div id="courses" class="tab-pane fade show active">
                <div class="card">
                    <div class="card-header">
                        <h4>Add Course</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="image">Image:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                    <label class="custom-file-label" for="image">Choose file</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_course">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Courses</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getCourses() as $course): ?>
                                <tr>
                                    <td><?php echo $course['title']; ?></td>
                                    <td><?php echo $course['description']; ?></td>
                                    <td>
                                        <?php if (!empty($course['image'])): ?>
                                        <img src="<?php echo $course['image']; ?>" alt="Course Image" style="max-width: 100px;">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                            <button type="submit" class="btn btn-danger" name="delete_course">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Test Questions Tab -->
            <div id="test_questions" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">
                        <h4>Add Test Question</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="course_id">Course:</label>
                                <select class="form-control" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    <?php foreach (getCourses() as $course): ?>
                                    <option value="<?php echo $course['id']; ?>"><?php echo $course['title']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="question">Question:</label>
                                <textarea class="form-control" id="question" name="question" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="option_a">Option A:</label>
                                <input type="text" class="form-control" id="option_a" name="option_a" required>
                            </div>
                            <div class="form-group">
                                <label for="option_b">Option B:</label>
                                <input type="text" class="form-control" id="option_b" name="option_b" required>
                            </div>
                            <div class="form-group">
                                <label for="option_c">Option C:</label>
                                <input type="text" class="form-control" id="option_c" name="option_c" required>
                            </div>
                            <div class="form-group">
                                <label for="option_d">Option D:</label>
                                <input type="text" class="form-control" id="option_d" name="option_d" required>
                            </div>
                            <div class="form-group">
                                <label for="correct_option">Correct Option:</label>
                                <select class="form-control" id="correct_option" name="correct_option" required>
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_test_question">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Test Questions</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Question</th>
                                    <th>Options</th>
                                    <th>Correct Option</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getTestQuestions() as $question): ?>
                                <tr>
                                    <td><?php echo $question['course_id']; ?></td>
                                    <td><?php echo $question['question']; ?></td>
                                    <td>
                                        A. <?php echo $question['option_a']; ?><br>
                                        B. <?php echo $question['option_b']; ?><br>
                                        C. <?php echo $question['option_c']; ?><br>
                                        D. <?php echo $question['option_d']; ?>
                                    </td>
                                    <td><?php echo $question['correct_option']; ?></td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                            <button type="submit" class="btn btn-danger" name="delete_question">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Events Tab -->
            <div id="events" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">
                        <h4>Add Event</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="date">Date:</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Upcoming">Upcoming</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image">Image:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                                    <label class="custom-file-label" for="image">Choose file</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_event">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Events</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getEvents() as $event): ?>
                                <tr>
                                    <td><?php echo $event['title']; ?></td>
                                    <td><?php echo $event['description']; ?></td>
                                    <td><?php echo $event['date']; ?></td>
                                    <td><?php echo $event['status']; ?></td>
                                    <td>
                                        <?php if (!empty($event['image'])): ?>
                                        <img src="<?php echo $event['image']; ?>" alt="Event Image" style="max-width: 100px;">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-danger" name="delete_event">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Syllabuses Tab -->
            <div id="syllabuses" class="tab-pane fade">
                <div class="card">
                    <div class="card-header">
                        <h4>Add Syllabus</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="course_id">Course:</label>
                                <select class="form-control" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    <?php foreach (getCourses() as $course): ?>
                                    <option value="<?php echo $course['id']; ?>"><?php echo $course['title']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="overview">Overview:</label>
                                <textarea class="form-control" id="overview" name="overview" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="document">Document:</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="document" name="document" accept=".pdf">
                                    <label class="custom-file-label" for="document">Choose file</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_syllabus">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Syllabuses</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Description</th>
                                    <th>Overview</th>
                                    <th>Document</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getSyllabuses() as $syllabus): ?>
                                <tr>
                                    <td><?php echo $syllabus['course_id']; ?></td>
                                    <td><?php echo $syllabus['description']; ?></td>
                                    <td><?php echo $syllabus['overview']; ?></td>
                                    <td>
                                        <?php if (!empty($syllabus['document'])): ?>
                                        <a href="<?php echo $syllabus['document']; ?>" target="_blank">View Document</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="syllabus_id" value="<?php echo $syllabus['id']; ?>">
                                            <button type="submit" class="btn btn-danger" name="delete_syllabus">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
   




<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass('selected').html(fileName);
    });

    // Fetch total users and courses for statistics
    $.ajax({
        url: 'fetch_statistics.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#totalCourses').text(data.totalCourses);
            $('#totalUsers').text(data.totalUsers);

            // Populate the user attempts chart
            var ctx = document.getElementById('userAttemptsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.userAttempts.labels,
                    datasets: [{
                        label: '# of Attempts',
                        data: data.userAttempts.data,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>
