<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Create Post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_post'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title && $content) {
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();
        $stmt->close();
    }
}

// ---------- SEARCH + PAGINATION ----------
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // posts per page
$offset = ($page - 1) * $limit;

// Count total posts (with search filter)
$countQuery = "SELECT COUNT(*) as total FROM posts WHERE title LIKE ? OR content LIKE ?";
$stmt = $conn->prepare($countQuery);
$like = "%" . $search . "%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$countResult = $stmt->get_result()->fetch_assoc();
$totalPosts = $countResult['total'];
$totalPages = ceil($totalPosts / $limit);

// Fetch posts with search + pagination
$query = "SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $like, $like, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog - Dashboard</title>
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
    header h2 {
        margin: 0;
    }
    header a {
        color: white;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 15px;
        border-radius: 5px;
        transition: 0.3s;
    }
    header a:hover {
        background: rgba(255, 255, 255, 0.4);
    }
    main {
        max-width: 900px;
        margin: 30px auto;
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0px 6px 18px rgba(0,0,0,0.05);
    }
    h3 {
        margin-bottom: 15px;
        color: #333;
    }
    .post-form input, .post-form textarea, .post-form button {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
    }
    form textarea {
        resize: none;
        height: 100px;
    }
    form button {
        background: linear-gradient(90deg, #ff7e5f, #ff2f92);
        color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
        margin-top: 12px;
        box-shadow: 0px 4px 10px rgba(255, 47, 146, 0.3);
    }
    form button:hover {
        opacity: 0.92;
    }
    .search-box {
        margin: 20px 0;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;              /* gap between input and button */
        max-width: 600px;
    }
    .search-box input {
        flex: 1;
        padding: 10px 12px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }
    .search-box button {
        padding: 10px 20px;
        border: none;
        background: #ff7e5f;
        color: white;
        font-weight: bold;
        cursor: pointer;
        border-radius: 5px;
        white-space: nowrap;
        margin-top: auto;
        width: 150px;
    }
    .search-box button:hover {
        opacity: 0.9;
    }
    .post {
        border: 1px solid #ddd;
        padding: 15px;
        margin-top: 15px;
        border-radius: 5px;
        background: #fafafa;
    }
    .post h4 {
        margin: 0;
        color: #ff2f92;
    }
    .post p {
        margin: 8px 0;
        color: #555;
        line-height: 1.5;
    }
    .post small {
        color: #888;
    }
    .post a {
        color: #ff7e5f;
        text-decoration: none;
        margin-right: 10px;
        font-size: 0.9rem;
    }
    .post a:hover {
        text-decoration: underline;
    }
    .pagination {
        margin-top: 20px;
        text-align: center;
    }
    .pagination a {
        display: inline-block;
        margin: 0 5px;
        padding: 8px 12px;
        background: #ff7e5f;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }
    .pagination a.active {
        background: #ff2f92;
    }
    .pagination a:hover {
        opacity: 0.85;
    }
</style>
</head>
<body>

<header>
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <a href="logout.php">Logout</a>
</header>

<main>
    <h3>Create New Post</h3>
    <form method="post" class ="post-form">
        <input type="text" name="title" placeholder="Post Title" required>
        <textarea name="content" placeholder="Post Content" required></textarea>
        <button type="submit" name="create_post">Add Post</button>
    </form>

    <h3>Search Posts</h3>
    <form method="get" class="search-box">
        <input type="text" name="search" placeholder="Search by title or content..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <h3>All Posts</h3>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post">
                <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                <small>Posted on <?php echo $row['created_at']; ?></small><br>
                <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No posts found.</p>
    <?php endif; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page-1; ?>">Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?search=<?php echo urlencode($search); ?>&page=<?php echo $page+1; ?>">Next</a>
        <?php endif; ?>
    </div>
</main>

</body>
</html>