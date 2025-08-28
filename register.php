<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (strlen($username) < 3 || strlen($password) < 5) {
        echo "<script>alert('Username must be at least 3 chars & Password at least 5 chars');</script>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            $stmt->execute([$username, $hashedPassword]);

            echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Error: Username may already exist.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Blog App</title>
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
    .register-container {
        background: white;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0px 6px 18px rgba(0,0,0,0.1);
        width: 350px;
        text-align: center;
    }
    h2 {
        margin-bottom: 20px;
        color: #333;
    }
    input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
    }
    button {
        width: 100%;
        padding: 10px;
        background: linear-gradient(90deg, #ff7e5f, #ff2f92);
        border: none;
        color: white;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }
    button:hover {
        opacity: 0.9;
    }
    a {
        display: block;
        margin-top: 15px;
        text-decoration: none;
        color: #ff2f92;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="register-container">
    <form method="post">
        <h2>Register</h2>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>
    <a href="login.php">Already have an account? Login</a>
</div>

</body>
</html>