<?php
include "../includes/auth.php";

if($_SESSION['role'] != "admin"){
    die("Access denied");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/chat_app/css/style.css">

<link rel="icon" href="/chat_app/favicon.ico?v=3" type="image/x-icon">
<link rel="manifest" href="/chat_app/manifest.json">
<meta name="viewport" content="width=device-width, initial-scale=1.0">


</head>
<body>

<div class="topbar">
    Admin Dashboard
</div>

<div class="container">
    <div class="card">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <p class="small-text">Manage your chat system from here.</p>

        <div class="nav-links">
            <a href="/chat_app/admin/add_user.php">Add New User</a>
            <a href="/chat_app/admin/users.php">Manage Users</a>
            <a href="/chat_app/logout.php">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
