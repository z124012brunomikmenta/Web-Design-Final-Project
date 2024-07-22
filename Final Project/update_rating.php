<?php
header('Content-Type: application/json');
session_start();

if (!isset($_COOKIE['userID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_COOKIE['userID'];
$data = json_decode(file_get_contents('php://input'), true);
$game_id = $data['gameId'];
$rating = $data['rating'];

$conn = new mysqli('localhost', 'root', '', 'games_database');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// Check if the game is already in the user's list
$sql = "SELECT * FROM gamesUserList WHERE gameID = ? AND userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $game_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing rating
    $update_sql = "UPDATE gamesUserList SET gameUserRating = ? WHERE gameID = ? AND userID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('iii', $rating, $game_id, $user_id);
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update rating']);
    }
} else {
    // Insert new rating
    $insert_sql = "INSERT INTO gamesUserList (gameID, userID, gameUserRating, statusInList) VALUES (?, ?, ?, 1)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param('iii', $game_id, $user_id, $rating);
    if ($insert_stmt->execute()) {
        // Update gamePlayCount
        $count_sql = "UPDATE gamesMainList SET gamePlayCount = (SELECT COUNT(*) FROM gamesUserList WHERE gameID = ? AND statusInList = 1) WHERE gameID = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param('ii', $game_id, $game_id);
        $count_stmt->execute();
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add rating']);
    }
}

$conn->close();
?>
