<?php

include "../includes/auth.php";
include "../config/db.php";

$current_user = $_SESSION['user_id'];
$chat_user = intval($_GET['user']);

$stmt = $conn->prepare(
"SELECT id, sender_id, message, file_path, deleted_by
 FROM messages 
 WHERE (sender_id=? AND receiver_id=?)
 OR (sender_id=? AND receiver_id=?)
 ORDER BY created_at ASC"
);

$stmt->bind_param("iiii", $current_user, $chat_user, $chat_user, $current_user);
$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){

    // Skip messages that this user deleted
    $deleted_by = $row['deleted_by'];

    if(!empty($deleted_by)){
        $deleted_users = explode(",", $deleted_by);
        if(in_array((string)$current_user, $deleted_users)){
            continue;
        }
    }

    $msg = htmlspecialchars($row['message']);
    $file = $row['file_path'];

    $content = "";

    // Text message
    if(!empty($msg)){
        $content .= nl2br($msg);
    }

    // File or image
    if(!empty($file)){
        $safe_file = htmlspecialchars($file);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $image_exts = ['jpg','jpeg','png','gif','webp'];

        $url = "/chat_app/" . $safe_file;

        if(in_array($ext, $image_exts)){
            $content .= "<br><img src='".$url."' style='max-width:200px;margin-top:8px;border-radius:8px;'>";
        } else {
            $content .= "<br><a href='".$url."' target='_blank'>Download File</a>";
        }
    }

    if($row['sender_id'] == $current_user){
        echo "<div class='message me selectable' data-id='".$row['id']."'>You: ".$content."</div>";
    }else{
        echo "<div class='message other selectable' data-id='".$row['id']."'>".$content."</div>";
    }
}

?>
