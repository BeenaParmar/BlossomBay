<?php
session_start();
include "config.php";

$stmt = $conn->prepare(
  "UPDATE users SET full_name=?, email=?, mobile=? WHERE id=?"
);
$stmt->bind_param(
  "sssi",
  $_POST['full_name'],
  $_POST['email'],
  $_POST['mobile'],
  $_SESSION['user_id']
);
$stmt->execute();

header("Location: profile.php");
exit;
?>