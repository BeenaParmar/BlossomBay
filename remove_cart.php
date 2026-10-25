<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    die("Login required");
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['product_id'])){
    
    $product_id = intval($_GET['product_id']);

    $conn->query("
        DELETE FROM cart 
        WHERE product_id='$product_id' 
        AND user_id='$user_id'
    ");
}

header("Location: cart.php");
exit;
?>