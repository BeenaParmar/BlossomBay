<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

$type = $_POST['type'];
$a1 = $_POST['address1'];
$a2 = $_POST['address2'];
$a3 = $_POST['address3'];
$country = $_POST['country'];
$city = $_POST['city'];
$date = $_POST['delivery_date'];
$msg = $_POST['message'];

$stmt = $conn->prepare("
INSERT INTO location 
(user_id, type, address1, address2, address3, country, city, delivery_date, message)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("issssssss",
$user_id, $type, $a1, $a2, $a3, $country, $city, $date, $msg
);

$stmt->execute();

echo "<script>alert('Location Saved 🌍'); window.location='BBglobe.php';</script>";
?>