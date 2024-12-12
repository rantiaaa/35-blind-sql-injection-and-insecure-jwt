<?php
session_start();
include('config.php');
include('jwt.php');

/* digunakan untuk memeriksa apakah JWT token terdapat di sesi tsb atau tidak. */
if (!isset($_COOKIE['personal-session']) || !verify_jwt($_COOKIE['personal-session'])) {
    header('Location: login.php');
    exit;
}

/* Terdapat:
1. proses encoding untuk mengetahui data data dari user (uname & role)
2. Apakah role admin? jika bukan maka akses akan ditolak */
$user_data = decode_payload($_COOKIE['personal-session']);
if (!$user_data || $user_data['role'] !== 'admin') {
    echo "You are not authorized to access this page.";
    exit;
}

/* Mengubah role user tanpa prepared statement */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $sql = "UPDATE users SET role = 'admin' WHERE id = $user_id";  // Rentan SQL Injection
    $conn->query($sql);
    echo "Role granted to user!";
}

/* Fitur pencarian user berdasarkan ID tanpa menampilkan detail pengguna */
$search_message = null;
if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
    $search_id = $_GET['search_id'];
    // Query untuk memeriksa apakah ID ada dalam database
    $search_sql = "SELECT id FROM users WHERE id = $search_id";  // Rentan SQL Injection
    $search_result = $conn->query($search_sql);
    
    if ($search_result && $search_result->num_rows > 0) {
        $search_message = "User ID exists in database";
    } else {
        $search_message = "User ID doesn't exist in database";
    }
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">My Profile</a>
        </div>
        <div class="nav-links">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <h2>Admin Panel</h2>

        <!-- Formulir Pencarian User berdasarkan ID -->
        <form method="GET" action="admin.php">
            <input type="text" name="search_id" placeholder="Search by user ID">
            <button type="submit" class="button">Search User</button>
        </form>

        <!-- Pesan Hasil Pencarian -->
        <?php if (isset($search_message)): ?>
            <p><?php echo htmlspecialchars($search_message); ?></p>
        <?php endif; ?>

        <br><br><br>

        <!-- Table -->
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <?php if ($row['role'] !== 'admin'): ?>
                                <form method="POST" action="admin.php">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="button">Grant Admin</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>