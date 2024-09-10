<?php
session_start();
require_once 'config.php'; // Adjust the path as necessary

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$search_query = '';
$certificates = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = $_POST['search_query'];

    // Fetch certificates and user details based on search query (certificate ID)
    $stmt = $conn->prepare("
        SELECT c.id as certificate_id, c.course_id, c.issued_date, u.id as user_id, u.username, u.email, cr.title 
        FROM certificates c
        JOIN users u ON c.user_id = u.id
        JOIN courses cr ON c.course_id = cr.id
        WHERE c.certificate_id LIKE CONCAT('%', ?, '%')
    ");
    if ($stmt) {
        $stmt->bind_param("s", $search_query);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $certificates[] = $row;
        }
        $stmt->close();
    } else {
        die("Error fetching certificates: " . $conn->error);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Certificates</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
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
        .certificates-box {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .certificate-item {
            margin-bottom: 20px;
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
        .btn-download {
            color: #fff;
        }
        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1><span>Search Certificates</span></h1>
</div>

<div class="container">
    <form method="POST" class="search-form">
        <div class="form-group">
            <label for="search_query">Search by Certificate ID:</label>
            <input type="text" name="search_query" id="search_query" class="form-control" value="<?php echo htmlspecialchars($search_query); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if (!empty($certificates)): ?>
        <div class="certificates-box">
            <?php foreach ($certificates as $certificate): ?>
                <div class="certificate-item">
                    <h4>Course: <?php echo htmlspecialchars($certificate['title']); ?></h4>
                    <p>Certificate ID: <?php echo htmlspecialchars($certificate['certificate_id']); ?></p>
                    <p>Issued Date: <?php echo htmlspecialchars($certificate['issued_date']); ?></p>
                    <p>User: <?php echo htmlspecialchars($certificate['username']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($certificate['email']); ?></p>
                    <a href="view.php?certificate_id=<?php echo htmlspecialchars($certificate['certificate_id']); ?>" class="btn btn-info">View</a>
                    
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p>No results found for the search query.</p>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
