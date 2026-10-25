<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: BBlogin.html");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

session_destroy();
header("Location: BBlogin.html");
exit;
