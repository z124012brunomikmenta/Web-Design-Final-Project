<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Game List</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Banner Section -->
    <?php include 'banner.php'; ?>

    <!-- Main Content -->
    <div class="container">
        <!-- Filters Banner -->
        <aside class="filters">
            <!-- Search Bar Section -->
            <div class="search-bar">
                <form action="community-list.php" method="GET">
                    <input type="text" name="search" placeholder="Search games..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            <h2>Filters</h2>
            <!-- Add filter options here -->
        </aside>
        
        <!-- Game List Section -->
        <main class="game-list">
            <?php
                // Database connection
                $conn = new mysqli('localhost', 'root', '', 'games_database');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Determine sorting method
                $sort_method = isset($_GET['sort']) ? $_GET['sort'] : 'highest-rated';
                $order_by = $sort_method == 'most-popular' ? 'gamePlayCount DESC' : 'gameOverallRating DESC';

                // Get search term
                $search_term = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

                // Fetch games from the database with search filter
                $sql = "SELECT * FROM gamesMainList WHERE gameTitle LIKE '%$search_term%' ORDER BY $order_by";
                $result = $conn->query($sql);

                // Check if user is logged in
                session_start();
                $user_id = isset($_COOKIE['userID']) ? $_COOKIE['userID'] : null;

                if ($result->num_rows > 0) {
                    $rank = 0;
                    while($row = $result->fetch_assoc()) {
                        $rank++;
                        $game_id = $row["gameID"];
                        $image_path = "assets/game-image/$game_id.png";

                        // Check if the game is in the user's list
                        $user_score = null;
                        if ($user_id) {
                            $user_sql = "SELECT gameUserRating, statusInList  FROM gamesUserList WHERE gameID = $game_id AND userID = $user_id";
                            $user_result = $conn->query($user_sql);
                            if ($user_result->num_rows > 0) {
                                $user_row = $user_result->fetch_assoc();
                                $user_score = $user_row['gameUserRating'];
                                $user_status = $user_row['statusInList'];
                            }
                        }

                        echo '<div class="game-item">';
                            echo '<div class="game-image"><img src="' . $image_path . '" alt="' . $row["gameTitle"] . '" onerror="this.onerror=null;this.src=\'assets/game-image/placeholder.png\';"></div>';
                            echo '<div class="game-details">';
                                echo '<div class="game-rating">' . "Score: " . $row["gameOverallRating"] . '</div>';
                                echo '<div class="game-title">' . $rank . ". " . $row["gameTitle"] . ' (' . $row["gameReleaseYear"] . ')</div>';
                                echo '<div class="game-genres">' . $row["gameGenres"] . '</div>';
                                echo '<div class="game-play-count">' . "Players: " . $row["gamePlayCount"] . '</div>';
                            echo '</div>';
                            echo '<div class="user-score">';
                                $button_text = $user_score !== null ? $user_score : '+';
                                echo '<button class="score-button" data-game-id="' . $game_id . '">' . $button_text . '</button>';
                            echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "No games found.";
                }

                $conn->close();
            ?>
        </main>
    </div>

    <!-- JavaScript for dropdown functionality -->
    <script>
    document.querySelectorAll('.score-button').forEach(button => {
        button.addEventListener('click', function() {
            // Check if dropdown already exists
            let dropdown = button.querySelector('.dropdown');

            // If it exists, toggle its visibility
            if (dropdown) {
                dropdown.classList.toggle('show-dropdown');
            } else {
                // Create dropdown menu
                dropdown = document.createElement('div');
                dropdown.classList.add('dropdown');

                for (let i = 10; i > 0; i--) {
                    const option = document.createElement('div');
                    option.classList.add('dropdown-option');
                    option.innerText = i;
                    option.addEventListener('click', function() {
                        const gameId = button.getAttribute('data-game-id');
                            const userId = <?php echo $user_id ? $user_id : 'null'; ?>;
                            if (userId) {
                                // Update user rating in the database
                                fetch('update_rating.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        userId: userId,
                                        gameId: gameId,
                                        rating: i
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        button.innerText = i;
                                    } else {
                                        alert('Failed to update rating.');
                                    }
                                });
                            } else {
                                alert('You must be logged in to rate games.');
                            }
                            dropdown.classList.remove('show-dropdown');
                    });
                    dropdown.appendChild(option);
                }

                button.appendChild(dropdown);

                // Use a timeout to ensure the transition effect applies correctly
                setTimeout(() => {
                    dropdown.classList.add('show-dropdown');
                }, 10);
            }
        });

        // Close the dropdown if clicked outside
        document.addEventListener('click', function(event) {
            if (!button.contains(event.target)) {
                let dropdown = button.querySelector('.dropdown');
                if (dropdown) {
                    dropdown.classList.remove('show-dropdown');
                }
            }
        });
    });
</script>

</body>
</html>
