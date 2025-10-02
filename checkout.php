<?php
session_start();
include 'config.php';

$cart_items = array();
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_unique($_SESSION['cart']));
    
    $sql = "SELECT id, name, price, image_url FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
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
    <title>Checkout - Clothify</title>
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
        <h2>Checkout</h2>
        <h3 style="text-align: left;">Order Summary</h3>
        <table style="width:100%; text-align: left; margin-bottom: 20px;">
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

    <form id="checkout-form" action="confirm_order.php" method="post">
    
        <h3 style="text-align: left; margin-top: 30px;">Delivery Information</h3>
        <input type="text" name="address" placeholder="Street Address" required><br>
        <input type="text" name="city" placeholder="City" required><br>
        <input type="text" name="zip_code" placeholder="Zip/Postal Code" required><br>

        <h3 style="text-align: left; margin-top: 30px;">Select Payment Method</h3>
        <div style="text-align: left; margin-top: 10px;">
            <input type="radio" id="cod" name="payment_method" value="cod" required>
            <label for="cod">Cash on Delivery</label><br>
            <input type="radio" id="razorpay" name="payment_method" value="razorpay" required>
            <label for="razorpay">Razorpay (Online Payment)</label>
        </div>
        
        <button type="submit" style="width: 100%; margin-top: 20px;">Confirm Order</button>
    </form>
    </div>

  
<script>
    document.getElementById('checkout-form').addEventListener('submit', function(event) {
        var razorpayRadio = document.getElementById('razorpay');
        if (razorpayRadio.checked) {
            event.preventDefault(); // Stop the form from submitting normally
            
            // Set the form action to the Razorpay handler
            this.action = 'pay_razorpay.php';
            
            // Now, submit the form to the new action
            this.submit();
        }
    });
</script>
</body>
</html>