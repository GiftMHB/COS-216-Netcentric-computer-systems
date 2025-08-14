<!-- Gift Mohuba u23545527 -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View</title>
        <link rel="stylesheet" type="text/css" href="../PA4/css/main.css">
        <link rel="stylesheet" type="text/css" href="../PA4/css/view.css">
        <link rel="stylesheet" type="text/css" href="../PA4/css/products.css">
        <link rel="stylesheet" type="text/css" href="../PA4/css/footer.css">
        <script src="../PA4/js/view.js" defer></script>
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

        <div class="products">
            <div class="product-holder">
                
            </div>

            <div class="product-info">
                <p class="description"></p>
                <ul class="extra-details">
                    <li></li>
                </ul>
            </div>
        </div>

        <?php include('footer.php'); ?>
    </body>
</html>

