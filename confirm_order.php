<?php
session_start();
include 'config.php'; // Includes the database connection

// --- 1. Capture Data and Set Statuses ---

// Default address capture (used by COD)
$delivery_address = trim($_POST['address'] ?? '');
$delivery_city = trim($_POST['city'] ?? '');
$delivery_zip = trim($_POST['zip_code'] ?? '');

if (isset($_GET['razorpay_payment_id'])) {
    $payment_method = "Razorpay";
    $transaction_status = "Completed"; 
    $order_status = "Pending";
    
    // CRITICAL FIX: IF COMING FROM RAZORPAY REDIRECT, PULL ADDRESS FROM SESSION
    $delivery_address = $_SESSION['delivery_address'] ?? $delivery_address;
    $delivery_city = $_SESSION['delivery_city'] ?? $delivery_city;
    $delivery_zip = $_SESSION['delivery_zip'] ?? $delivery_zip;
    
    // Clear the address session variables after retrieval
    unset($_SESSION['delivery_address'], $_SESSION['delivery_city'], $_SESSION['delivery_zip']);

} else {
    // COD Logic (Uses the initial $_POST capture)
    $payment_method = "Cash on Delivery";
    $transaction_status = "Pending Payment"; 
    $order_status = "Pending";
}

// --- 2. Calculate Total Price Safely ---
$total_price = 0;
$valid_product_ids = [];

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    
    // Clean and validate IDs
    foreach (array_unique($_SESSION['cart']) as $id) {
        if (is_numeric($id)) {
            $valid_product_ids[] = (int)$id;
        }
    }

    if (!empty($valid_product_ids)) {
        $product_id_string = implode(',', $valid_product_ids);
        
        // Execute SQL query to get prices for valid IDs
        $sql = "SELECT id, price FROM products WHERE id IN ($product_id_string)";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $total_price += $row['price'];
            }
        }
    }
}

// --- 3. Insert Order into Database ---
$user_id = $_SESSION['user_id'] ?? 0; 
$order_placed = false;

// Check if we have a valid order (items, address, and logged-in user)
if ($total_price > 0 && !empty($delivery_address) && $user_id > 0) {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, delivery_address, delivery_city, delivery_zip, payment_method, order_status, transaction_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssssss", $user_id, $total_price, $delivery_address, $delivery_city, $delivery_zip, $payment_method, $order_status, $transaction_status);

    if ($stmt->execute()) {
        $order_placed = true;
    } else {
        // Handle database error
        die("Error placing order: " . $conn->error);
    }
    $stmt->close();
}

// --- 4. Final Cleanup and Display ---
$delivery_date = date('F j, Y', strtotime('+5 days'));
if ($order_placed) {
    unset($_SESSION['cart']); // Only clear the cart if the order was successfully saved
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
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
        <h2>Order Confirmed!</h2>
        <?php if ($order_placed): ?>
            <p>Thank you for your order. Your selected payment method is **<?php echo htmlspecialchars($payment_method); ?>**.</p>
            
            <h3 style="text-align: left; margin-top: 20px;">Delivery Address:</h3>
            <p style="text-align: left;">
                <?php echo $delivery_address; ?><br>
                <?php echo $delivery_city . ', ' . $delivery_zip; ?>
            </p>

            <p>Your order will be processed and shipped shortly.</p>
            <p>The estimated date of delivery is **<?php echo $delivery_date; ?>**.</p>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">We could not process your order.</p>
            <p>Please ensure you are logged in and your cart is not empty before checking out.</p>
            <p style="color: grey; margin-top: 10px;">(This often happens when the delivery address is lost during payment redirects. Ensure your `pay_razorpay.php` is updated to store the address in the session.)</p>
        <?php endif; ?>
        
        <p style="margin-top: 20px;">
            <a href="index.php"><button>Continue Shopping</button></a>
        </p>
    </div>

   
</body>
</html>