<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

if(!isset($_GET['id'])){
    header("Location: BBhome.php");
    exit;
}

$product_id = intval($_GET['id']);
$query = $conn->query("SELECT * FROM products WHERE id='$product_id'");
$product = $query->fetch_assoc();

if(!$product){
    echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>Product not found. <a href='BBhome.php'>Return Home</a></div>";
    exit;
}

$price = number_format((float)$product['price'], 2);
$stock = (int)($product['stock'] ?? 10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .details-wrapper {
            max-width: 1040px;
            width: 92%;
            margin: 35px auto 60px auto;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .product-showcase {
            background: white;
            border-radius: var(--radius-lg);
            padding: 35px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            display: flex;
            gap: 45px;
            align-items: flex-start;
        }

        .product-visual-col {
            flex: 1;
            max-width: 440px;
            width: 100%;
        }

        .main-image-wrap {
            width: 100%;
            height: 400px;
            border-radius: var(--radius-md);
            overflow: hidden;
            background: #fdfaf8;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid var(--border-color);
            position: relative;
        }

        .main-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .main-image-wrap:hover img {
            transform: scale(1.05);
        }

        .product-info-col {
            flex: 1.2;
            display: flex;
            flex-direction: column;
        }

        .category-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.6px;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 4px 12px;
            border-radius: var(--radius-full);
            width: fit-content;
            margin-bottom: 10px;
        }

        .product-headline {
            font-family: var(--font-serif);
            font-size: 28px;
            font-weight: 700;
            color: #2b2b2b;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .product-meta-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .pid-tag {
            font-family: monospace;
            background: #f1f5f9;
            color: #475569;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
        }

        .stock-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 700;
            color: #15803d;
        }

        .stock-tag.low {
            color: #b45309;
        }

        .product-price-bar {
            font-size: 30px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5ebeb;
            display: flex;
            align-items: baseline;
            gap: 10px;
        }

        .product-price-bar .tax-note {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 400;
        }

        .info-block {
            margin-bottom: 20px;
        }

        .info-block h3 {
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #555;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .info-block p {
            font-size: 14px;
            color: #4a4a4a;
            line-height: 1.6;
        }

        .action-cta-row {
            display: flex;
            gap: 14px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .perks-grid {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f5ebeb;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .perk-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #4a5568;
        }

        .perk-item i {
            color: var(--primary);
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .product-showcase {
                flex-direction: column;
                gap: 25px;
                padding: 20px;
            }
            .product-visual-col {
                max-width: 100%;
            }
            .main-image-wrap {
                height: 280px;
            }
            .perks-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="details-wrapper">
        <div class="breadcrumb">
            <a href="BBhome.php">Home</a>
            <i class="fa-solid fa-angle-right" style="font-size: 11px;"></i>
            <span><?php echo htmlspecialchars($product['category'] ?? 'Gifts'); ?></span>
            <i class="fa-solid fa-angle-right" style="font-size: 11px;"></i>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>

        <div class="product-showcase">
            <!-- Visual Column -->
            <div class="product-visual-col">
                <div class="main-image-wrap">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         onerror="this.src='images/Bbouquet.jpg';">
                </div>
            </div>

            <!-- Details Column -->
            <div class="product-info-col">
                <div class="category-chip">
                    <i class="fa-solid fa-sparkles"></i> <?php echo htmlspecialchars($product['category'] ?? 'Special Gift'); ?>
                </div>

                <h1 class="product-headline"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="product-meta-row">
                    <span class="pid-tag">PID: #PRD-<?php echo $product['id']; ?></span>
                    <?php if ($stock > 0): ?>
                        <span class="stock-tag"><i class="fa-solid fa-circle-check"></i> In Stock (<?php echo $stock; ?> available)</span>
                    <?php else: ?>
                        <span class="stock-tag low"><i class="fa-solid fa-triangle-exclamation"></i> Limited Fresh Stock</span>
                    <?php endif; ?>
                </div>

                <div class="product-price-bar">
                    ₹<?php echo $price; ?>
                    <span class="tax-note">Inclusive of all taxes</span>
                </div>

                <?php if (!empty($product['description'])): ?>
                <div class="info-block">
                    <h3><i class="fa-regular fa-file-lines" style="color:var(--primary);"></i> Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($product['details'])): ?>
                <div class="info-block">
                    <h3><i class="fa-solid fa-circle-info" style="color:var(--primary);"></i> Handcrafting & Details</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['details'])); ?></p>
                </div>
                <?php endif; ?>

                <div class="action-cta-row">
                    <button class="bb-btn bb-btn-primary" style="flex:1; padding: 14px 24px;" onclick="addToCart(<?php echo $product['id']; ?>)">
                        <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                    </button>
                    <button class="bb-btn bb-btn-secondary" onclick="addToWishlist(<?php echo $product['id']; ?>)" title="Save to Wishlist">
                        <i class="fa-regular fa-heart"></i> Wishlist
                    </button>
                </div>

                <div class="perks-grid">
                    <div class="perk-item">
                        <i class="fa-solid fa-truck-fast"></i>
                        <span>Express Delivery Available</span>
                    </div>
                    <div class="perk-item">
                        <i class="fa-solid fa-shield-heart"></i>
                        <span>100% Freshness Guaranteed</span>
                    </div>
                    <div class="perk-item">
                        <i class="fa-solid fa-gift"></i>
                        <span>Complimentary Message Card</span>
                    </div>
                    <div class="perk-item">
                        <i class="fa-solid fa-award"></i>
                        <span>Earn 5 Reward Points</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

    <script>
        function addToCart(productId){
            fetch('add_to_cart.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({product_id: productId})
            })
            .then(res => res.text())
            .then(data=>{
                if(data.trim() === "success"){
                    alert("Added to your shopping cart! 🌸");
                    location.reload();
                } else {
                    alert(data);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Failed to add to cart");
            });
        }

        function addToWishlist(productId){
            fetch('add_to_wishlist.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({product_id: productId})
            })
            .then(res => res.text())
            .then(data=>{
                if(data.trim() === "success"){
                    alert("Added to your wishlist ❤️");
                } else {
                    alert(data);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Failed to update wishlist");
            });
        }
    </script>
</body>
</html>