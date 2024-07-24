<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="wide-container">
    <div class="welcome-section">
        <div class="welcome-content">
            <h1>Welcome to GameRanker!</h1>
            <img src="assets/logo.png" alt="Website Logo" class="logo">
            <h2>Join GameRanker Today!</h2>
            <p>Become part of our growing community! Ready to begin your journey? Sign up and discover the best games now!</p>
        </div>
    </div>
    <div class="form-section">
        <div class="form-container">
            <h2>Register</h2>
            <form action="register.php" method="POST">
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
                <button type="submit">Register</button>
            </form>
            <p>Already have an account?<p>
            <p><a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $conn = new mysqli('localhost', 'root', '', 'games_database');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "INSERT INTO users (userName, email, password) VALUES ('$username', '$email', '$password')";
            if ($conn->query($sql) === TRUE) {
                echo "Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            
            $conn->close();
        }
    ?>
</body>
</html>
