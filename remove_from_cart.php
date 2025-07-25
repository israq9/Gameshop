<?php
session_start();

// Get the index of the item to remove
$key = $_GET['key'];

// Remove the item from the cart
if (isset($_SESSION['cart'][$key])) {
    unset($_SESSION['cart'][$key]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
}

// Redirect back to the cart page
header('Location: cart.php');
exit;
?>
