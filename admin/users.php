<?php
include "../includes/auth.php";
include "../config/db.php";

// Only admins can see this page
if($_SESSION['role'] != "admin"){
    die("Access denied");
}

// Updated query to include email
$stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="/chat_app/css/style.css">
    <link rel="icon" href="/chat_app/favicon.ico?v=3" type="image/x-icon">
    <link rel="manifest" href="/chat_app/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="topbar">
    Manage Users
</div>

<div class="container">
    <div class="card">
        <h2>All Registered Users</h2>

        <?php while($row = $result->fetch_assoc()): ?>
            <div class="list-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
                <strong><?php echo htmlspecialchars($row['username']); ?></strong> 
                <span class="small-text" style="color: #666;">(<?php echo htmlspecialchars($row['email']); ?>)</span>
                <br>
                <span class="small-text">Role: <?php echo htmlspecialchars($row['role']); ?></span>
                <br><br>

                <?php if($row['role'] != "admin"): ?>
                    <a class="btn-danger" href="delete_user.php?id=<?php echo $row['id']; ?>" 
                       onclick="return confirm('Are you sure you want to delete user: <?php echo $row['username']; ?>?')">Delete User</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

        <div class="nav-links">
            <a href="/chat_app/admin/dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
