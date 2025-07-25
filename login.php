<?php
session_start();
include 'database.php';

// Debug: Check if session is working
if (isset($_SESSION['user_id'])) {
    error_log("Session user_id already set: " . $_SESSION['user_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $response = ['success' => false, 'message' => ''];

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $response['message'] = 'No account found with that email.';
    } else {
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            error_log("User logged in, user_id set to: " . $userId);
            $response['success'] = true;
            $response['message'] = 'Login successful!';
        } else {
            $response['message'] = 'Invalid password.';
        }
    }

    echo json_encode($response);
    exit;
}

$message = isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Game Shop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="register.php">Register</a></li>
      <li><a href="login.php" class="active">Login</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <section class="login-section">
    <h1>Login to Your Account</h1>
    <?php if ($message): ?>
      <p style="color: <?php echo $message === 'Registration successful!' ? 'green' : 'red'; ?>;">
        <?php echo htmlspecialchars($message); ?>
      </p>
    <?php endif; ?>
    <form id="login-form">
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
      </div>

      <div class="form-group">
        <button type="submit" class="btn">Login</button>
      </div>

      <div class="form-group">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
      </div>
    </form>
  </section>

  <footer>
    <p>© 2025 Game Shop. All Rights Reserved.</p>
  </footer>

  <script src="script.js"></script>
</body>
</html>