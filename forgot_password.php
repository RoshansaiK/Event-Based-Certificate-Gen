<!-- forgot_password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Forgot Password</title>
</head>
<body>
    <div class="container">
        <div class="login">
            <h1>Forgot Password</h1>
            <form method="post" action="send_reset_link.php">
                <div class="input-box">
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <button type="submit">Send Reset Link</button>
            </form>
        </div>
    </div>
</body>
</html>
<style>
body {
    width: 100%;
    min-height: 100vh;
    box-sizing: border-box;
    font-family: "Open Sans";
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #161623;
}

.container {
    position: relative;
    width: 100%;
    max-width: 400px;
    padding: 20px;
    background-color: #161623;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 10px;
}

.container::before, .container::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    animation: move-up6 2s ease-in infinite alternate-reverse;
}

.container::before {
    width: 200px;
    height: 200px;
    background: #7B66FF;
    top: -50px;
    left: -50px;
}

.container::after {
    width: 150px;
    height: 150px;
    background: #5FBDFF;
    bottom: -50px;
    right: -50px;
}

@keyframes move-up6 {
    to {
        transform: translateY(-20px);
    }
}

.login {
    position: relative;
    width: 100%;
    padding: 20px;
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 15px;
    backdrop-filter: blur(25px);
    box-shadow: 10px 10px 40px rgba(0, 0, 0, 0.2), -10px -10px 40px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.login h1 {
    font-size: 1.5rem;
    color: #fff;
    margin-bottom: 20px;
}

.login form {
    width: 100%;
}

.login form .input-box {
    width: 100%;
    position: relative;
    margin-bottom: 20px;
}

.login form .input-box input {
    width: 100%;
    border: none;
    padding: 0.8rem;
    border-radius: 10px;
    color: #fff;
    background-color: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.4);
}

.login form .input-box input::placeholder {
    color: #fff;
    font-size: 0.8rem;
}

.login form button {
    width: 100%;
    border: none;
    padding: 0.8rem;
    border-radius: 10px;
    color: #fff;
    background-color: #161623;
    border: 1px solid rgba(255, 255, 255, 0.4);
    transition: 0.2s ease;
    cursor: pointer;
}

.login form button:hover {
    background-color: #7b66ff;
}

.login form button:focus {
    background-color: #7b66ff;
    border: 1px solid #7b66ff;
}

.login form .links {
    width: 100%;
    text-align: center;
}

.login form .links a {
    color: #fff;
    font-size: 0.8rem;
    margin: 0 5px;
}
</style>
