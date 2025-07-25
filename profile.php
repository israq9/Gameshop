<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('database.php');

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("User not found.");
}

$order_query = "SELECT * FROM orders WHERE user_id = ?";
$order_stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($order_stmt, 'i', $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);

// Use session-based cart
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
error_log("Profile page - Cart contents: " . print_r($cart_items, true));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="profile.php" class="active">Profile</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <section class="profile">
        <h2>Your Profile</h2>
        <p>Name: <?= isset($user['username']) ? htmlspecialchars($user['username']) : 'Not Available' ?></p>
        <p>Email: <?= isset($user['email']) ? htmlspecialchars($user['email']) : 'Not Available' ?></p>
        
        <h3>Your Orders</h3>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($order_result)) : ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td>$<?= number_format($order['total_price'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <h3>Your Cart</h3>
        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty. <?php if (!empty($order_result) && mysqli_num_rows($order_result) > 0) { echo "It may have been cleared after your recent order."; } ?></p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <footer>
        <p>© 2025 Game Shop. All Rights Reserved.</p>
    </footer>
</body>
</html>