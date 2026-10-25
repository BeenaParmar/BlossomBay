<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

$order_id = intval($_GET['order_id'] ?? 0);
$grand_total = 0;
$order_num = "";

if($order_id > 0 && isset($conn)){
    $res = $conn->query("SELECT order_number, grand_total FROM orders WHERE id='$order_id'");
    if($res && $row = $res->fetch_assoc()){
        $grand_total = number_format((float)$row['grand_total'], 2);
        $order_num = $row['order_number'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instant UPI QR Payment | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .qr-wrapper {
            max-width: 500px;
            width: 92%;
            margin: 40px auto 60px auto;
            flex: 1;
        }

        .qr-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 35px 25px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            text-align: center;
        }

        .qr-card h2 {
            font-family: var(--font-serif);
            font-size: 26px;
            color: #3b232c;
            margin-bottom: 6px;
        }

        .qr-frame {
            background: #faf7f8;
            padding: 16px;
            border-radius: var(--radius-md);
            border: 2px dashed #f0cdd8;
            display: inline-block;
            margin: 20px auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }

        .qr-frame img {
            width: 230px;
            height: 230px;
            object-fit: contain;
            display: block;
            border-radius: 8px;
        }

        .app-badges-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .app-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="qr-wrapper">
        <div class="qr-card">
            <h2><i class="fa-solid fa-qrcode" style="color:var(--primary);"></i> Scan to Pay</h2>
            <p style="color:var(--text-muted); font-size:14px;">
                Scan with any UPI application to complete payment for Order <b>#<?php echo htmlspecialchars($order_num); ?></b>
            </p>

            <?php if($grand_total > 0): ?>
            <div style="font-size:24px; font-weight:800; color:var(--primary); margin-top:10px;">
                Amount: ₹<?php echo $grand_total; ?>
            </div>
            <?php endif; ?>

            <div class="qr-frame">
                <img src="images/qr_payment.jpeg" alt="BlossomBay QR Code" onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=upi://pay?pa=blossombay@upi&pn=BlossomBay';">
            </div>

            <div class="app-badges-row">
                <span class="app-badge"><i class="fa-brands fa-google-pay"></i> Google Pay</span>
                <span class="app-badge">PhonePe</span>
                <span class="app-badge">Paytm</span>
                <span class="app-badge">BHIM UPI</span>
            </div>

            <p style="font-size:13px; color:#64748b; margin-top:20px; line-height:1.5;">
                Once your payment is approved in your UPI app, click below to immediately verify and earn <b>5 Blossom Reward Points</b>.
            </p>

            <div style="margin-top:24px;">
                <a href="payment_success.php?order_id=<?php echo $order_id; ?>" class="bb-btn bb-btn-primary" style="width:100%; padding:14px; font-size:15px;">
                    <i class="fa-solid fa-circle-check"></i> I Have Completed the Payment
                </a>
            </div>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>