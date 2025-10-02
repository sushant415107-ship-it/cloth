<?php
// Temporary: Ensure errors are displayed during debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$user = "root";   // your MySQL username
$pass = "";       // your MySQL password
$db   = "cloth_shop";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection and die if it fails
if ($conn->connect_error) {
    // CRITICAL: Stop the script and display the exact error
    die("<h1>Database Connection Failed!</h1><p>Please check your config.php credentials.</p><p>Error: " . $conn->connect_error . "</p>");
}
?>