<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'matt') {
    header('Location: admin_login.php');
    exit;
}

include('database.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discount = isset($_POST['discount']) ? $_POST['discount'] : null;

    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = 'images/' . basename($image_name);

    if (!file_exists('images')) {
        mkdir('images', 0777, true);
    }

    if (move_uploaded_file($image_tmp, $image_path)) {
        $query = "INSERT INTO games (name, genre, description, price, discount, image_url) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssssds', $name, $genre, $description, $price, $discount, $image_path);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Game added successfully!";
        } else {
            $message = "Error adding game!";
        }
    } else {
        $message = "Error uploading image! Check permissions on the 'images/' directory.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Game</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="add_game.php" class="active">Add Game</a></li>
            <li><a href="view_orders.php">View Orders</a></li>
            <li><a href="add_promotion.php">Add Promotion</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="admin-dashboard">
        <h1>Add New Game</h1>
        <?php if ($message): ?>
            <p style="color: <?php echo strpos($message, 'Error') !== false ? 'red' : 'green'; ?>;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <form action="add_game.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Game Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="discount">Discount (Optional):</label>
                <input type="number" id="discount" name="discount" step="0.01">
            </div>

            <div class="form-group">
                <label for="image">Game Image:</label>
                <input type="file" id="image" name="image" required>
            </div>

            <div class="form-group">
                <button type="submit">Add Game</button>
            </div>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 Game Shop. All Rights Reserved.</p>
    </footer>
</body>
</html>