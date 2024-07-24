<?php
    $isLoggedIn = isset($_COOKIE['userID']);
    $profilePicture = 'assets/profile.png';
?>
<header>
    <div class="logo">
        <a href="index.php"><img src="assets/logo.png" alt="Website Logo"></a>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="community-list.php?sort=highest-rated">Highest Rated</a></li>
            <li><a href="community-list.php?sort=most-popular">Most Popular</a></li>
        </ul>
    </nav>
    <div class="profile">
        <?php if ($isLoggedIn): ?>
            <a href="logout.php" class="logout-button">Logout</a>
            <a href="profile.php"><img src="<?php echo $profilePicture; ?>" alt="Profile"></a>
        <?php else: ?>
            <a href="login.php" class="login-button">Login</a>
        <?php endif; ?>
    </div>
</header>
