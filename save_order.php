<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    echo "not_logged_in";
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['cart'])) {
    echo "invalid_data";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $data['cart'];

/* CREATE ORDER */
$order_number = "BB-" . date("Y") . "-" . strtoupper(substr(uniqid(), -6));
$grand_total = 0;

foreach ($cart as $item) {
$price = (int) preg_replace('/[^\d]/', '', $item['price']);
$qty   = (int) $item['qty'];
    $grand_total += $price * $qty;
}

$orderStmt = $conn->prepare(
  "INSERT INTO orders (user_id, order_number, grand_total, order_status, payment_status)
   VALUES (?, ?, ?, 'Confirmed', 'Paid')"
);

$orderStmt->bind_param("isd", $user_id, $order_number, $grand_total);
$orderStmt->execute();

$order_id = $conn->insert_id;

/* INSERT ORDER ITEMS */
$itemStmt = $conn->prepare(
  "INSERT INTO order_items (order_id, product_name, price, quantity, subtotal)
   VALUES (?, ?, ?, ?, ?)"
);

foreach ($cart as $item) {
$price = (int) preg_replace('/[^\d]/', '', $item['price']);
$qty   = (int) $item['qty'];
    $subtotal = $price * $qty;

    $itemStmt->bind_param(
        "isiii",
        $order_id,
        $item['name'],
        $price,
        $qty,
        $subtotal
    );
    $itemStmt->execute();
}

echo "success";
exit;
