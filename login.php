<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['userid'] = $id;
        $_SESSION['role'] = $role;

        if ($role == 1) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7HftUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div class="login">
            <h1>Login Form</h1>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="post" action="login.php">
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class="fa fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fa fa-lock"></i>
                </div>
                <div class="rembar">
                    <input id="rembar" type="checkbox">
                    <label for="rembar">Remember me</label>
                </div>
                <button type="submit">Login</button>
                <div class="links">
                    <a href="forgot_password.php">Forgot password</a>
                    <a href="register.html">You don't have an account</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<style>
body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: "Open Sans", sans-serif;
    background-color: #161623;
    overflow: hidden;
}

.container {
    position: relative;
    width: 100%;
    max-width: 400px;
    padding: 20px;
    background-color: rgba(22, 22, 35, 0.9);
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    text-align: center;
}

.container::before, .container::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    z-index: -1;
}

.container::before {
    width: 400px;
    height: 400px;
    background: #7B66FF;
    top: -200px;
    left: -200px;
    animation: move-up 2s ease-in-out infinite alternate-reverse;
}

.container::after {
    width: 250px;
    height: 250px;
    background: #5FBDFF;
    bottom: -125px;
    right: -125px;
    animation: move-up 2s ease-in-out infinite alternate-reverse;
}

@keyframes move-up {
    to {
        transform: translateY(-50px);
    }
}

.login {
    width: 100%;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.login h1 {
    font-size: 1.8rem;
    color: #fff;
    margin-bottom: 20px;
}

.login form .input-box {
    position: relative;
    margin-bottom: 20px;
}

.login form .input-box input {
    width: 100%;
    padding: 15px 10px 15px 35px;
    border: none;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
}

.login form .input-box input::placeholder {
    color: #ddd;
}

.login form .input-box i {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    color: #fff;
}

.login form .rembar {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.login form .rembar input {
    margin-right: 10px;
}

.login form .rembar label {
    color: #fff;
}

.login form button {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 5px;
    background: #7b66ff;
    color: #fff;
    cursor: pointer;
    transition: background 0.3s ease;
}

.login form button:hover {
    background: #5fbdff;
}

.login form .links {
    display: flex;
    justify-content: space-between;
}

.login form .links a {
    color: #fff;
    font-size: 0.9rem;
    text-decoration: none;
}

.login form .links a:hover {
    text-decoration: underline;
}
</style>
