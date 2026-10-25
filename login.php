<?php
session_start();
include "config.php";

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header("Location: BBlogin.html?error=Please enter username and password");
    exit;
}

$stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {

    // store session
$_SESSION['user_id'] = $user['id'];      // or your primary key
$_SESSION['username'] = $user['username'];

    // redirect to home
    header("Location: BBhome.php");
    exit;

} else {
    header("Location: BBlogin.html?error=Invalid username or password");
    exit;
}
