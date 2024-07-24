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
    <div class="form-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account yet? <a href="register.php">Register here</a></p>
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
