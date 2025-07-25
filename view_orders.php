<?php
session_start();
include('database.php');

// Check if admin is logged in
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'matt') {
    error_log("Admin access denied: No valid session for username 'matt'");
    header('Location: admin_login.php');
    exit;
}

// Check if the order_id is set in the URL
$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if ($order_id) {
    // Get the order details with user information
    $query = "SELECT o.*, u.email, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    $order_result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($order_result);

    if (!$order) {
        error_log("Order not found for order_id: $order_id");
        echo "Order not found!";
        exit;
    }

    // Get the items in the order
    $query = "SELECT oi.*, g.name FROM order_items oi JOIN games g ON oi.game_id = g.id WHERE oi.order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    $order_items_result = mysqli_stmt_get_result($stmt);
    $num_items = mysqli_num_rows($order_items_result);
    error_log("Order items found for order_id $order_id: $num_items items");
} else {
    // Fetch all orders for admin to select from
    $query = "SELECT o.id, o.order_date, u.username, o.total_price FROM orders o JOIN users u ON o.user_id = u.id";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $orders_result = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="add_game.php">Add Game</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
            <li><a href="add_promotion.php">Add Promotion</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="admin-dashboard">
        <?php if ($order_id): ?>
            <h2>Order #<?php echo $order['id']; ?> Details</h2>
            <p>User: <?php echo htmlspecialchars($order['username'] ?: $order['email']); ?></p>
            <p>Date: <?php echo $order['order_date']; ?></p>
            <p>Total Price: $<?php echo number_format($order['total_price'], 2); ?></p>

            <h3>Order Items</h3>
            <?php if (mysqli_num_rows($order_items_result) > 0): ?>
                <table class="cart-table">
                    <tr>
                        <th>Game</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php while ($item = mysqli_fetch_assoc($order_items_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No items found for this order.</p>
            <?php endif; ?>
        <?php else: ?>
            <h2>All Orders</h2>
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <table class="cart-table">
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Total Price</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo $order['order_date']; ?></td>
                            <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td><a href="view_orders.php?order_id=<?php echo $order['id']; ?>" class="btn">View Details</a></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No orders found. Please complete an order first.</p>
            <?php endif; ?>
        <?php endif; ?>

        <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
    </div>

    <footer>
        <p>© 2025 Game Shop. All Rights Reserved.</p>
    </footer>
</body>
</html>