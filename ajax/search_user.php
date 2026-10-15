<?php
include "../includes/auth.php";
include "../config/db.php";

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$response = ['success' => false, 'message' => ''];

if (empty($email)) {
    $response['message'] = "Please enter an email.";
} else {
    // Search for the user by email
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Don't let users search for themselves
        if ($user['id'] == $_SESSION['user_id']) {
            $response['message'] = "You cannot chat with yourself.";
        } else {
            $response['success'] = true;
            $response['user_id'] = $user['id'];
            $response['username'] = $user['username'];
        }
    } else {
        $response['message'] = "User not found with that email.";
    }
}

echo json_encode($response);
