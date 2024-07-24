<?php
    // Clear the cookies
    setcookie('userID', '', time() - 3600, '/');
    setcookie('userName', '', time() - 3600, '/');
    setcookie('profilePicture', '', time() - 3600, '/');
    
    // Redirect to home page
    header('Location: index.php');
    exit();
?>
