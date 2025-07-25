<?php
session_start();
include('database.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to the cart.']);
    exit;
}

$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;

if ($game_id <= 0) {
    error_log("Invalid game_id: " . $game_id);
    echo json_encode(['success' => false, 'message' => 'Invalid game ID.']);
    exit;
}

try {
    $query = "SELECT id, name, price FROM games WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
        exit;
    }

    mysqli_stmt_bind_param($stmt, 'i', $game_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $game = mysqli_fetch_assoc($result);

    if (!$game) {
        error_log("Game not found for id: " . $game_id);
        echo json_encode(['success' => false, 'message' => 'Game not found.']);
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['game_id'] == $game_id) {
            $item['quantity']++;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'game_id' => $game['id'],
            'name' => $game['name'],
            'price' => $game['price'],
            'quantity' => 1
        ];
    }

    error_log("Cart updated, item added: " . $game['name'] . ", session_id: " . session_id());
    echo json_encode(['success' => true, 'message' => 'Item added to cart!', 'cart_count' => count($_SESSION['cart'])]);
} catch (Exception $e) {
    error_log("Add to cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while adding to cart. Please try again.']);
}
exit;
?>