<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    echo "Login required";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$action = $_POST['action'];

if($action == "increase"){
    $conn->query("UPDATE cart 
                  SET quantity = quantity + 1 
                  WHERE user_id='$user_id' 
                  AND product_id='$product_id'");
}

if($action == "decrease"){
    $conn->query("UPDATE cart 
                  SET quantity = quantity - 1 
                  WHERE user_id='$user_id' 
                  AND product_id='$product_id' 
                  AND quantity > 1");
}

header("Location: cart.php");
?>