<?php
session_start();
require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Verify email before proceeding
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    $_SESSION['email_verified'] = true;
}

// Redirect to email verification if not verified
if (!isset($_SESSION['email_verified']) || $_SESSION['email_verified'] !== true) {
    include 'verify_email.php';
    exit();
}

// Fetch user details
$user_id = $_SESSION['userid'];
$stmt = $conn->prepare("SELECT name, attempts, exits FROM users WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $attempts, $exits);
    $stmt->fetch();
    $stmt->close();

    // Check if attempts are exceeded
    if ($attempts >= 3) {
        header("Location: home.php");
        exit();
    }

    // Check if exits are exceeded
    if ($exits >= 4) {
        $attempts++;
        $stmt = $conn->prepare("UPDATE users SET attempts = ?, exits = 0 WHERE id = ?");
        $stmt->bind_param("ii", $attempts, $user_id);
        $stmt->execute();
        $stmt->close();

        header("Location: home.php");
        exit();
    }
} else {
    die("Error fetching user details: " . $conn->error);
}

// Fetch course details and test questions
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

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

    $stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d FROM test_questions WHERE course_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        $stmt->close();
    } else {
        die("Error fetching test questions: " . $conn->error);
    }
}

// Function to update exit count
function updateExitCount($conn, $user_id, $exits) {
    $stmt = $conn->prepare("UPDATE users SET exits = ? WHERE id = ?");
    $stmt->bind_param("ii", $exits, $user_id);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Test</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #00416A, #E4E5E6);
            color: #fff;
            padding-top: 70px;
            margin-bottom: 60px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            margin-top: 20px;
        }
        .test-box {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .question-item {
            margin-bottom: 20px;
        }
        .question {
            color: #fff;
        }
        .options label {
            color: #fff;
            display: block;
            margin-top: 10px;
        }
        .options input[type="radio"] {
            margin-right: 10px;
        }
        .submit-btn {
            margin-top: 20px;
        }
        .timer {
            color: #fff;
            font-size: 20px;
            margin-top: 10px;
        }
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #00416A;
            padding: 15px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #E4E5E6;
            font-size: 24px;
            font-weight: bold;
        }
        .header h1 span {
            background-color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            color: #00416A;
        }
        .fullscreen-btn {
            margin-bottom: 20px;
            cursor: pointer;
            color: #fff;
        }
        .full-height {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="header">
    <h1><span>Take Test</span></h1>
</div>

<div class="full-height">
    <div class="container">
        <div class="test-box" id="testBox" style="display: none;">
            <h2>Test for Course: <?php echo htmlspecialchars($course_title); ?></h2>
            <h3>Welcome, <?php echo htmlspecialchars($username); ?>!</h3>
            <div id="timer" class="timer">Time Remaining: 20:00</div>
            <form id="testForm" action="submit_test.php" method="POST">
                <?php foreach ($questions as $question): ?>
                    <div class="question-item">
                        <p class="question"><?php echo htmlspecialchars($question['question']); ?></p>
                        <div class="options">
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="A">
                                <?php echo htmlspecialchars($question['option_a']); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="B">
                                <?php echo htmlspecialchars($question['option_b']); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="C">
                                <?php echo htmlspecialchars($question['option_c']); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="D">
                                <?php echo htmlspecialchars($question['option_d']); ?>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <button type="submit" class="btn btn-primary submit-btn">Submit Test</button>
            </form>
        </div>
        <button id="startTest" class="btn btn-primary">Start Test</button>
        <div id="fullscreenToggle" class="fullscreen-btn" onclick="toggleFullscreen()">
            <i class="fas fa-expand"></i> Full-Screen
        </div>
    </div>
</div>

<script>
    var timeLeft = 1200; // 20 minutes in seconds
    var timer = null;

    document.getElementById('startTest').addEventListener('click', function() {
        startTest();
    });

    function startTest() {
        fetchQuestions(); // Function to fetch questions (you need to implement this)
        toggleFullscreen(); // Enter full-screen mode
        startTimer(); // Start the timer
    }

    function fetchQuestions() {
        document.getElementById('testBox').style.display = 'block';
        document.getElementById('startTest').style.display = 'none';
        document.getElementById('fullscreenToggle').style.display = 'block';
    }

    function startTimer() {
        timer = setInterval(function() {
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById('testForm').submit();
            } else {
                timeLeft--;
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                document.getElementById('timer').innerText = 'Time Remaining: ' + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }
        }, 1000);
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    window.addEventListener('beforeunload', function (event) {
        // Increment the exit count in the database
        fetch('update_exit_count.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: <?php echo $user_id; ?> })
        });
    });
</script>
</body>
</html>
