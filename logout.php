<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Logout</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #ff7e5f, #feb47b);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .logout-container {
        background: white;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0px 6px 18px rgba(0,0,0,0.1);
        text-align: center;
        width: 350px;
    }
    h2 {
        color: #333;
    }
    p {
        color: #666;
        margin: 10px 0 20px;
    }
    a {
        display: inline-block;
        padding: 10px 20px;
        background: linear-gradient(90deg, #ff7e5f, #ff2f92);
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }
    a:hover {
        opacity: 0.9;
    }
</style>
<meta http-equiv="refresh" content="2;url=login.php">
</head>
<body>

<div class="logout-container">
    <h2>✅ Logged Out</h2>
    <p>You have been successfully logged out.</p>
    <a href="login.php">Go to Login</a>
</div>

</body>
</html>
