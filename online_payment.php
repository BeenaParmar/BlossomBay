<?php
session_start();
include "config.php";

if (!isset($_GET['order_id'])) {
    die("Invalid order");
}

$order_id = (int) $_GET['order_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    die("Order not found");
}

$amount = $order['grand_total'] * 100; // Razorpay needs paise
?>

<!DOCTYPE html>
<html>
<head>
<title>Online Payment</title>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>

<h2>Processing Payment...</h2>

<script>
var options = {
    "key": "YOUR_RAZORPAY_TEST_KEY",
    "amount": "<?php echo $amount; ?>",
    "currency": "INR",
    "name": "BlossomBay",
    "description": "Order Payment",
    "handler": function (response) {

        fetch("verify_payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                order_id: "<?php echo $order_id; ?>",
                payment_id: response.razorpay_payment_id
            })
        })
        .then(res => res.text())
        .then(data => {
            window.location.href = "order_success.php?order_id=<?php echo $order_id; ?>";
        });
    }
};

var rzp = new Razorpay(options);
rzp.open();
</script>

</body>
</html>