<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="../PA4/css/signup.css">
    <script src="../PA4/js/signup.js" defer></script>
</head>
<body>

    <form id="signupForm" style="border:1px solid #ccc">
        <div class="container">
            <h1>Sign Up</h1>
            <p>Please fill in this form to create an account.</p>
            <hr>

            <label><b>Name</b></label>
            <input type="text" id="name" name="name">

            <label><b>Surname</b></label>
            <input type="text" id="surname" name="surname">

            <label><b>Email</b></label>
            <input type="text" id="email" name="email">

            <label><b>Password</b></label>
            <input type="password" id="password" name="password">

            <label><b>Type: 'Customer', 'Courier', or 'Inventory Manager' </b></label>
            <input type="text" id="type" name="type">

            <p>By creating an account you agree to our <a href="#">Terms & Privacy</a>.</p>
            <div class="clearfix">
            <button type="button" class="cancelbtn" onclick="window.location.href='products.php';">Cancel</button>
            <button type="submit" class="signupbtn">Sign Up</button>
            </div>

            <p id="error" style="color:red;"></p>
            <p id="success" style="color:green;"></p>
        </div>
    </form>
    <?php include('footer.php');?>
</body>
</html>
