<?php
session_start();
include 'config.php';

// Check if a user is logged in
if (!isset($_SESSION['user_id'])) { // Changed to user_id for security
    header("Location: login.php");
    exit();
}

// --- CRITICAL FIX: Store Address Data in Session ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['delivery_address'] = $_POST['address'] ?? '';
    $_SESSION['delivery_city'] = $_POST['city'] ?? '';
    $_SESSION['delivery_zip'] = $_POST['zip_code'] ?? '';
}
// ---------------------------------------------------


// Ensure the total price is available
$total_price = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_unique($_SESSION['cart']));
    
    $sql = "SELECT price FROM products WHERE id IN ($product_ids)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $total_price += $row['price'];
        }
    }
} else {
    // Redirect if cart is empty
    header("Location: cart.php");
    exit();
}

// Your Razorpay API keys
$api_key    = "rzp_test_RLx1vyoFWtwTtF";
$api_secret = "GIQh7aZhgVr7CwpvgKEypZuP"; // **Replace this with your actual API Secret from the test dashboard**

// Convert price to the smallest currency unit (e.g., paise for INR)
$amount = $total_price * 100; 

// Generate a unique order ID
$order_id = "ORDER_" . uniqid(); 

// Include the Razorpay SDK
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

$api = new Api($api_key, $api_secret);

// Create a new order
$order = $api->order->create([
    'receipt' => $order_id,
    'amount' => $amount,
    'currency' => 'INR' 
]);

// Prepare payment data for the JavaScript checkout form
$payment_data = [
    "key"               => $api_key,
    "amount"            => $amount,
    "name"              => "Clothify",
    "description"       => "Online Clothing Purchase",
    "image"             => "images/logo.png",
    "prefill"           => [
        "name"          => $_SESSION['user'] ?? 'Customer',
        "email"         => "user@example.com", 
        "contact"       => "9999999999",
    ],
    "notes"             => [
        "address"           => $_SESSION['delivery_address'], // Pass address to Razorpay notes
        "merchant_order_id" => $order_id,
    ],
    "theme"             => [
        "color"             => "#222"
    ],
    "order_id"          => $order->id, 
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Processing Payment...</title>
</head>
<body>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = <?php echo json_encode($payment_data); ?>;
    
    options.handler = function (response){
        window.location.href = 'confirm_order.php?razorpay_payment_id=' + response.razorpay_payment_id + '&razorpay_order_id=' + response.razorpay_order_id;
    };
    
    options.modal = {
        "ondismiss": function(){
            window.location.href = 'checkout.php?status=failed';
        }
    };
    
    var rzp = new Razorpay(options);
    rzp.open();
</script>

</body>
</html>