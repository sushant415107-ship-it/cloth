<?php
session_start();
include 'config.php'; // Includes database connection

// FUNCTION TO CHECK ADMIN SESSION (MUST BE INCLUDED IN ALL ADMIN FILES)
function check_admin_auth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: admin_login.php");
        exit();
    }
}
check_admin_auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Clothify</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-nav { margin-top: 20px; padding: 0 40px; display: flex; justify-content: center; gap: 30px; }
        .admin-nav a { background: #3498db; color: white; padding: 15px 30px; border-radius: 6px; font-weight: bold; transition: background 0.3s; }
        .admin-nav a:hover { background: #2980b9; }
        .admin-content { padding: 40px; }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo"><a href="index.php">Clothify</a></div>
        <div class="nav-links"></div>
        <div class="auth-links">
            <a href="admin_logout.php">Logout</a>
        </div>
    </div>
    
    <div class="admin-nav">
        <a href="admin_products.php">Manage Products (CRUD)</a>
        <a href="admin_orders.php">Manage Orders & Status</a>
    </div>

    <div class="admin-content">
        <h2>Welcome to the Clothify Admin Panel</h2>
        <p>Use the links above to manage your store's inventory and orders.</p>
    </div>

   
</body>
</html>