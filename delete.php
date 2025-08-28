<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit(); }

$stmt = $conn->prepare("DELETE FROM posts WHERE id=?");
$stmt->execute([$id]);

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $stmt = $conn->prepare("DELETE FROM posts WHERE id=?");
    $stmt->execute([$id]);
    
header("Location: index.php");
exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confirm Delete</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .confirm-box {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0px 6px 18px rgba(0,0,0,0.1);
        max-width: 400px;
        text-align: center;
    }
    .confirm-box h2 {
        color: #ff2f92;
        margin-bottom: 10px;
    }
    .confirm-box p {
        color: #555;
        margin-bottom: 20px;
    }
    .btn {
        padding: 10px 18px;
        font-size: 1rem;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        margin: 5px;
    }
    .btn-delete {
        background: linear-gradient(90deg, #ff7e5f, #ff2f92);
        color: white;
        box-shadow: 0px 4px 10px rgba(255, 47, 146, 0.3);
    }
    .btn-delete:hover {
        opacity: 0.92;
    }
    .btn-cancel {
        background: #ccc;
        color: #333;
    }
    .btn-cancel:hover {
        background: #bbb;
    }
</style>
</head>
<body>

<div class="confirm-box">
    <h2>⚠ Confirm Deletion</h2>
    <p>Are you sure you want to delete this post? This action cannot be undone.</p>
    <form method="post">
        <button type="submit" name="confirm" class="btn btn-delete">Yes, Delete</button>
        <a href="index.php" class="btn btn-cancel">Cancel</a>
    </form>
</div>

</body>
</html>