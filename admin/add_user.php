<?php
include "../includes/auth.php";
include "../config/db.php";

// 1. Security Check: Only allow Admins
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    die("Access denied. Admin privileges required.");
}

$message = "";
$error = false;

if(isset($_POST['add'])){
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];

    // 2. Validation: Check if fields are empty
    if(empty($username) || empty($email) || empty($password_raw)){
        $message = "All fields are required";
        $error = true;
    } 
    // 3. Validation: Check if email ends with @hj.com
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@hj\.com$/', $email)) {
        $message = "Invalid email. Email must end with @hj.com";
        $error = true;
    }
    else {
        // Hash the password for security
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // 4. Database Insert (Now including the email column)
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $email, $password);

        if($stmt->execute()){
            $message = "User created successfully with email: " . htmlspecialchars($email);
            $error = false;
        } else {
            // This usually happens if the email or username already exists
            $message = "Error: Username or Email already exists in the system.";
            $error = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="/chat_app/css/style.css">
    <link rel="icon" href="/chat_app/favicon.ico?v=3" type="image/x-icon">
    <link rel="manifest" href="/chat_app/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Small styling for message colors */
        .msg { padding: 10px; border-radius: 5px; margin-bottom: 10px; text-align: center; }
        .msg-error { background: #f8d7da; color: #721c24; }
        .msg-success { background: #d4edda; color: #155724; }
    </style>
</head>
<body>

<div class="topbar">
    Add New User
</div>

<div class="container">
    <div class="card">
        <h2>Create User</h2>

        <?php if(!empty($message)): ?>
            <div class="msg <?php echo $error ? 'msg-error' : 'msg-success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" required>

            <label>Email Address</label>
            <input type="email" name="email" placeholder="example@hj.com" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>

            <button type="submit" name="add">Create User</button>
        </form>

        <div class="nav-links">
            <a href="/chat_app/admin/dashboard.php">Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
