<?php
session_start();
include('database.php');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Default sort option
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$orderBy = "ORDER BY name ASC";

// Determine sorting order
switch ($sort) {
    case 'price_asc':
        $orderBy = "ORDER BY price ASC";
        break;
    case 'price_desc':
        $orderBy = "ORDER BY price DESC";
        break;
    case 'name_asc':
        $orderBy = "ORDER BY name ASC";
        break;
    default:
        $orderBy = "ORDER BY name ASC";
}

// Fetch active promotion
$current_date = date('Y-m-d');
$query = "SELECT * FROM promotions WHERE ? BETWEEN valid_from AND valid_to LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $current_date);
mysqli_stmt_execute($stmt);
$promotion_result = mysqli_stmt_get_result($stmt);
$promotion = mysqli_fetch_assoc($promotion_result);

// Fetch games with sorting and optional search
$query = "SELECT * FROM games";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM games WHERE name LIKE '%$search%'";
}
$query .= " $orderBy";
$games = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Game Shop</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .promotion {
            text-align: center;
            padding: 20px;
            margin: 20px auto;
            max-width: 600px;
            border: 2px solid #ffd700;
            border-radius: 10px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { border-color: #ffd700; }
            50% { border-color: #ffeb3b; }
            100% { border-color: #ffd700; }
        }

        .promotion img {
            max-width: 300px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php" class="active">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section class="welcome">
        <h1>Welcome to the Game Shop</h1>
        <p>Your one-stop shop for the latest and greatest games!</p>
        <a href="index.php" class="btn shop-now">Shop Now</a>
    </section>

    <?php if ($promotion): ?>
        <section class="promotion">
            <h2>Current Promotion: <?= htmlspecialchars($promotion['code']) ?></h2>
            <p>Discount: <?= $promotion['discount_percentage'] ?>%</p>
            <img src="<?= htmlspecialchars($promotion['image_url']) ?>" alt="Promotion Image">
        </section>
    <?php endif; ?>

    <section class="search">
        <form action="index.php" method="GET">
            <input type="text" name="search" placeholder="Search for games..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
            <button type="submit">Search</button>
        </form>
    </section>

    <section class="sort">
        <form action="index.php" method="GET">
            <label for="sort">Sort By:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
            </select>
            <?php if (isset($_GET['search'])): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
            <?php endif; ?>
        </form>
    </section>

    <section class="game-listings">
        <h2>Featured Games</h2>
        <div class="game-container" id="game-container">
            <?php
            if (mysqli_num_rows($games) > 0) {
                while ($row = mysqli_fetch_assoc($games)) {
                    $price = $row['price'];
                    $discounted_price = $promotion ? $price * (1 - $promotion['discount_percentage'] / 100) : $price;
                    echo "<div class='game-item'>
                            <img src='{$row['image_url']}' alt='{$row['name']}'>
                            <h3>{$row['name']}</h3>
                            <p>Genre: {$row['genre']}</p>";
                    if ($promotion) {
                        echo "<p>Original Price: $<del>" . number_format($price, 2) . "</del></p>
                              <p>Discounted Price: $" . number_format($discounted_price, 2) . "</p>";
                    } else {
                        echo "<p>Price: $" . number_format($price, 2) . "</p>";
                    }
                    echo "<a href='#' class='btn add-to-cart' data-id='{$row['id']}'>Add to Cart</a>
                          </div>";
                }
            } else {
                echo '<p>No games found.</p>';
            }
            ?>
        </div>
    </section>

    <footer>
        <p>© 2025 Game Shop. All Rights Reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>