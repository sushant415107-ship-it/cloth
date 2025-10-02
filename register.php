<?php
session_start();
// CLEAR CART if any old session data is lingering
unset($_SESSION['cart']);
include 'config.php';
// ... rest of the code ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $password);

    if ($stmt->execute()) {
        // CRITICAL FIX: Get the ID of the new user and store it
        $user_id = $conn->insert_id; 
        
        $_SESSION['user'] = $fullname;
        $_SESSION['user_id'] = $user_id; // Store the user's ID
        
        header("Location: index.php");
        exit();
    } else {
        $error = "Email already exists!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Online Clothes Shop</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="form-container">
    <h2>Create an Account</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="fullname" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
