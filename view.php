<?php
require_once 'config.php'; // Adjust path as necessary

if (!isset($_GET['certificate_id'])) {
    die("Certificate ID not specified.");
}

$certificate_id = $_GET['certificate_id'];

// Fetch certificate details including user details
$stmt = $conn->prepare("
    SELECT u.username, cr.title AS course_title
    FROM certificates c
    JOIN users u ON c.user_id = u.id
    JOIN courses cr ON c.course_id = cr.id
    WHERE c.id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $certificate_id);
    $stmt->execute();
    $stmt->bind_result($username, $course_title);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Error fetching certificate details: " . $conn->error);
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Appreciation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Roboto", sans-serif;
            background: white;
            padding: 50px 0;
        }

        .certificate-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .certificate {
            position: relative;
            width: 80%;
            max-width: 800px;
            border: 5px solid #333;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            background-color: white;
            background-image: url('g.jpg'); /* Adjust the image path as necessary */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .certificate-content h2 {
            font-size: 1.5em;
            color: #721a1a;
            margin-bottom: 10px;
        }

        .certificate-content h1 {
            font-size: 2.5em;
            color: #2a4e70;
            margin-bottom: 20px;
        }

        .certificate-content p {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 10px;
        }

        .certificate-content p strong {
            color: #333;
        }

        .certificate-content .event {
            font-size: 1.8em;
            color: #2a4e70;
            margin-bottom: 20px;
        }

        .signature {
            margin-top: 30px;
            font-size: 1.2em;
            color: #333;
        }

        .download-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2a4e70;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .download-btn:hover {
            background-color: #1a3e50;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div id="certificate" class="certificate">
            <div class="certificate-content">
                <h2 class="event">Talentevent</h2> <!-- Event Name -->
                <h2 style="color:#2a4e70;">CERTIFICATE OF APPRECIATION</h2>
                <p>Proudly Presented To</p>
                <h1><?php echo htmlspecialchars($username); ?></h1>
                <p>For successfully completing the course:</p>
                <p><strong>Course Name:</strong> <?php echo htmlspecialchars($course_title); ?></p>
                <p>with outstanding performance.</p>
                <p>at</p>
                <p><strong>Aditya Institute of Technology and Management</strong></p>
                <div class="signature">
                    <p>Nageswar Rao Sir, Director</p>
                </div>
                <p><strong>Certificate ID:</strong> <?php echo htmlspecialchars($certificate_id); ?></p>
            </div>
        </div>
        <button class="download-btn" onclick="downloadCertificate()">Download Certificate</button>
    </div>

    <script>
        function downloadCertificate() {
            const certificate = document.getElementById('certificate');
            html2canvas(certificate).then(canvas => {
                const link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = 'certificate.png';
                link.click();
            });
        }
    </script>
</body>
</html>
