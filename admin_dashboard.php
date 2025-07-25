<?php
session_start();

// Debug: Check session state
if (isset($_SESSION['admin'])) {
    error_log("Admin_dashboard.php: Admin is logged in, username: " . $_SESSION['username']);
} else {
    error_log("Admin_dashboard.php: No admin logged in.");
}

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'matt') {
    header('Location: admin_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <h1>Admin Dashboard</h1>
        <div class="admin-dashboard-content">
            <div class="card">
                <h3>Manage Games</h3>
                <p>View and add games to the store</p>
                <button onclick="window.location.href='add_game.php'">Add New Game</button>
            </div>

            <div class="card">
                <h3>Manage Orders</h3>
                <p>View all orders placed by users</p>
                <button onclick="window.location.href='view_orders.php'">View Orders</button>
            </div>

            <div class="card">
                <h3>Manage Promotions</h3>
                <p>Create new promotions and discounts</p>
                <button onclick="window.location.href='add_promotion.php'">Add Promotion</button>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Game Shop. All Rights Reserved.</p>
    </footer>
</body>
</html>