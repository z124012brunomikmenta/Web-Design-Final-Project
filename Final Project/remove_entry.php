<?php


$dsn = 'mysql:host=localhost;dbname=games_database';
$username = 'root';
$password = '';
$pdo = new PDO($dsn, $username, $password);

$game_id = filter_input(INPUT_POST, 'game_id', FILTER_VALIDATE_INT);
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);


if ($game_id != false && $user_id != false){
    $query = 'DELETE FROM gamesUserList
                WHERE gameID = :game_id AND userID = :user_id';
    $statement = $pdo->prepare($query);
    $statement->bindValue(':game_id', $game_id);
    $statement->bindvalue(':user_id', $user_id);
    $statement->execute();
    $update_count_sql = "UPDATE gamesMainList SET gamePlayCount = (SELECT COUNT(*) FROM gamesUserList WHERE gameID = ? AND statusInList = 1) WHERE gameID = ?";
    $statement = $pdo->prepare($update_count_sql);
    $statement->bindValue(1, $game_id);
    $statement->bindValue(2, $game_id);
    $statement->execute();
}
include('profile.php');
?>