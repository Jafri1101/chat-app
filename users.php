<?php
include __DIR__ . '/includes/auth.php';
include __DIR__ . '/config/db.php';
include __DIR__ . '/includes/functions.php';

$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];

if($current_role == "admin"){
    header("Location: /chat_app/admin/dashboard.php");
    exit();
}

// RESTRICTED QUERY: Only show users you have a chat history with
$stmt = $conn->prepare("
    SELECT DISTINCT users.id, users.username, users.last_seen 
    FROM users 
    JOIN messages ON (users.id = messages.sender_id OR users.id = messages.receiver_id)
    WHERE (messages.sender_id = ? OR messages.receiver_id = ?) 
    AND users.id != ?
    ORDER BY users.username ASC
");
$stmt->bind_param("iii", $current_user_id, $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat - Search User</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="/chat_app/favicon.ico?v=3" type="image/x-icon">
    <link rel="manifest" href="/chat_app/manifest.json">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .search-box {
            display: flex;
            margin-bottom: 20px;
            gap: 5px;
        }
        .search-box input {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #00b894;
            background: #2d3436;
            color: white;
        }
        .search-box button {
            background: #00b894;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-box button:hover { background: #00947a; }
    </style>
</head>
<body>

<div class="topbar">
    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
</div>

<div class="container">
    <div class="card">
        <h2>Start a Chat</h2>
        
        <!-- SEARCH BY EMAIL SECTION -->
        <div class="search-box">
            <input type="email" id="emailInput" placeholder="Enter email (@hj.com)...">
            <button onclick="searchUser()">Search</button>
        </div>

        <hr style="border: 0; border-top: 1px solid #444; margin-bottom: 20px;">

        <h3>Recent Chats</h3>
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="list-item" style="display:flex; justify-content:space-between; align-items:center;">
                    <a href="chat.php?user=<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['username']); ?>
                    </a>
                    <small>
                        <?php echo getStatus($row['last_seen']); ?>
                    </small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="small-text">No active chats. Search for an email above to start one.</p>
        <?php endif; ?>

        <div class="nav-links">
            <a href="/chat_app/logout.php">Logout</a>
        </div>
    </div>
</div>

<script>
function searchUser() {
    const email = document.getElementById('emailInput').value.trim();
    
    if(email === "") {
        alert("Please enter an email address.");
        return;
    }

    if(!email.endsWith('@hj.com')) {
        alert("Only @hj.com emails are allowed.");
        return;
    }

    // Call the AJAX search script
    const formData = new FormData();
    formData.append('email', email);

    fetch('ajax/search_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // If user found, go to chat
            window.location.href = "chat.php?user=" + data.user_id;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Something went wrong with the search.");
    });
}
</script>

</body>
</html>
