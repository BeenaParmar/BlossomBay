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
    WHERE c.user_id = '$user_id'
");

$total = 0;
$item_count = $query->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .cart-wrapper {
            max-width: 1080px;
            width: 92%;
            margin: 35px auto 60px auto;
            flex: 1;
        }

        .cart-header-title {
            font-family: var(--font-serif);
            font-size: 30px;
            color: #3b232c;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cart-layout {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .cart-items-col {
            flex: 1.8;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .cart-item-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }

        .cart-item-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .cart-item-img {
            width: 90px;
            height: 90px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            background: #fdfaf8;
            border: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .cart-item-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-size: 16px;
            font-weight: 700;
            color: #2b2b2b;
            margin-bottom: 4px;
        }

        .cart-item-unit-price {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .cart-item-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .qty-controls {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-full);
            background: #faf7f8;
            overflow: hidden;
        }

        .qty-btn {
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .qty-btn:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .qty-value {
            font-size: 14px;
            font-weight: 700;
            padding: 0 10px;
            color: #2b2b2b;
        }

        .remove-link {
            color: #dc2626;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: var(--radius-full);
            background: #fee2e2;
            transition: var(--transition);
        }

        .remove-link:hover {
            background: #ef4444;
            color: white;
        }

        .cart-item-subtotal {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
        }

        /* Order Summary Sidebar */
        .cart-summary-col {
            flex: 1;
            position: sticky;
            top: 90px;
        }

        .summary-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 26px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
        }

        .summary-card h3 {
            font-family: var(--font-serif);
            font-size: 20px;
            color: #3b232c;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f5ebeb;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            color: #555;
        }

        .summary-line.total {
            font-size: 18px;
            font-weight: 800;
            color: #2b2b2b;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed #f0e6e8;
        }

        .summary-line.total span:last-child {
            color: var(--primary);
            font-size: 22px;
        }

        /* Empty State */
        .empty-cart-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 60px 30px;
            text-align: center;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            max-width: 550px;
            margin: 40px auto;
        }

        .empty-cart-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .cart-layout {
                flex-direction: column;
            }
            .cart-summary-col {
                width: 100%;
                position: static;
            }
            .cart-item-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .cart-item-img {
                width: 100%;
                height: 160px;
            }
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="cart-wrapper">
        <h1 class="cart-header-title">
            <i class="fa-solid fa-bag-shopping" style="color:var(--primary);"></i> Your Shopping Cart
        </h1>

        <?php if($item_count > 0): ?>
        <div class="cart-layout">
            <!-- Left Items List -->
            <div class="cart-items-col">
                <?php
                while($row = $query->fetch_assoc()):
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
                    $item_name = htmlspecialchars($row['name']);
                    $item_price = number_format((float)$row['price'], 2);
                    $item_sub = number_format((float)$subtotal, 2);
                    $item_img = !empty($row['image']) ? $row['image'] : 'images/Bbouquet.jpg';
                    $pid = $row['product_id'];
                    $qty = $row['quantity'];
                ?>
                <div class="cart-item-card">
                    <div class="cart-item-img">
                        <img src="<?php echo htmlspecialchars($item_img); ?>" alt="<?php echo $item_name; ?>" onerror="this.src='images/Bbouquet.jpg';">
                    </div>

                    <div class="cart-item-details">
                        <h3 class="cart-item-name"><?php echo $item_name; ?></h3>
                        <div class="cart-item-unit-price">Price: ₹<?php echo $item_price; ?> each</div>

                        <div class="cart-item-actions">
                            <!-- Qty buttons -->
                            <div class="qty-controls">
                                <form action="update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="qty-btn" title="Decrease">&minus;</button>
                                </form>
                                <span class="qty-value"><?php echo $qty; ?></span>
                                <form action="update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="qty-btn" title="Increase">+</button>
                                </form>
                            </div>

                            <a href="remove_cart.php?product_id=<?php echo $pid; ?>" 
                               class="remove-link"
                               onclick="return confirm('Remove <?php echo addslashes($item_name); ?> from your cart?');">
                                <i class="fa-regular fa-trash-can"></i> Remove
                            </a>

                            <div class="cart-item-subtotal">₹<?php echo $item_sub; ?></div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Right Summary Card -->
            <div class="cart-summary-col">
                <div class="summary-card">
                    <h3>Order Summary</h3>
                    <div class="summary-line">
                        <span>Items Subtotal:</span>
                        <span>₹<?php echo number_format((float)$total, 2); ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Delivery Fee:</span>
                        <span style="color:#15803d; font-weight:700;">FREE</span>
                    </div>
                    <div class="summary-line">
                        <span>Taxes & Handling:</span>
                        <span>Included</span>
                    </div>

                    <div class="summary-line total">
                        <span>Grand Total:</span>
                        <span>₹<?php echo number_format((float)$total, 2); ?></span>
                    </div>

                    <div style="margin-top:22px;">
                        <a href="checkout.php" class="bb-btn bb-btn-primary" style="width:100%; padding:14px;">
                            <i class="fa-solid fa-lock"></i> Proceed to Checkout
                        </a>
                    </div>

                    <div style="text-align:center; margin-top:14px;">
                        <a href="BBhome.php" style="color:var(--text-muted); font-size:13px; text-decoration:none;">
                            <i class="fa-solid fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>

        <!-- Empty Cart -->
        <div class="empty-cart-card">
            <div class="empty-cart-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h2 style="font-family:var(--font-serif); font-size:24px; color:#333; margin-bottom:8px;">Your Cart is Empty</h2>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:22px;">
                Looks like you haven't added any fresh blossoms or treats to your cart yet.
            </p>
            <a href="BBhome.php" class="bb-btn bb-btn-primary">
                <i class="fa-solid fa-gift"></i> Discover Collections
            </a>
        </div>

        <?php endif; ?>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>
