<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gameID = $_POST['gameID'];
    $reviewComment = $_POST['reviewComment'];

    $conn = new mysqli('localhost', 'root', '', 'games_database');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE gamesUserList SET reviewComment = ? WHERE gameID = ? AND userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $reviewComment, $gameID, $_COOKIE['userID']);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
