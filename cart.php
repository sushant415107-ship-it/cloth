<?php
session_start();
include 'config.php';

$cart_items = array();
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Get unique product IDs from the cart to query the database
    $product_ids = implode(',', array_unique($_SESSION['cart']));
    
    $sql = "SELECT id, name, price, image_url FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Build an array of cart items with their quantities
        $product_details = array();
        while($row = $result->fetch_assoc()) {
            $product_details[$row['id']] = $row;
        }

        foreach ($_SESSION['cart'] as $id) {
            if (isset($product_details[$id])) {
                $cart_items[] = $product_details[$id];
                $total_price += $product_details[$id]['price'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Clothify</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <div class="navbar">
        <div class="logo">
            <a href="index.php">Clothify</a>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="contact.php">Contact</a>
            <a href="about.php">About</a>
        </div>
        <div class="auth-links">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <a href="cart.php">Cart</a>
        </div>
    </div>

    <div class="form-container">
        <h2>Your Shopping Cart</h2>
        <?php if (!empty($cart_items)): ?>
            <table style="width:100%; text-align: left;">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3 style="text-align: right; margin-top: 20px;">Total: $<?php echo number_format($total_price, 2); ?></h3>
<a href="checkout.php"><button style="width: 100%;">Checkout</button></a>        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

   

</body>
</html>