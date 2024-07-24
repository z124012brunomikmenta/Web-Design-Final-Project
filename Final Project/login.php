<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="wide-container">
    <div class="welcome-section">
        <div class="welcome-content">
            <h1>Welcome Back!</h1>
            <img src="assets/logo.png" alt="Website Logo" class="logo">
            <h2>Dive back into the gaming world!</h2>
            <p>See the latest rankings, updates, and stats! Ready to continue your journey? Log in and explore what’s new!</p>
        </div>
    </div>
    <div class="form-section">
        <div class="form-container">
            <h2>Log In</h2>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <div class="input-icon">
                        <img src="assets/user-data-icon.svg" alt="Username Icon">
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <img src="assets/envelope-line-icon.svg" alt="Email Icon">
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-icon">
                        <img src="assets/lock-line-icon.svg" alt="Password Icon">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>
                <button type="submit">Log In</button>
            </form>
            <p>Don't have an account yet?<p>
            <p><a href="register.php">Register here</a></p>
        </div>
    </div>
</div>

    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $conn = new mysqli('localhost', 'root', '', 'games_database');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    setcookie('userID', $user['userID'], time() + (86400 * 30), "/"); // 30 days
                    setcookie('userName', $user['userName'], time() + (86400 * 30), "/");
                    header('Location: index.php');
                } else {
                    echo "Invalid password.";
                }
            } else {
                echo "No user found with this email.";
            }
            
            $conn->close();
        }
    ?>
</body>
</html>
