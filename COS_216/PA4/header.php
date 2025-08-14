<?php
    require_once 'config.php';  
    session_start(); 

    $config = new DatabaseConfig();  
    $databaseConnection = new DatabaseConnection($config);  
    $connection = $databaseConnection->getConnection();  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" type="text/css" href="../PA4/css/products.css">
    <link rel="stylesheet" type="text/css" href="../PA4/css/main.css">
    <link rel="stylesheet" type="text/css" href="../PA4/css/footer.css">
    <!-- <link rel="stylesheet" type="text/css" href="../PA4/css/lightDarkTheme.css"> -->
    <script src="../PA4/js/theme.js" defer></script>
    <script src="../PA4/js/products.js" defer></script>
</head>
<body>

    <div class="top_box">
        
        <a href="../../index.html" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.html') ? 'active' : ''; ?>">Home</a>
        <a href="products.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>">Products</a>
        <a href="deals.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'deals.php') ? 'active' : ''; ?>">Deals</a>
        <a href="cart.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'cart.php') ? 'active' : ''; ?>">Cart</a>
        <a href="wishlist.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'wishlist.php') ? 'active' : ''; ?>">Wishlist</a>

        <div class="search">
            <input type="text" id="myInput" placeholder="Search..." onkeyup="search()" name="search">
            <button type="submit" onclick="search()">
                🔍
            </button>
        </div>

    </div>

    <div class="auth-links">
        <?php if (isset($_SESSION['user'])): ?>
            <span>Welcome, <?php echo $_SESSION['user']['name']; ?>!</span>
            <a href="logout.php">🚪Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Register📋</a>
        <?php endif; ?>
    </div>

</body>
</html>
