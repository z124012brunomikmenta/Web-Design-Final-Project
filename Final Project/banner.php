<?php
    $isLoggedIn = isset($_COOKIE['userID']);
    $profilePicture = 'assets/profile.png';
?>
<header>
    <div class="header-content">
        <div class="logo">
            <a href="index.php"><img src="assets/logo.png" alt="Website Logo"></a>
        </div>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-toggle-label">
            <img src="assets/three-horizontal-lines-icon.svg" alt="Menu" class="nav-icon">
        </label>
        <nav class="nav-cont">
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="community-list.php?sort=highest-rated">Highest Rated</a></li>
                <li><a href="community-list.php?sort=most-popular">Most Popular</a></li>
                <li>
                    <div class="profile">
                        <?php if ($isLoggedIn): ?>
                            <a href="logout.php" class="logout-button">Logout</a>
                            <a href="profile.php"><img src="<?php echo $profilePicture; ?>" alt="Profile"></a>
                        <?php else: ?>
                            <a href="login.php" class="login-button">Login</a>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
