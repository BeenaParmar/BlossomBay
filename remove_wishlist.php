<?php
session_start();
include "config.php";

if(!isset($_SESSION['user_id'])){
    echo "Login required";
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($data['product_id'])){
    echo "Invalid request";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$data['product_id'];

$stmt = $conn->prepare(
    "DELETE FROM wishlist WHERE user_id=? AND product_id=?"
);

$stmt->bind_param("ii",$user_id,$product_id);

if($stmt->execute()){
    echo "success";
}else{
    echo "error";
}
?>