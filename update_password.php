<?php
session_start();
include "config.php";

if (!isset($_POST['password'])) {
    echo "missing";
    exit;
}

if (!isset($_SESSION['reset_user'])) {
    echo "unauthorized";
    exit;
}

$newPassword = $_POST['password'];
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$user = $_SESSION['reset_user'];

// Update password
$stmt = $conn->prepare(
    "UPDATE users SET password = ? WHERE email = ? OR mobile = ?"
);
$stmt->bind_param("sss", $hashedPassword, $user, $user);

if ($stmt->execute()) {
    unset($_SESSION['otp']);
    unset($_SESSION['reset_user']);
    echo "success";
} else {
    echo "error";
}

