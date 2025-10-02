<?php
session_start();
include 'config.php';

// Hardcoded Admin Credentials (Change this immediately for production!)
$ADMIN_USERNAME = "admin@clothify.com";
$ADMIN_PASSWORD_HASH = password_hash("secureadminpassword", PASSWORD_DEFAULT); 

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === $ADMIN_USERNAME && password_verify($password, $ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid admin credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Clothify</title>
    <link rel="stylesheet" href="css/styles.css"> 
</head>
<body>
<div class="form-container">
    <h2>Admin Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Admin Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login as Admin</button>
    </form>
</div>
</body>
</html>