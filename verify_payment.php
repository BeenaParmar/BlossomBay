<?php
session_start();
include "config.php";

$data = json_decode(file_get_contents("php://input"), true);

$order_id = $data['order_id'];

$stmt = $conn->prepare("UPDATE orders SET payment_status='Paid' WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

echo "success";