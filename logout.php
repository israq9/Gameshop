<?php
session_start(); // Start the session

// Destroy all session data
session_destroy();

// Redirect to signin page
header("Location: index.php");
include 'database.php';

exit();
?>