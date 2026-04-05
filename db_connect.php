<?php
// Database connection configuration
// This file establishes a connection to the MySQL database
// and is included by other PHP files that need database access

// Database server configuration
$server = "localhost";  // Database host
$user = "root";         // Database username
$pass = "";             // Database password (empty for local development)
$name = "animal_adoption";  // Database name

// Attempt to connect to the database
try {
    $conn = mysqli_connect($server, $user, $pass, $name);
} catch (mysqli_sql_exception $e) {
    // Terminate script if connection fails
    die("Connection failed: " . $e->getMessage());
}
?>