<?php
session_start();
include "config.php"; // your DB connection

// 🔴 User must be logged in
if(!isset($_SESSION['user_id'])){
    echo "Please login first";
    exit;
}

$user_id = $_SESSION['user_id'];

// 🔴 Read JSON data
$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['product_id'])){
    echo "Invalid request";
    exit;
}

$product_id = (int)$data['product_id'];


// ✅ Check if product exists (VERY PROFESSIONAL)
$checkProduct = $conn->prepare(
    "SELECT id FROM products WHERE id = ?"
);
$checkProduct->bind_param("i", $product_id);
$checkProduct->execute();
$res = $checkProduct->get_result();

if($res->num_rows == 0){
    echo "Product not found";
    exit;
}


// ✅ Prevent duplicate wishlist items
$checkWishlist = $conn->prepare(
    "SELECT id FROM wishlist 
     WHERE user_id = ? AND product_id = ?"
);

$checkWishlist->bind_param("ii", $user_id, $product_id);
$checkWishlist->execute();
$result = $checkWishlist->get_result();

if($result->num_rows > 0){
    echo "Already in wishlist";
    exit;
}


// ✅ Insert into wishlist
$stmt = $conn->prepare(
    "INSERT INTO wishlist (user_id, product_id)
     VALUES (?, ?)"
);

$stmt->bind_param("ii", $user_id, $product_id);

if($stmt->execute()){
    echo "success";
}else{
    echo "Database error";
}
?>
