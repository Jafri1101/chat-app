<?php
include "../includes/auth.php";
include "../config/db.php";

$current_user = $_SESSION['user_id'];

if (!isset($_POST['messages'])) {
    exit("No messages selected");
}

$messages = $_POST['messages'];

if (!is_array($messages)) {
    exit("Invalid request");
}

foreach ($messages as $msg_id) {
    $msg_id = intval($msg_id);

    // Get current deleted_by value
    $stmt = $conn->prepare("SELECT deleted_by FROM messages WHERE id=?");
    $stmt->bind_param("i", $msg_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $deleted_by = $row['deleted_by'];

        if (empty($deleted_by)) {
            $new_deleted_by = (string)$current_user;
        } else {
            $deleted_users = explode(",", $deleted_by);

            if (!in_array((string)$current_user, $deleted_users)) {
                $deleted_users[] = (string)$current_user;
            }

            $new_deleted_by = implode(",", $deleted_users);
        }

        $update = $conn->prepare("UPDATE messages SET deleted_by=? WHERE id=?");
        $update->bind_param("si", $new_deleted_by, $msg_id);
        $update->execute();
    }
}

echo "success";
?>
