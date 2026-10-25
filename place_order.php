<?php
session_start();
include "db.php";

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'];
$payment_status = "Pending";

$grand_total = 0;

/* GET CART ITEMS */

$cart_query = $conn->query("
SELECT c.product_id, c.quantity, p.name, p.price, p.image, p.description
FROM cart c
JOIN products p ON c.product_id = p.id
WHERE c.user_id='$user_id'
");

/* CHECK IF CART IS EMPTY */

if($cart_query->num_rows == 0){
echo "Cart is empty";
exit();
}

/* CALCULATE TOTAL */

while($row = $cart_query->fetch_assoc()){

$subtotal = $row['price'] * $row['quantity'];
$grand_total += $subtotal;

}

/* CREATE ORDER */

$order_number = "ORD".rand(10000,99999);
$tracking_id = "TRK-BB-".rand(100000,999999);
$payment_label = ($payment_method == "COD") ? "Cash on Delivery (COD)" : "Online Payment (QR)";

$conn->query("
INSERT INTO orders
(user_id,order_number,tracking_id,grand_total,payment_method,order_status,payment_status)
VALUES
('$user_id','$order_number','$tracking_id','$grand_total','$payment_label','Placed','$payment_status')
");

$order_id = $conn->insert_id;


/* GET CART AGAIN TO INSERT ITEMS */

$cart_items = $conn->query("
SELECT c.product_id, c.quantity, p.name, p.price, p.image, p.description
FROM cart c
JOIN products p ON c.product_id = p.id
WHERE c.user_id='$user_id'
");

/* INSERT ORDER ITEMS */

while($item = $cart_items->fetch_assoc()){

$subtotal = $item['price'] * $item['quantity'];
$prod_id = (int)$item['product_id'];
$prod_name = $conn->real_escape_string($item['name']);
$prod_price = (float)$item['price'];
$prod_qty = (int)$item['quantity'];
$prod_img = $conn->real_escape_string($item['image']);
$prod_desc = $conn->real_escape_string($item['description']);

$conn->query("
INSERT INTO order_items
(order_id,product_id,product_name,price,quantity,subtotal,image,description)
VALUES
('$order_id',
'$prod_id',
'$prod_name',
'$prod_price',
'$prod_qty',
'$subtotal',
'$prod_img',
'$prod_desc')
");

}

/* CLEAR CART */

$conn->query("DELETE FROM cart WHERE user_id='$user_id'");


/* PAYMENT REDIRECT */

if($payment_method == "COD"){

header("Location: my_orders.php");
exit();

}

if($payment_method == "QR"){

header("Location: qr_payment.php?order_id=$order_id");
exit();

}
?>