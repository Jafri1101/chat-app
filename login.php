<?php
session_start();
include "config/db.php";

$error = "";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if(empty($username) || empty($password)){
        $error = "All fields are required";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if($result->num_rows == 1){

            $user = $result->fetch_assoc();

            if(password_verify($password, $user['password'])){

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] == "admin"){
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: users.php");
                }
                exit();

            } else {
                $error = "Invalid password";
            }

        } else {
            $error = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">

<link rel="icon" href="/chat_app/favicon.ico?v=3" type="image/x-icon">
<link rel="manifest" href="/chat_app/manifest.json">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>

<div class="container" style="max-width: 450px; margin-top: 80px;">
    <div class="card">
        <h2 class="text-center">Jafri Chat System</h2>

        <?php if(!empty($error)): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>

            <button type="submit" name="login" style="width:100%;">Login</button>
        </form>
    </div>
</div>


<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/chat_app/service-worker.js').then(function(reg) {
      console.log('Service Worker registered with scope:', reg.scope);
    }).catch(function(err) {
      console.log('Service Worker registration failed:', err);
    });
  });
}
</script>



</body>
</html>
