<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="../PA4/js/login.js" defer></script>
    <link rel="stylesheet" href="../PA4/css/login.css"> 
</head>
<body>

    <div class="box">
        
        <div class="circle-image"></div> 

        <h2>Login</h2>
        <form method="POST" action="login.php" id="login-form">
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Enter your password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>
