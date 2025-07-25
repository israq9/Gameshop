<?php
session_start();

include('database.php');

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'matt') {
    header('Location: admin_login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $promotion_code = $_POST['promotion_code'];
    $discount_percentage = $_POST['discount_percentage'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $image_name = $_FILES['promotion_image']['name'];
    $image_tmp = $_FILES['promotion_image']['tmp_name'];
    $image_path = 'images/' . basename($image_name);

    if (!file_exists('images')) {
        mkdir('images', 0777, true);
    }

    mysqli_begin_transaction($conn);

    try {
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Insert promotion
            $query = "INSERT INTO promotions (code, discount_percentage, valid_from, valid_to, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssdss', $promotion_code, $discount_percentage, $start_date, $end_date, $image_path);
            mysqli_stmt_execute($stmt);

            // Apply discount to all games
            $query = "UPDATE games SET discount = ? WHERE discount IS NULL OR discount < ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'dd', $discount_percentage, $discount_percentage);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);
            $message = "Promotion added successfully and applied to all games!";
        } else {
            throw new Exception("Error uploading promotion image!");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Error adding promotion: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Promotion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="add_game.php">Add Game</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
            <li><a href="add_promotion.php" class="active">Add Promotion</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="admin-dashboard">
        <h1>Add Promotion</h1>
        <?php if ($message): ?>
            <p style="color: <?php echo strpos($message, 'Error') !== false ? 'red' : 'green'; ?>;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <form action="add_promotion.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="promotion_code">Promotion Code:</label>
                <input type="text" id="promotion_code" name="promotion_code" required>
            </div>

            <div class="form-group">
                <label for="discount_percentage">Discount Percentage:</label>
                <input type="number" id="discount_percentage" name="discount_percentage" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required>
            </div>

            <div class="form-group">
                <label for="promotion_image">Promotion Image:</label>
                <input type="file" id="promotion_image" name="promotion_image" required>
            </div>

            <div class="form-group">
                <button type="submit">Add Promotion</button>
            </div>
        </form>
    </div>

    <footer>
        <p>© 2025 Game Shop. All Rights Reserved.</p>
    </footer>
</body>
</html>