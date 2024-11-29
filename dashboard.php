<?php
session_start();
include('config.php');
include('jwt.php');

if (!isset($_SESSION['token']) || !verify_jwt($_SESSION['token'])) {
    header('Location: login.php');
    exit;
}

$user_data = decode_payload($_SESSION['token']);
if ($user_data['role'] !== 'admin') {
    echo "You are not authorized to access this page.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (empty($title) || empty($content)) {
        echo "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO articles (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();
        echo "Article added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="my-account.php">My Account</a>
        </div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Admin Dashboard - Add Article</h2>
        <form method="POST" action="d