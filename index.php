<?php
include('config.php');

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM articles WHERE title LIKE ?");
    $stmt->bind_param("s", $search);
} else {
    $stmt = $conn->prepare("SELECT * FROM articles");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - WikiWow</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="my-account.php">My Account</a>
        </div>
        <div>
            <a href="login.php">Login/Register</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>Welcome to WikiWow</h1>
        <form method="GET" action="index.php">
            <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search articles">
            <button class="button" type="submit">Search</button>
        </form>

        <div class="articles">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="article">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p>
                        <?php
                        $short_content = substr($row['content'], 0, 200);
                        echo htmlspecialchars($short_content) . '...'; 
                        ?>
                    </p>
                    <a href="read-more.php?id=<?php echo $row['id']; ?>" class="read-more">Read more</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
