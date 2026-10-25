<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    echo "not_logged_in";
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$order_number = $data['order_number'] ?? '';


if (!isset($data['order_number'])) {
    echo "invalid_request";
    exit;
}


$user_id = $_SESSION['user_id'];
$order_number = $data['order_number'];

$stmt = $conn->prepare(
  "UPDATE orders 
   SET order_status = 'Cancelled' 
   WHERE order_number = ? AND user_id = ?"
);

$stmt->bind_param("si", $order_number, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "success";
} else {
    echo "cannot_cancel";
}
