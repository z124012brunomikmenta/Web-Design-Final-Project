<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gameID = $_POST['gameID'];
    $userID = $_POST['userID'];
    $status = $_POST['status'];

    $conn = new mysqli('localhost', 'root', '', 'games_database');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($status === 'remove') {
        $sql = "DELETE FROM gamesUserList WHERE gameID = ? AND userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $gameID, $userID);
    } else {
        $sql = "UPDATE gamesUserList SET statusInList = ? WHERE gameID = ? AND userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iii', $status, $gameID, $userID);
    }

    if ($stmt->execute()) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
