<?php
session_start();
// CLEAR CART if any old session data is lingering
unset($_SESSION['cart']); 
include 'config.php';
// ... rest of the code ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $fullname, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // SUCCESSFUL LOGIN BLOCK
            $_SESSION['user'] = $fullname;
            // CRITICAL FIX: Store the user's ID for order processing
            $_SESSION['user_id'] = $id; 
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with that email!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Online Clothes Shop</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="form-container">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>New user? <a href="register.php">Register here</a></p>
</div>
</body>
</html>