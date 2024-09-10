<!-- reset_password.php -->
<?php
if (!isset($_GET['token'])) {
    die("Invalid request");
}
$token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Reset Password</title>
    <style>
        body {
            width: 100%;
            min-height: 100vh;
            box-sizing: border-box;
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #161623;
        }

        .container {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #161623;
            overflow: hidden;
        }

        .container::before, .container::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: #7B66FF;
            animation: move-up6 2s ease-in infinite alternate-reverse;
        }

        .container::before {
            width: 30vw;
            height: 30vw;
        }

        .container::after {
            width: 20vw;
            height: 20vw;
            background: #5FBDFF;
        }

        @keyframes move-up6 {
            to {
                transform: translateY(-50px);
            }
        }

        .login {
            position: relative;
            width: 90%;
            max-width: 400px;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            z-index: 10;
            backdrop-filter: blur(25px);
            box-shadow: 10px 10px 40px rgba(0, 0, 0, 0.2), -10px -10px 40px rgba(0, 0, 0, 0.2);
        }

        .login h1 {
            font-size: 1.5rem;
            color: #fff;
            margin: 0 0 20px 0;
            text-align: center;
        }

        .login form {
            display: flex;
            flex-direction: column;
        }

        .login form .input-box {
            width: 100%;
            position: relative;
            margin-bottom: 20px;
            display: flex;
        }

        .login form .input-box input {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.4);
            outline: none;
        }

        .login form .input-box input::placeholder {
            color: #fff;
            font-size: 0.8rem;
            transition: 0.5s ease;
        }

        .login form .input-box input:focus::placeholder {
            opacity: 0;
        }

        .login form button {
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            color: #fff;
            background-color: #161623;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: 0.2s ease;
            outline: none;
        }

        .login form button:hover {
            background-color: #7b66ff;
            cursor: pointer;
        }

        .login form button:focus {
            background-color: #7b66ff;
            border: 1px solid #7b66ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login">
            <h1>Reset Password</h1>
            <form method="post" action="update_password.php">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="input-box">
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
