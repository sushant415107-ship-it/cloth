<?php
session_start();
// Check if the admin is logged in
if (isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']); // Unset the specific admin session variable
}
header("Location: admin_login.php");
exit();
?>