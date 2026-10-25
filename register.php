<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config.php";

if (!isset($_POST['full_name'], $_POST['email'], $_POST['username'], $_POST['password'])) {
    die("Form data missing");
}

$full_name = trim($_POST['full_name']);
$email     = trim($_POST['email']);
$username  = trim($_POST['username']);
$mobile    = trim($_POST['mobile']);
$password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (full_name, username, email, mobile, password)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssss",
    $full_name,
    $username,
    $email,
    $mobile,
    $password
);

if ($stmt->execute()) {
    header("Location: BBlogin.html");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

