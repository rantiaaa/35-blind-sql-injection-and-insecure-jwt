<?php
session_start();
include('config.php');
include('jwt.php');

if (!isset($_SESSION['token']) || !verify_jwt($_SESSION['token'])) {
    header('Location: login.php');
    exit;
}

$user_data = decode_payload($_SESSION['token']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <div>
            <a href="index.php">Home</a>
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>My Account</h2>
        <p>Username: <?php echo htmlspecialchars($user_data['username']); ?></p>
        <p>Role: <?php echo htmlspecialchars($user_data['role']); ?></p>
    </div>
</body>
</html>