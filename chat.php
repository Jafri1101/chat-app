<?php
include "includes/auth.php";
include "config/db.php";

$current_user = $_SESSION['user_id'];

if(!isset($_GET['user'])){
    die("No user selected");
}

$chat_user = intval($_GET['user']);

$stmt = $conn->prepare("SELECT username FROM users WHERE id=?");
$stmt->bind_param("i", $chat_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if(!$user){
    die("User not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="css/chat.css">

<link rel="icon" href="/chat_app/favicon.ico?v=3" type="image/x-icon">
<link rel="manifest" href="/chat_app/manifest.json">

</head>

<body>

<div class="chat-container">

    <div class="chat-header">
        <a href="users.php" style="color:white; text-decoration:none; margin-right:15px;">❮❮ Back</a>
         <?php echo htmlspecialchars($user['username']); ?>
    </div>

    <div class="chat-box" id="chat-box"></div>
<button id="deleteSelected" style="display:none; margin:10px; padding:8px 14px; background:red; color:white; border:none; border-radius:5px; cursor:pointer;">
    Delete Selected
</button>

    <div class="chat-input">
    <input type="text" id="message" placeholder="Type a message">
    <script>
document.getElementById("message").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        sendMessage();
    }
});
</script>


    
<input type="file" id="file" style="display:none;">
 <button id="fileButton" type="button" class="icon-button" title="Attach file">
        <!-- You can use SVG or text icon -->
        🔗
    </button>
    
    <button onclick="sendMessage()">Send</button>
</div>


</div>

<script>
let selectedMessages = [];

function fetchMessages(){
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "ajax/fetch_messages.php?user=<?php echo $chat_user; ?>", true);
    xhr.onload = function(){
        if(this.status === 200){
            var chatBox = document.getElementById("chat-box");
            chatBox.innerHTML = this.responseText;
            chatBox.scrollTop = chatBox.scrollHeight;

            attachSelectableEvents(); // NEW
        }
    }
    xhr.send();
}

// Attach click events to message bubbles
function attachSelectableEvents(){
    const messages = document.querySelectorAll(".selectable");

    messages.forEach(msg => {
        msg.addEventListener("click", function(){
            const id = this.getAttribute("data-id");

            if(this.classList.contains("selected")){
                // unselect
                this.classList.remove("selected");
                selectedMessages = selectedMessages.filter(m => m !== id);
            } else {
                // select
                this.classList.add("selected");
                selectedMessages.push(id);
            }

            toggleDeleteButton();
        });
    });
}

function toggleDeleteButton(){
    const btn = document.getElementById("deleteSelected");
    if(selectedMessages.length > 0){
        btn.style.display = "block";
    } else {
        btn.style.display = "none";
    }
}

// SEND DELETE REQUEST
document.getElementById("deleteSelected").addEventListener("click", function(){

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/delete_messages.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function(){
        if(this.responseText.trim() === "success"){
            selectedMessages = [];
            fetchMessages();
            toggleDeleteButton();
        }
    }

    xhr.send("messages[]=" + selectedMessages.join("&messages[]="));
});

document.getElementById("fileButton").addEventListener("click", function(){
    document.getElementById("file").click();
});
document.getElementById("file").addEventListener("change", function(){
    if(this.files.length > 0){
        console.log("Selected file:", this.files[0].name);
        // You can add UI update if you want (small text below icon)
    }
});

function sendMessage(){
    var msg = document.getElementById("message").value;
    var fileInput = document.getElementById("file");
    var file = fileInput.files[0];

    if(msg.trim() === "" && !file){
        return;
    }

    var formData = new FormData();
    formData.append("message", msg);
    formData.append("user", "<?php echo $chat_user; ?>");

    if(file){
        formData.append("file", file);
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/send_message.php", true);
    xhr.onload = function(){
        document.getElementById("message").value = "";
        document.getElementById("file").value = "";
        fetchMessages();
    }
    xhr.send(formData);
}

setInterval(fetchMessages, 1000);
fetchMessages();
</script>


</body>
</html>
