<?php
session_start();
include 'config.php';

// --- 1. Admin Authentication Check ---
// Note: This function should be included in every admin file for security
function check_admin_auth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: admin_login.php");
        exit();
    }
}
check_admin_auth();

$message = '';

// --- 2. Handle Status Update ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $new_order_status = $_POST['order_status'];
    $new_transaction_status = $_POST['transaction_status'];
    
    // Fetch current payment method for validation
    $stmt = $conn->prepare("SELECT payment_method FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_details = $result->fetch_assoc();
    $stmt->close();
    
    if ($order_details['payment_method'] === 'Razorpay' && $new_transaction_status !== 'Completed') {
        // Prevent changing transaction status for Razorpay orders (they should remain Completed)
        $message = "Error: Razorpay transaction status must remain 'Completed'. Only Order Status can be changed.";
    } else {
        // Update both statuses
        $stmt = $conn->prepare("UPDATE orders SET order_status = ?, transaction_status = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_order_status, $new_transaction_status, $order_id);
        
        if ($stmt->execute()) {
            $message = "Order #$order_id status updated successfully!";
        } else {
            $message = "Error updating status: " . $conn->error;
        }
        $stmt->close();
    }
}

// --- 3. Fetch All Orders for Display ---
$orders = [];
// NOTE: We join with users table to display customer email/name (assuming a 'users' table exists)
$sql = "SELECT o.*, u.fullname, u.email 
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        ORDER BY o.id DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Admin-specific Styles */
        body { background-color: var(--background-light); }
        .admin-nav { margin-top: 20px; padding: 0 40px; display: flex; justify-content: flex-start; gap: 30px; }
        .admin-nav a { background: #3498db; color: white; padding: 10px 20px; border-radius: 6px; font-weight: bold; transition: background 0.3s; }
        .admin-nav a:hover { background: #2980b9; }
        .admin-content { padding: 40px; max-width: 1400px; margin: 0 auto; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .order-list table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .order-list th, .order-list td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        .order-list th { background-color: #f8f9fa; }
        .status-form select { padding: 8px; border-radius: 4px; margin-right: 10px; }
        .status-form button { padding: 8px 12px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background 0.3s; }
        .status-form button:hover { background: #27ae60; }
        .status-cell.Pending { color: #f39c12; font-weight: bold; }
        .status-cell.Completed { color: #27ae60; font-weight: bold; }
        .status-cell.Delivered { color: #2980b9; font-weight: bold; }
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
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_products.php">Manage Products (CRUD)</a>
    </div>

    <div class="admin-content">
        <h2>Manage Customer Orders</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="order-list">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Order Status</th>
                        <th>Transaction Status</th>
                        <th>Delivery Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="8">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['fullname'] ?? 'N/A') . " (" . htmlspecialchars($order['email'] ?? 'N/A') . ")"; ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($order['total_amount'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td class="status-cell <?php echo htmlspecialchars($order['order_status']); ?>"><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td class="status-cell <?php echo htmlspecialchars($order['transaction_status']); ?>"><?php echo htmlspecialchars($order['transaction_status']); ?></td>
                                <td><?php echo htmlspecialchars($order['delivery_address'] . ', ' . $order['delivery_city'] . ' ' . $order['delivery_zip']); ?></td>
                                <td>
                                    <form method="post" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        
                                        <select name="order_status">
                                            <option value="Pending" <?php echo ($order['order_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Processing" <?php echo ($order['order_status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo ($order['order_status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo ($order['order_status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo ($order['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        
                                        <select name="transaction_status" 
                                                <?php echo ($order['payment_method'] === 'Razorpay') ? 'disabled' : ''; ?>
                                                title="<?php echo ($order['payment_method'] === 'Razorpay') ? 'Razorpay status is fixed.' : 'Change transaction status for COD.'; ?>">
                                            <option value="Pending Payment" <?php echo ($order['transaction_status'] == 'Pending Payment') ? 'selected' : ''; ?>>Pending Payment</option>
                                            <option value="Completed" <?php echo ($order['transaction_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                        </select>
                                        
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

   

</body>
</html>