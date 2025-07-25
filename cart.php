<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$totalPrice = 0;
error_log("Cart session data: " . print_r($_SESSION['cart'], true));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="cart.php" class="active">Cart</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <section class="checkout">
        <h2>Your Cart</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <?php
                        $itemTotal = $item['price'] * $item['quantity'];
                        $totalPrice += $itemTotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($itemTotal, 2); ?></td>
                            <td>
                                <a href="remove_from_cart.php?key=<?php echo $index; ?>" class="btn">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total: $<?php echo number_format($totalPrice, 2); ?></h3>
            <a href="checkout.php" class="btn">Proceed to Checkout</a>
        <?php endif; ?>
    </section>

    <footer>
        <p>© 2025 Game Shop. All Rights Reserved.</p>
    </footer>
</body>
</html>