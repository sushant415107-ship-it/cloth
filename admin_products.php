<?php
session_start();
include 'config.php'; 

// --- 1. Admin Authentication Check ---
// Include the check_admin_auth function defined in admin_dashboard.php 
// For simplicity, we'll redefine it here. In a large app, you'd include a separate auth file.
function check_admin_auth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: admin_login.php");
        exit();
    }
}
check_admin_auth();

$message = '';
$edit_product = null;

// --- 2. Handle CRUD Operations ---

// A. DELETE Product
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Product deleted successfully!";
    } else {
        $message = "Error deleting product: " . $conn->error;
    }
    $stmt->close();
    header("Location: admin_products.php"); // Redirect to clean the URL
    exit();
}

// B. CREATE / UPDATE Product (Form Submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $image_url = trim($_POST['image_url']);
    $category = trim($_POST['category']);
    $id = $_POST['product_id'] ?? null;

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, image_url=?, category=? WHERE id=?");
        $stmt->bind_param("sdssi", $name, $price, $image_url, $category, $id);
        $action = "updated";
    } else {
        // CREATE (INSERT)
        $stmt = $conn->prepare("INSERT INTO products (name, price, image_url, category) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $image_url, $category);
        $action = "added";
    }

    if ($stmt->execute()) {
        $message = "Product successfully " . $action . "!";
    } else {
        $message = "Error " . $action . " product: " . $conn->error;
    }
    $stmt->close();
    // Redirect after POST to prevent resubmission
    header("Location: admin_products.php?status=" . urlencode($message)); 
    exit();
}

// C. EDIT (Load product data into form)
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT id, name, price, image_url, category FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_product = $result->fetch_assoc();
    }
    $stmt->close();
}

// --- 3. Fetch All Products for Display ---
$products = [];
$result = $conn->query("SELECT id, name, price, image_url, category FROM products ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();

// Check for status message after redirect
if (isset($_GET['status'])) {
    $message = htmlspecialchars($_GET['status']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Admin-specific Styles */
        body { background-color: var(--background-light); }
        .admin-nav { margin-top: 20px; padding: 0 40px; display: flex; justify-content: flex-start; gap: 30px; }
        .admin-nav a { background: #3498db; color: white; padding: 10px 20px; border-radius: 6px; font-weight: bold; transition: background 0.3s; }
        .admin-nav a:hover { background: #2980b9; }
        .admin-content { padding: 40px; max-width: 1200px; margin: 0 auto; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .product-list table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .product-list th, .product-list td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        .product-list th { background-color: #f8f9fa; }
        .action-links a { margin-right: 10px; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 14px; }
        .action-links .edit { background-color: #f0ad4e; color: white; }
        .action-links .delete { background-color: #d9534f; color: white; }
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
        <a href="admin_orders.php">Manage Orders & Status</a>
    </div>

    <div class="admin-content">
        <h2><?php echo $edit_product ? 'Edit Product: ' . htmlspecialchars($edit_product['name']) : 'Add New Product'; ?></h2>
        
        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="form-container" style="max-width: 600px; text-align: left; margin: 20px 0;">
            <form method="post" action="admin_products.php">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($edit_product['id'] ?? ''); ?>">
                
                <input type="text" name="name" placeholder="Product Name" value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>" required>
                
                <input type="number" name="price" placeholder="Price (e.g., 49.99)" step="0.01" value="<?php echo htmlspecialchars($edit_product['price'] ?? ''); ?>" required>
                
                <input type="text" name="image_url" placeholder="Image URL (e.g., images/product.jpg)" value="<?php echo htmlspecialchars($edit_product['image_url'] ?? ''); ?>" required>
                
                <select name="category" required>
                    <option value="" disabled selected>Select Category</option>
                    <?php
                        // List the categories used in men.php (You might want to make this dynamic later)
                        $categories = ['men', 'T-shirt', 'Jeans', 'Pants', 'Shirt', 'Jacket', 'Hoodie', 'Polo'];
                        foreach ($categories as $cat) {
                            $selected = ($edit_product['category'] ?? '') === $cat ? 'selected' : '';
                            echo "<option value=\"$cat\" $selected>" . htmlspecialchars($cat) . "</option>";
                        }
                    ?>
                </select>
                
                <button type="submit"><?php echo $edit_product ? 'Update Product' : 'Add Product'; ?></button>
                <?php if ($edit_product): ?>
                    <a href="admin_products.php"><button type="button" style="background-color: #5bc0de;">Cancel Edit</button></a>
                <?php endif; ?>
            </form>
        </div>

        <h3>Existing Products</h3>
        <div class="product-list">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr><td colspan="5">No products found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($product['category']); ?></td>
                                <td class="action-links">
                                    <a class="edit" href="admin_products.php?edit_id=<?php echo $product['id']; ?>">Edit</a>
                                    <a class="delete" href="admin_products.php?delete_id=<?php echo $product['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
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