<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Clothify</title>
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
        </div>
    </div>

   <div class="form-container">
    <h2>Contact Us</h2>
    <form action="submit_contact.php" method="post">
        <p>We'd love to hear from you! Please fill out the form below and we'll get back to you as soon as possible.</p>
        <input type="text" name="name" placeholder="Your Name" required><br>
        <input type="email" name="email" placeholder="Your Email" required><br>
        <textarea name="message" placeholder="Your Message" rows="5" required></textarea><br>
        <button type="submit">Send Message</button>
    </form>
</div>

  

</body>
</html>