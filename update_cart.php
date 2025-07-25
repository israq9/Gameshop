<?php
session_start();

// Ensure session exists and user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if the user is not logged in
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user_id is saved in session after login
$game_id = $_POST['game_id']; // The ID of the game to remove

// Database connection
include('database.php');

// Remove the game from the cart
$query = "DELETE FROM cart WHERE user_id = ? AND game_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ii', $user_id, $game_id);
mysqli_stmt_execute($stmt);

header('Location: cart.php'); // Redirect back to cart page
exit;
?>
