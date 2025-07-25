<?php
session_start();

// Debug: Check if session is working
if (isset($_SESSION['admin'])) {
    error_log("Session admin already set: " . $_SESSION['admin']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    $response = ['success' => false, 'message' => ''];

    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'matt' && $password === '123') {
        $_SESSION['admin'] = true;
        $_SESSION['username'] = $username;
        error_log("Admin logged in, username set to: " . $username);
        $response['success'] = true;
        $response['message'] = 'Login successful!';
    } else {
        $response['message'] = 'Invalid username or password.';
    }

    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-section">
        <h1>Admin Login</h1>
        <form id="admin-login-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>

    <footer>
        <p>© 2025 Game Shop. All Rights Reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>