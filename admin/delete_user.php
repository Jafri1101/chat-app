<?php
include "../includes/auth.php";
include "../config/db.php";

if($_SESSION['role'] != "admin"){
    die("Access denied");
}

$id = $_GET['id'];

$sql = "DELETE FROM users WHERE id='$id'";
$conn->query($sql);

header("Location: users.php");
exit();
?>
