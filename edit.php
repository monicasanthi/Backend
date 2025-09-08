<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Fetch post
$stmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("Post not found");
}

// Authorization: Admin can edit all, Editor can edit only their own
if ($_SESSION['role'] !== 'admin' && $post['user_id'] != $_SESSION['user_id']) {
    die("Unauthorized");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title = trim($_POST['title']);
    $new_content = trim($_POST['content']);

    if ($new_title && $new_content) {
        $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
        $stmt->execute([$new_title, $new_content, $id]);
        header("Location: index.php");
        exit();
    } else {
        $error = "Title and Content cannot be empty!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Post</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        margin: 0;
        padding: 0;
    }
    header {
        background: linear-gradient(90deg, #ff7e5f, #ff2f92);
        color: white;
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    header h2 { margin: 0; }
    header a {
        color: white;
        text-decoration: none;
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 5px;
        transition: 0.3s;
    }
    header a:hover { background: rgba(255,255,255,0.4); }
    main {
        max-width: 700px;
        margin: 30px auto;
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0px 6px 18px rgba(0,0,0,0.05);
    }
    form input, form textarea, form button {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
    }
    form textarea { resize: none; height: 120px; }
    form button {
        background: linear-gradient(90deg, #ff7e5f, #ff2f92);
        color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
        margin-top: 12px;
        box-shadow: 0px 4px 10px rgba(255, 47, 146, 0.3);
    }
    form button:hover { opacity: 0.92; }
    .back-link {
        display: inline-block;
        margin-top: 15px;
        color: #ff7e5f;
        text-decoration: none;
    }
    .back-link:hover { text-decoration: underline; }
    .error { color: red; margin-top: 10px; }
</style>
</head>
<body>

<header>
    <h2>Edit Post</h2>
    <a href="index.php">Back</a>
</header>

<main>
    <h3>Update Your Post</h3>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <input type="text" name="title" 
               value="<?php echo htmlspecialchars($post['title']); ?>" required>
        <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button type="submit">Update Post</button>
    </form>
    <a class="back-link" href="index.php">⬅ Back to Dashboard</a>
</main>

</body>
</html>