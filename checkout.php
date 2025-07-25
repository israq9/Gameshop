<?php
session_start();
include('database.php');

// Get cart from session
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user details and place the order
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');

    // Insert order into orders table
    $query = "INSERT INTO orders (user_id, order_date, total_price) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'isd', $user_id, $order_date, $total);
    mysqli_stmt_execute($stmt);

    // Get the last inserted order ID
    $order_id = mysqli_insert_id($conn);

    // Insert order items into order_items table
    foreach ($cart as $item) {
        $game_id = $item['game_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        $query = "INSERT INTO order_items (order_id, game_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iiid', $order_id, $game_id, $quantity, $price);
        mysqli_stmt_execute($stmt);
    }

    // Clear the cart
    unset($_SESSION['cart']);

    // Display payment success message
    echo '<p style="color: green;">Payment Successful!</p>';

    // Redirect to the view order page after a brief delay
    header('Refresh: 2; URL=view_order.php?order_id=' . $order_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h2>Review Your Order</h2>

    <table>
        <tr>
            <th>Game</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        <?php foreach ($cart as $item): ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3">Total</td>
            <td>$<?php echo number_format($total, 2); ?></td>
        </tr>
    </table>

    <form method="POST">
        <button type="submit">Pay Now</button>
    </form>
</body>
</html>