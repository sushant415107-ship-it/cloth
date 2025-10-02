<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the product ID to the cart
    $_SESSION['cart'][] = $product_id;

    // Redirect back to the men's page
    header("Location: men.php");
    exit();
} else {
    // If accessed directly or without a product ID, redirect to home
    header("Location: index.php");
    exit();
}
?>