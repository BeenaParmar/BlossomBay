<?php
include "db.php";

$order_id = $_GET['order_id'] ?? 0;

if($order_id == 0){
    die("Invalid Order ID");
}

$stmt = $conn->prepare("UPDATE orders SET payment_status='Paid' WHERE id=?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

$user_id = $_SESSION['user_id'];

// Add 5 reward points
$conn->query("
UPDATE users 
SET reward_points = reward_points + 5 
WHERE id = '$user_id'
");

header("Location: my_orders.php");
exit();
?>
