<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: BBlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = $conn->query("
    SELECT c.product_id, c.quantity, p.name, p.price, p.image
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id='$user_id'
");

if($query->num_rows == 0){
    header("Location: cart.php");
    exit;
}

$total = 0;
$items = [];
while($row = $query->fetch_assoc()){
    $items[] = $row;
    $total += ($row['price'] * $row['quantity']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .checkout-wrapper {
            max-width: 960px;
            width: 92%;
            margin: 35px auto 60px auto;
            flex: 1;
        }

        .checkout-title {
            font-family: var(--font-serif);
            font-size: 30px;
            color: #3b232c;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 30px;
            align-items: flex-start;
        }

        .checkout-box {
            background: white;
            border-radius: var(--radius-lg);
            padding: 30px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
        }

        .box-heading {
            font-family: var(--font-serif);
            font-size: 20px;
            color: #2b2b2b;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f5ebeb;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Payment Option Selectable Cards */
        .payment-options-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 25px;
        }

        .payment-card-label {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            background: #fff;
            gap: 16px;
        }

        .payment-card-label:hover {
            border-color: var(--primary-light);
            background: var(--primary-soft);
        }

        .payment-card-label input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .payment-card-label input[type="radio"]:checked + .pay-icon-col {
            color: var(--primary);
        }

        .pay-icon-col {
            font-size: 24px;
            color: #8c8588;
            transition: var(--transition);
        }

        .pay-info-col h4 {
            font-size: 15px;
            font-weight: 700;
            color: #2b2b2b;
            margin-bottom: 2px;
        }

        .pay-info-col p {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Summary Item List */
        .summary-items-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
            max-height: 280px;
            overflow-y: auto;
            padding-right: 6px;
        }

        .summary-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #fdf2f4;
        }

        .summary-item-name {
            font-weight: 600;
            color: #3b232c;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .summary-item-img {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            object-fit: cover;
        }

        .summary-pricing-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
            color: #64748b;
        }

        .summary-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            font-weight: 800;
            color: #2b2b2b;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #f0e6e8;
        }

        .summary-total-row span:last-child {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="checkout-wrapper">
        <h1 class="checkout-title">
            <i class="fa-solid fa-credit-card" style="color:var(--primary);"></i> Secure Checkout
        </h1>

        <form action="place_order.php" method="POST">
            <div class="checkout-grid">
                <!-- Left: Payment Method -->
                <div class="checkout-box">
                    <h3 class="box-heading">
                        <i class="fa-solid fa-wallet" style="color:var(--primary);"></i> Select Payment Method
                    </h3>

                    <div class="payment-options-list">
                        <!-- COD Option -->
                        <label class="payment-card-label">
                            <input type="radio" name="payment_method" value="COD" required checked>
                            <div class="pay-icon-col">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div class="pay-info-col">
                                <h4>Cash on Delivery (COD)</h4>
                                <p>Pay in cash or UPI directly when your package arrives at your doorstep</p>
                            </div>
                        </label>

                        <!-- QR Code Option -->
                        <label class="payment-card-label">
                            <input type="radio" name="payment_method" value="QR" required>
                            <div class="pay-icon-col">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div class="pay-info-col">
                                <h4>UPI / Instant QR Code</h4>
                                <p>Scan & pay immediately using Google Pay, PhonePe, or Paytm</p>
                            </div>
                        </label>
                    </div>

                    <div style="background:#f8fafc; border-radius:var(--radius-md); padding:16px; font-size:13px; color:#64748b; line-height:1.5;">
                        <i class="fa-solid fa-shield-halved" style="color:#15803d; margin-right:6px;"></i>
                        All orders are protected with BlossomBay's Freshness Guarantee. Flowers are prepared just hours prior to dispatch.
                    </div>
                </div>

                <!-- Right: Order Summary -->
                <div class="checkout-box">
                    <h3 class="box-heading">
                        <i class="fa-solid fa-receipt" style="color:var(--primary);"></i> Order Summary
                    </h3>

                    <div class="summary-items-list">
                        <?php foreach($items as $it): 
                            $it_sub = $it['price'] * $it['quantity'];
                            $it_img = !empty($it['image']) ? $it['image'] : 'images/Bbouquet.jpg';
                        ?>
                        <div class="summary-item-row">
                            <div class="summary-item-name">
                                <img src="<?php echo htmlspecialchars($it_img); ?>" class="summary-item-img" onerror="this.src='images/Bbouquet.jpg';">
                                <div>
                                    <div><?php echo htmlspecialchars($it['name']); ?></div>
                                    <div style="font-size:12px; color:#888;">Qty: <?php echo $it['quantity']; ?></div>
                                </div>
                            </div>
                            <span style="font-weight:700;">₹<?php echo number_format((float)$it_sub, 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-pricing-row">
                        <span>Items Subtotal:</span>
                        <span>₹<?php echo number_format((float)$total, 2); ?></span>
                    </div>

                    <div class="summary-pricing-row">
                        <span>Delivery Charge:</span>
                        <span style="color:#15803d; font-weight:700;">FREE</span>
                    </div>

                    <div class="summary-total-row">
                        <span>Total Payable:</span>
                        <span>₹<?php echo number_format((float)$total, 2); ?></span>
                    </div>

                    <div style="margin-top:24px;">
                        <button type="submit" class="bb-btn bb-btn-primary" style="width:100%; padding:14px; font-size:15px;">
                            <i class="fa-solid fa-check"></i> Confirm & Place Order
                        </button>
                    </div>

                    <div style="text-align:center; margin-top:14px;">
                        <a href="cart.php" style="color:var(--text-muted); font-size:13px; text-decoration:none;">
                            <i class="fa-solid fa-arrow-left"></i> Return to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>