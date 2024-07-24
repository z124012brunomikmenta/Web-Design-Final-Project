<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include 'banner.php'; ?>

    <div class="container">
        <?php
            if (!isset($_COOKIE['userID'])) {
                header('Location: login.php');
                exit();
            }

            $userID = $_COOKIE['userID'];
            $conn = new mysqli('localhost', 'root', '', 'games_database');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Build genre filter query
            $selected_genres = isset($_GET['genres']) ? $_GET['genres'] : [];
            $selected_statuses = isset($_GET['statuses']) ? $_GET['statuses'] : [];
            
            $genre_filter = '';
            if (!empty($selected_genres)) {
                $genre_conditions = [];
                foreach ($selected_genres as $genre) {
                    $genre_conditions[] = "gml.gameGenres = '$genre'";
                }
                $genre_filter = ' AND (' . implode(' OR ', $genre_conditions) . ')';
            }

            $status_filter = '';
            if (!empty($selected_statuses)) {
                $status_conditions = [];
                foreach ($selected_statuses as $status) {
                    $status_conditions[] = "gul.statusInList = '$status'";
                }
                $status_filter = ' AND (' . implode(' OR ', $status_conditions) . ')';
            }

            $sql = "SELECT gml.gameID,
            gml.gameTitle,
            gml.gameReleaseYear,
            gml.gameGenres,
            gml.gameOverallRating,
            gul.listID,
            gul.gameUserRating,
            gul.reviewComment,
            gul.statusInList 
            FROM gamesMainList gml
            JOIN gamesUserList gul
            ON gml.gameID = gul.gameID
            WHERE gul.userID = '$userID' $genre_filter $status_filter
            ORDER BY gameTitle ASC";
            $userGames = $conn->query($sql);
        ?>

        <aside class="filters">
            <h2>Filters</h2>
            <?php
            echo '<form action="profile.php" method="GET">';

            echo '<div class="status-filters">';
            echo '<h3>Status</h3>';
            $statuses = [
                1 => 'Completed',
                2 => 'Playing',
                3 => 'Plan To Play'
            ];
            foreach ($statuses as $status_value => $status_label) {
                $checked = in_array($status_value, $selected_statuses) ? 'checked' : '';
                echo '<label>';
                echo '<input type="checkbox" name="statuses[]" value="' . $status_value . '" ' . $checked . '>';
                echo ' <strong>' . $status_label . '</strong>';
                echo '</label><br>';
            }
            echo '</div>';

            $sql = "SELECT DISTINCT gml.gameGenres 
                    FROM gamesMainList gml
                    JOIN gamesUserList gul ON gml.gameID = gul.gameID 
                    WHERE gul.userID = '$userID'";
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $genres[] = $row['gameGenres'];
                }
            }
            sort($genres);
            echo '<div class="genre-filters">';
            echo '<h3>Genres</h3>';
            foreach($genres as $genre){
                $checked = in_array($genre, $selected_genres) ? 'checked' : '';
                echo '<label>';
                echo '<input type="checkbox" name="genres[]" value="'. $genre . '" ' . $checked . '>';
                echo ' <strong>' . $genre . '</strong>';
                echo '</label><br>';
            }
            echo '</div>';

            echo '<button type="submit" class="apply-button">Apply</button>';
            echo '</form>';
            ?>
        </aside>

        <div class="game-list">
            <?php
                if ($userGames->num_rows > 0) {
                    $rank = 0;
                    while($row = $userGames->fetch_assoc()) {
                        $rank++;
                        $game_id = $row["gameID"];
                        $image_path = "assets/game-image/$game_id.png";
                        $status_class = '';
                        switch ($row["statusInList"]) {
                            case 1:
                                $status_class = 'status-completed';
                                break;
                            case 2:
                                $status_class = 'status-playing';
                                break;
                            case 3:
                                $status_class = 'status-plan-to-play';
                                break;
                        }
                        echo '<div class="game-item" data-game-id="' . $game_id . '">';
                            echo '<div class="game-image"><img src="' . $image_path . '" alt="' . $row["gameTitle"] . '" onerror="this.onerror=null;this.src=\'assets/game-image/placeholder.png\';"></div>';
                            echo '<div class="game-details">';
                                echo '<div class="game-rating">' . "Your rating: " . $row["gameUserRating"] . '</div>';
                                echo '<div class="game-title">' . $rank . ". " .$row["gameTitle"] . ' (' . $row["gameReleaseYear"] . ')</div>';
                                echo '<div class="game-genres">' . $row["gameGenres"] . '</div>';
                            echo '</div>';
                            echo '<div class="game-comments">';
                                echo $row['reviewComment'];
                                echo '<button class="add-comment-button">Add Comment</button>';
                            echo '</div>';
                            echo '<div class="status-dropdown">';
                                echo '<button class="status-button ' . $status_class . '">Status</button>';
                                echo '<div class="status-menu">';
                                    echo '<button class="status-option" data-status="1">Completed</button>';
                                    echo '<button class="status-option" data-status="2">Playing</button>';
                                    echo '<button class="status-option" data-status="3">Plan To Play</button>';
                                    echo '<button class="status-option" data-status="remove">Remove entry</button>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "No games found.";
                }

                $conn->close();
            ?>
        </div>
    </div>

    <script>
document.querySelectorAll('.add-comment-button').forEach(button => {
    button.addEventListener('click', function() {
        const gameItem = button.closest('.game-item');
        let currentCommentNode = button.previousSibling;
        const currentComment = currentCommentNode ? currentCommentNode.textContent.trim() : '';
        const commentBox = document.createElement('textarea');
        commentBox.classList.add('comment-box');
        commentBox.value = currentComment;

        // Hide the button
        button.style.display = 'none';

        if (currentCommentNode) {
            button.parentNode.replaceChild(commentBox, currentCommentNode);
        } else {
            button.parentNode.insertBefore(commentBox, button);
        }
        commentBox.focus();

        const saveComment = () => {
            const newComment = commentBox.value;
            const gameId = gameItem.dataset.gameId;

            // AJAX request to update the review comment in the database
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_comment.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("gameID=" + gameId + "&reviewComment=" + encodeURIComponent(newComment));

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (newComment) {
                        const newCommentNode = document.createTextNode(newComment);
                        commentBox.replaceWith(newCommentNode);
                        button.parentNode.insertBefore(newCommentNode, button);
                    } else {
                        commentBox.remove();
                    }
                    // Show the button again
                    button.style.display = '';
                    button.textContent = 'Add Comment';
                }
            };
        };

        commentBox.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                saveComment();
            } else if (event.key === 'Escape') {
                commentBox.replaceWith(document.createTextNode(currentComment));
                // Show the button again
                button.style.display = '';
                button.textContent = 'Add Comment';
            }
        });
    });
});

document.querySelectorAll('.status-button').forEach(button => {
    button.addEventListener('click', function() {
        const statusMenu = button.nextElementSibling;
        statusMenu.classList.toggle('show');
    });
});

document.querySelectorAll('.status-option').forEach(option => {
    option.addEventListener('click', function() {
        const gameItem = option.closest('.game-item');
        const gameId = gameItem.dataset.gameId;
        const userId = <?php echo $userID; ?>;
        const status = option.dataset.status;
        const statusButton = gameItem.querySelector('.status-button');

        // AJAX request to update the status in the database
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("gameID=" + gameId + "&userID=" + userId + "&status=" + status);

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (status === 'remove') {
                    gameItem.remove();
                } else {
                    // Update the button class based on the new status
                    statusButton.classList.remove('status-completed', 'status-playing', 'status-plan-to-play');
                    switch (status) {
                        case '1':
                            statusButton.classList.add('status-completed');
                            break;
                        case '2':
                            statusButton.classList.add('status-playing');
                            break;
                        case '3':
                            statusButton.classList.add('status-plan-to-play');
                            break;
                    }
                    alert('Status updated successfully');
                }
            }
        };

        // Hide the dropdown menu
        option.parentNode.classList.remove('show');
    });
});

// Hide the status menu if clicked outside
document.addEventListener('click', function(event) {
    if (!event.target.matches('.status-button')) {
        document.querySelectorAll('.status-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

</script>
</body>
</html>
