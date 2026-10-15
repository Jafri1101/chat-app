<?php
session_start();

include __DIR__ . '/../config/db.php';

// If user is logged in update last_seen
if (isset($_SESSION['user_id'])) {
    $update_stmt = $conn->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?");
    $update_stmt->bind_param("i", $_SESSION['user_id']);
    $update_stmt->execute();
}

// Protect pages
if (!isset($_SESSION['user_id'])) {
    header("Location: /chat_app/login.php");
    exit();
}
?>
