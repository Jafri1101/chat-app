<?php

include "../includes/auth.php";
include "../config/db.php";

$sender = $_SESSION['user_id'];
$receiver = isset($_POST['user']) ? intval($_POST['user']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : "";
$file_path = NULL;

if ($receiver <= 0) {
    exit();
}

/* Handle file upload */
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {

    $upload_dir = "../uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $original_name = basename($_FILES['file']['name']);
    $safe_name = time() . "_" . preg_replace("/[^A-Za-z0-9._-]/", "_", $original_name);
    $target_file = $upload_dir . $safe_name;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        $file_path = "uploads/" . $safe_name;
    }
}

/* Allow send if message or file exists */
if ($message === "" && $file_path === NULL) {
    exit();
}

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, file_path) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $sender, $receiver, $message, $file_path);
$stmt->execute();

?>
