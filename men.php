<?php
session_start();
include 'config.php';

// Base SQL query
$sql = "SELECT id, name, price, image_url FROM products WHERE category = 'men'";
$params = array();
$types = '';

// Check for search and category filter inputs
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = '%' . trim($_GET['search']) . '%';
    $sql .= " AND name LIKE ?";
    $params[] = $search_term;
    $types .= 's';
}

if (isset($_GET['category_filter']) && !empty($_GET['category_filter'])) {
    $category_filter = $_GET['category_filter'];
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $category_filter . '%';
    $types .= 's';
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Fashion - Clothify</title>
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

    <div style="text-align: center; margin: 20px;">
        <form method="GET" action="men.php">
            <input type="text" name="search" placeholder="Search products..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; width: 250px;">
            <select name="category_filter" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                <option value="">All Categories</option>
                <option value="T-shirt">T-Shirts</option>
                <option value="Jeans">Jeans</option>
                <option value="Pants">Pants</option>
                <option value="Shirt">Shirts</option>
                <option value="Jacket">Jackets</option>
                <option value="Hoodie">Hoodies</option>
                <option value="Polo">Polos</option>
            </select>
            <button type="submit" style="padding: 8px 12px; border: none; border-radius: 5px; background-color: #222; color: #fff; cursor: pointer;">Search</button>
        </form>
    </div>

    <div class="categories">
        <h2>Men's Collection</h2>
        <div class="product-grid">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?>
                    <div class="product-item">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price">$<?php echo htmlspecialchars($row['price']); ?></p>
                        <form action="add_to_cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <button type="submit">Add to Cart</button>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No products found in this category.</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>

    
</body>
</html>