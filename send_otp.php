<?php
session_start();

if (!isset($_POST['contact'])) {
    echo "No contact";
    exit;
}

$contact = $_POST['contact'];

$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['reset_user'] = $contact;

echo "Your OTP is: $otp";
