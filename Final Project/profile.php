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
            WHERE userID = '$userID'
            ORDER BY gameUserRating DESC";
            $userGames = $conn->query($sql);
        ?>

        <aside class="filters">
            <h2>Filters</h2>
            <!-- Add filter options here -->
        </aside>

        <div class="game-list">
            <?php
                if ($userGames->num_rows > 0) {
                    $rank = 0;
                    while($row = $userGames->fetch_assoc()) {
                        $rank++;
                        $game_id = $row["gameID"];
                        $image_path = "assets/game-image/$game_id.png";
                        echo '<div class="game-item">';
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
