<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";

$user_id = $_SESSION['user_id'] ?? 0;
$category = "special";

$stmt = $conn->prepare("SELECT * FROM products WHERE LOWER(category) = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
$product_count = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Surprises | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .occasion-hero {
            background: linear-gradient(135deg, #fce4ec 0%, #fff0f5 100%);
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .occasion-hero h1 {
            font-family: var(--font-serif);
            font-size: 36px;
            color: #3b232c;
            margin-bottom: 8px;
        }

        .occasion-hero p {
            color: var(--text-muted);
            font-size: 15px;
            max-width: 600px;
            margin: 0 auto 20px auto;
        }

        .collection-wrapper {
            max-width: 1140px;
            width: 92%;
            margin: 30px auto 60px auto;
            flex: 1;
        }

        .toolbar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-field {
            position: relative;
            min-width: 280px;
        }

        .search-field i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }

        .search-field input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border-radius: var(--radius-full);
            border: 1.5px solid var(--border-color);
            outline: none;
            font-family: inherit;
            background: white;
            transition: var(--transition);
        }

        .search-field input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(156, 74, 98, 0.12);
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <!-- OCCASION HERO -->
    <div class="occasion-hero">
        <h1>🌟 Special Moments & Surprises</h1>
        <p>Curated luxury surprises for graduations, promotions, heartfelt apologies, and random acts of kindness</p>
    </div>

    <!-- MAIN COLLECTION -->
    <main class="collection-wrapper">
        <div class="toolbar-row">
            <div class="search-field">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="searchInput" placeholder="Search special gifts...">
            </div>
            <div style="font-size:14px; font-weight:700; color:var(--primary);">
                <i class="fa-solid fa-star"></i> <?php echo $product_count; ?> Handcrafted Special Gifts Available
            </div>
        </div>

        <?php if($product_count > 0): ?>
        <div class="bb-product-grid">
            <?php while($row = $result->fetch_assoc()): 
                $pid = (int)$row['id'];
                $pname = htmlspecialchars($row['name']);
                $pprice = number_format((float)$row['price'], 2);
                $pimg = !empty($row['image']) ? $row['image'] : 'images/teddy.jpg';
                $pdesc = htmlspecialchars($row['description'] ?? 'Artisanal gift designed to touch hearts and create smiles.');
            ?>
            <div class="bb-product-card" data-product-card>
                <div class="bb-product-img-wrap">
                    <a href="product_details.php?id=<?php echo $pid; ?>">
                        <img src="<?php echo htmlspecialchars($pimg); ?>" alt="<?php echo $pname; ?>" onerror="this.src='images/teddy.jpg';">
                    </a>
                </div>

                <div class="bb-product-body">
                    <h3 class="bb-product-name"><?php echo $pname; ?></h3>
                    <p class="bb-product-desc"><?php echo $pdesc; ?></p>

                    <div class="bb-product-footer">
                        <span class="bb-product-price">₹<?php echo $pprice; ?></span>
                        <div style="display:flex; gap:6px;">
                            <button type="button" class="bb-btn bb-btn-primary" style="padding: 7px 14px; font-size: 13px;" onclick="addToCart(<?php echo $pid; ?>)">
                                <i class="fa-solid fa-bag-shopping"></i> Add
                            </button>
                            <button type="button" class="bb-btn bb-btn-secondary" style="padding: 7px 10px; font-size: 13px;" onclick="addToWishlist(<?php echo $pid; ?>)" title="Save to Wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
        <div style="text-align:center; padding:60px 20px; background:white; border-radius:var(--radius-lg); border:1px solid var(--border-color);">
            <i class="fa-solid fa-star" style="font-size:36px; color:var(--primary); margin-bottom:12px;"></i>
            <h3>No Special products found at this moment</h3>
            <p style="color:var(--text-muted); margin-top:6px;">Check back soon for fresh arrivals!</p>
        </div>
        <?php endif; ?>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

    <script>
        document.getElementById("searchInput").addEventListener("input", function () {
            let filter = this.value.toLowerCase().trim();
            let cards = document.querySelectorAll("[data-product-card]");
            cards.forEach(function (card) {
                if (card.innerText.toLowerCase().includes(filter)) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            });
        });

        function addToCart(productId) {
            fetch("add_to_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Added to cart! 🌸");
                    location.reload();
                } else {
                    alert(data);
                }
            });
        }

        function addToWishlist(productId) {
            fetch("add_to_wishlist.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Saved to wishlist ❤️");
                } else {
                    alert(data);
                }
            });
        }
    </script>
</body>
</html>