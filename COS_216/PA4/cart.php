<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cart</title>
        <link rel="stylesheet" type="text/css" href="../PA3/css/main.css">
        <link rel="stylesheet" type="text/css" href="../PA3/css/cart.css">
        <script src="../PA3/js/cart.js" defer></script>
    </head>

    <body>

       
        <h1>Your Cart</h1>

        <div class="cart-container">
            <div class="loader-container">
                <div class="loader">
                  <span class="loader-text">loading</span>
                  <span class="load"></span>
                </div>
            </div>
        </div>

        <div class="cart-summary">
            <p>Subtotal: R0.00</p>
            <p>Grand Total: R0.00</p>
            <button class="checkout-btn">💳 Checkout</button>
        </div>

    </body>
</html>

<?php include('footer.php'); ?>
