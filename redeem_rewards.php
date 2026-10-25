<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];

// Check points
$stmt = $conn->prepare("SELECT reward_points FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if($user['reward_points'] < 500){
    die("Not enough points");
}

// Deduct 500 points
$conn->query("
UPDATE users 
SET reward_points = reward_points - 500 
WHERE id = '$user_id'
");

// OPTIONAL: mark free reward
$conn->query("
INSERT INTO orders (user_id, total_price, payment_status)
VALUES ('$user_id', 0, 'Free Reward')
");

header("Location: rewards.php?success=1");
exit();
?>