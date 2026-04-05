<?php
// Logout script to end admin session
// This file clears all session data and redirects to login page

session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session completely
session_destroy();

// Set a flag to indicate successful logout (optional)
$_SESSION['logged_out'] = true;

// Redirect to login page
header("Location: login.php");
exit();
?>