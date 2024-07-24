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
            $genre_filter = '';
            if (!empty($selected_genres)) {
                $genre_conditions = [];
                foreach ($selected_genres as $genre) {
                    $genre_conditions[] = "gml.gameGenres = '$genre'";
                }
                $genre_filter = ' AND (' . implode(' OR ', $genre_conditions) . ')';
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
            WHERE gul.userID = '$userID' $genre_filter
            ORDER BY gameTitle ASC";
            $userGames = $conn->query($sql);
        ?>

        <aside class="filters">
            <h2>Filters</h2>
            <?php
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
            echo '<br>';
            echo '<form action="profile.php" method="GET">';
            foreach($genres as $genre){
                $checked = in_array($genre, $selected_genres) ? 'checked' : '';
                echo'<label>';
                echo '<input type=checkbox name="genres[]" value="'. $genre . '" ' . $checked . '>';
                echo ' <strong>' . $genre . '</strong>';
                echo '</label><br>';
            }
            echo '<button type="submit" class="apply-button">Apply</button>';
            echo '</form>';
            ?>
        </aside>

        <div class="game-list">
            <?php
            if ($userGames->num_rows > 0) {
                while($row = $userGames->fetch_assoc()) {
                    $game_id = $row["gameID"];
                    $image_path = "assets/game-image/$game_id.png";
                    echo '<div class="game-item">';
                        echo '<div class="game-image"><img src="' . $image_path . '" alt="' . $row["gameTitle"] . '" onerror="this.onerror=null;this.src=\'assets/game-image/placeholder.png\';"></div>';
                        echo '<div class="game-details">';
                            echo '<div class="game-title">' .$row["gameTitle"] . ' (' . $row["gameReleaseYear"] . ')</div>';
                            echo '<div class="game-rating">' . "Your rating: " . $row["gameUserRating"] . '</div>';
                            echo '<div class="game-genres">' . $row["gameGenres"] . '</div>';
                        echo '</div>';
                        echo '<div class="game-comments">';
                            echo $row['reviewComment'];
                            if($row['reviewComment'] == NULL){
                                echo '<button class="add-comment-button">Add Comment</button>';
                            }
                        echo '</div>';
                        echo '<form action="remove_entry.php" method="post">';
                        echo '<input type="hidden" name="game_id" value="'. $game_id .'">';
                        echo '<input type="hidden" name="user_id" value="'. $userID .'">';
                        echo '<button type="submit" class="remove-from-list-button">Remove Entry</button>';
                        echo '</form>';
                    echo '</div>';
                }
            } else {
                    echo "No games found.";
                }

                $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
