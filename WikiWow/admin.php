<?php
session_start();
include('config.php');
include('jwt.php');

/* Periksa apakah JWT token ada dan valid */
if (!isset($_COOKIE['personal-session']) || !verify_jwt($_COOKIE['personal-session'])) {
    header('Location: login.php');
    exit;
}

/* Dekode JWT untuk mendapatkan data pengguna */
$user_data = decode_payload($_COOKIE['personal-session']);
if (!$user_data || $user_data['role'] !== 'admin') {
    echo "You are not authorized to access this page.";
    exit;
}

/* Mengubah role user menggunakan prepared statement */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // Prepared statement untuk update role
    $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
    $stmt->bind_param("i", $user_id);  // "i" untuk tipe integer
    $stmt->execute();
    $stmt->close();
    
    echo "Role granted to user!";
}

/* Fitur pencarian user berdasarkan ID menggunakan prepared statement */
$search_message = null;
if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
    $search_id = $_GET['search_id'];

    // Prepared statement untuk pencarian user
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $search_id);  // "i" untuk tipe integer
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $search_message = "User ID exists in database";
    } else {
        $search_message = "User ID doesn't exist in database";
    }
    
    $stmt->close();
}

// Ambil semua data user untuk ditampilkan di tabel
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