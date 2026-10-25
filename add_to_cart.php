<?php
session_start();
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['product_id'])) {
    echo "Invalid request";
    exit;
}

if(!isset($_SESSION['user_id'])){
    echo "Login required";
    exit;
}

$product_id = intval($data['product_id']);
$user_id = $_SESSION['user_id'];

// Check if product already in cart
$check = $conn->query("SELECT * FROM cart 
                       WHERE user_id='$user_id' 
                       AND product_id='$product_id'");

if($check->num_rows > 0){
    $conn->query("UPDATE cart 
                  SET quantity = quantity + 1 
                  WHERE user_id='$user_id' 
                  AND product_id='$product_id'");
} else {
    $conn->query("INSERT INTO cart (user_id, product_id, quantity) 
                  VALUES ('$user_id', '$product_id', 1)");
}

echo "success";
?>