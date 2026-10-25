<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: BBlogin.php");
    exit;
}

// Fetch ALL home products
$result = $conn->query("SELECT * FROM products WHERE category='Home' OR category='home'");
if(!$result){
    die("Query Failed: " . $conn->error);
}

$products = [];
while($row = $result->fetch_assoc()){
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlossomBay — Luxury Gifting & Blooms</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Slider Styling */
        .hero-slider-wrap {
            max-width: 1140px;
            width: 92%;
            margin: 25px auto 40px auto;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-hover);
            position: relative;
        }

        .slider {
            width: 100%;
            overflow: hidden;
            border-radius: var(--radius-lg);
        }

        .slides {
            display: flex;
            width: 400%;
            animation: slideAnimation 16s infinite cubic-bezier(0.4, 0, 0.2, 1);
        }

        .slides img {
            width: 25%;
            height: 380px;
            object-fit: cover;
            display: block;
        }

        @keyframes slideAnimation {
            0%, 20% { margin-left: 0; }
            25%, 45% { margin-left: -100%; }
            50%, 70% { margin-left: -200%; }
            75%, 95% { margin-left: -300%; }
            100% { margin-left: 0; }
        }

        /* Search Section */
        .search-container {
            max-width: 600px;
            margin: -20px auto 40px auto;
            position: relative;
            z-index: 10;
            padding: 0 15px;
        }

        .search-bar-wrap {
            position: relative;
            box-shadow: 0 8px 25px rgba(156, 74, 98, 0.15);
            border-radius: var(--radius-full);
        }

        .search-bar-wrap i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 16px;
        }

        .search-bar-wrap input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border-radius: var(--radius-full);
            border: 2px solid white;
            background: rgba(255, 255, 255, 0.95);
            font-size: 15px;
            font-family: inherit;
            outline: none;
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }

        .search-bar-wrap input:focus {
            border-color: var(--primary-light);
            background: white;
            box-shadow: 0 0 0 4px rgba(156, 74, 98, 0.12);
        }

        /* Section Headers */
        .section-header {
            text-align: center;
            margin: 45px 0 25px 0;
        }

        .section-header h2 {
            font-family: var(--font-serif);
            font-size: 32px;
            color: #3b232c;
            position: relative;
            display: inline-block;
        }

        .section-header h2::after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, #d86c8b, #f7c6d8);
            margin: 8px auto 0 auto;
            border-radius: 2px;
        }

        .section-header p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 6px;
        }

        /* Occasions Grid */
        .occasions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 20px;
            max-width: 1140px;
            width: 92%;
            margin: 0 auto 50px auto;
        }

        .occasion-card {
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            text-decoration: none;
            text-align: center;
            transition: var(--transition);
            display: block;
            position: relative;
        }

        .occasion-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }

        .occasion-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .occasion-card:hover img {
            transform: scale(1.08);
        }

        .occasion-card .occ-title {
            padding: 12px 10px;
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 14px;
            background: #fff;
        }

        /* Content Container */
        .section-wrapper {
            max-width: 1140px;
            width: 92%;
            margin: 0 auto 40px auto;
        }

        /* Custom Gifts Grid */
        .custom-gifts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            max-width: 1140px;
            width: 92%;
            margin: 0 auto 60px auto;
        }

        .custom-item-card {
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            cursor: pointer;
            text-align: center;
            transition: var(--transition);
            position: relative;
        }

        .custom-item-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary);
        }

        .custom-item-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .custom-item-card:hover img {
            transform: scale(1.08);
        }

        .custom-card-label {
            padding: 12px 8px;
            font-weight: 700;
            color: #2b2b2b;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .custom-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(156, 74, 98, 0.85);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: var(--radius-full);
            backdrop-filter: blur(4px);
        }

        /* Custom Order Modal */
        #customPopup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(43, 43, 43, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(5px);
            animation: bbFadeIn 0.25s ease;
        }

        .custom-modal-box {
            background: white;
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 440px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            position: relative;
            border: 1px solid var(--border-color);
            animation: bbFadeDown 0.3s ease;
        }

        .close-modal-btn {
            position: absolute;
            top: 18px;
            right: 18px;
            background: var(--primary-soft);
            border: none;
            color: var(--primary);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .close-modal-btn:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .modal-title {
            font-family: var(--font-serif);
            font-size: 22px;
            color: var(--primary-dark);
            margin-bottom: 6px;
            text-align: center;
        }

        .modal-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- REUSABLE UNIFIED NAVBAR -->
    <?php include "navbar.php"; ?>

    <!-- SLIDER -->
    <div class="hero-slider-wrap">
        <div class="slider">
            <div class="slides">
                <img src="images/slider1.jpg" alt="BlossomBay Blooms">
                <img src="images/slider2.jpg" alt="Fresh Petals">
                <img src="images/slider3.jpg" alt="Handcrafted Cakes">
                <img src="images/slider4.jpg" alt="Luxury Hampers">
            </div>
        </div>
    </div>

    <!-- LIVE SEARCH BAR -->
    <div class="search-container">
        <div class="search-bar-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Search cakes, bouquets, hampers, or perfumes...">
        </div>
    </div>

    <!-- OCCASIONS SECTION -->
    <div class="section-header">
        <h2>Celebrations & Occasions</h2>
        <p>Specially curated collections designed to make every milestone memorable</p>
    </div>

    <div class="occasions-grid">
        <a href="BBoccasionbirthday.php" class="occasion-card">
            <img src="images/occasion1.jpg" alt="Birthday">
            <div class="occ-title"><i class="fa-solid fa-cake-candles" style="color:var(--primary);"></i> Birthday</div>
        </a>
        <a href="BBoccasionanniversary.php" class="occasion-card">
            <img src="images/occasion2.jpg" alt="Anniversary">
            <div class="occ-title"><i class="fa-solid fa-champagne-glasses" style="color:var(--primary);"></i> Anniversary</div>
        </a>
        <a href="BBoccasionwedding.php" class="occasion-card">
            <img src="images/occasion3.jpg" alt="Wedding">
            <div class="occ-title"><i class="fa-solid fa-ring" style="color:var(--primary);"></i> Wedding</div>
        </a>
        <a href="BBoccasionvalentine.php" class="occasion-card">
            <img src="images/occasion4.jpg" alt="Valentine">
            <div class="occ-title"><i class="fa-solid fa-heart" style="color:var(--primary);"></i> Valentine</div>
        </a>
        <a href="BBoccasionspecial.php" class="occasion-card">
            <img src="images/occasion5.jpg" alt="Special">
            <div class="occ-title"><i class="fa-solid fa-star" style="color:var(--primary);"></i> Special</div>
        </a>
        <a href="BBoccasionfestive.php" class="occasion-card">
            <img src="images/occasion6.jpg" alt="Festive">
            <div class="occ-title"><i class="fa-solid fa-wand-magic-sparkles" style="color:var(--primary);"></i> Festive</div>
        </a>
    </div>

    <!-- ================= BESTSELLER PRODUCTS ================= -->
    <?php
    $index = 0;
    ?>

    <!-- CAKES -->
    <div class="section-wrapper">
        <div class="section-header">
            <h2>Artisanal Cakes</h2>
            <p>Freshly baked melt-in-mouth indulgence for sweet celebrations</p>
        </div>

        <div class="bb-product-grid">
            <?php for($i=0; $i<6 && isset($products[$index]); $i++, $index++): 
                $p = $products[$index];
            ?>
            <div class="bb-product-card" data-product-card>
                <div class="bb-product-img-wrap">
                    <a href="product_details.php?id=<?php echo $p['id']; ?>">
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='images/bcake.jpg';">
                    </a>
                </div>
                <div class="bb-product-body">
                    <h3 class="bb-product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <p class="bb-product-desc"><?php echo htmlspecialchars($p['description'] ?? 'Delicious gourmet creation handcrafted with premium ingredients.'); ?></p>
                    <div class="bb-product-footer">
                        <span class="bb-product-price">₹<?php echo number_format((float)$p['price'], 2); ?></span>
                        <a href="product_details.php?id=<?php echo $p['id']; ?>" class="bb-btn bb-btn-primary" style="padding: 8px 16px; font-size: 13px;">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- BOUQUETS -->
    <div class="section-wrapper">
        <div class="section-header">
            <h2>Signature Flower Bouquets</h2>
            <p>Fresh handpicked blossoms carefully wrapped to convey love and admiration</p>
        </div>

        <div class="bb-product-grid">
            <?php for($i=0; $i<6 && isset($products[$index]); $i++, $index++): 
                $p = $products[$index];
            ?>
            <div class="bb-product-card" data-product-card>
                <div class="bb-product-img-wrap">
                    <a href="product_details.php?id=<?php echo $p['id']; ?>">
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='images/Bbouquet.jpg';">
                    </a>
                </div>
                <div class="bb-product-body">
                    <h3 class="bb-product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <p class="bb-product-desc"><?php echo htmlspecialchars($p['description'] ?? 'Elegant bouquet wrapped with signature BlossomBay satin ribbons.'); ?></p>
                    <div class="bb-product-footer">
                        <span class="bb-product-price">₹<?php echo number_format((float)$p['price'], 2); ?></span>
                        <a href="product_details.php?id=<?php echo $p['id']; ?>" class="bb-btn bb-btn-primary" style="padding: 8px 16px; font-size: 13px;">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- COMBOS -->
    <div class="section-wrapper">
        <div class="section-header">
            <h2>Celebration Combos</h2>
            <p>Thoughtfully paired delights: bouquets, cakes, soft teddies, and chocolates</p>
        </div>

        <div class="bb-product-grid">
            <?php for($i=0; $i<6 && isset($products[$index]); $i++, $index++): 
                $p = $products[$index];
            ?>
            <div class="bb-product-card" data-product-card>
                <div class="bb-product-img-wrap">
                    <a href="product_details.php?id=<?php echo $p['id']; ?>">
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" onerror="this.src='images/hamper.jpg';">
                    </a>
                </div>
                <div class="bb-product-body">
                    <h3 class="bb-product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <p class="bb-product-desc"><?php echo htmlspecialchars($p['description'] ?? 'Curated gift combo guaranteed to bring wide smiles.'); ?></p>
                    <div class="bb-product-footer">
                        <span class="bb-product-price">₹<?php echo number_format((float)$p['price'], 2); ?></span>
                        <a href="product_details.php?id=<?php echo $p['id']; ?>" class="bb-btn bb-btn-primary" style="padding: 8px 16px; font-size: 13px;">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- CUSTOM GIFTS -->
    <div class="section-wrapper">
        <div class="section-header">
            <h2>Personalized & Custom Gifts</h2>
            <p>Click any item to customize colors, messages, flavors, and designs</p>
        </div>

        <div class="custom-gifts-grid">
            <div class="custom-item-card" onclick="openPopup(this)" data-product="Cake">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/cake7.jpg" alt="Cake">
                <div class="custom-card-label"><i class="fa-solid fa-cake-candles" style="color:var(--primary);"></i> Designer Cake</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Bouquet of Flowers">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/bouquet7.jfif" alt="Bouquet">
                <div class="custom-card-label"><i class="fa-solid fa-spa" style="color:var(--primary);"></i> Flower Bouquet</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Cup">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/cup.webp" alt="Cup">
                <div class="custom-card-label"><i class="fa-solid fa-mug-saucer" style="color:var(--primary);"></i> Ceramic Mug</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Flower Vase">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/vase.jpg" alt="Vase">
                <div class="custom-card-label"><i class="fa-solid fa-wine-glass" style="color:var(--primary);"></i> Glass Vase</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Jewellery">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/jwellery2.jpg" alt="Jewellery">
                <div class="custom-card-label"><i class="fa-solid fa-gem" style="color:var(--primary);"></i> Jewellery Set</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Phone Case">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/phone case.webp" alt="Phone Case">
                <div class="custom-card-label"><i class="fa-solid fa-mobile-screen" style="color:var(--primary);"></i> Phone Case</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Perfume">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/perfumes.webp" alt="Perfume">
                <div class="custom-card-label"><i class="fa-solid fa-spray-can-sparkles" style="color:var(--primary);"></i> Luxury Perfume</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Photo Frame">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/frame.webp" alt="Photo Frame">
                <div class="custom-card-label"><i class="fa-regular fa-image" style="color:var(--primary);"></i> Photo Frame</div>
            </div>

            <div class="custom-item-card" onclick="openPopup(this)" data-product="Wishing Card">
                <span class="custom-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Customize</span>
                <img src="images/cards.jpg" alt="Wishing Card">
                <div class="custom-card-label"><i class="fa-regular fa-envelope" style="color:var(--primary);"></i> Greeting Card</div>
            </div>
        </div>
    </div>

    <!-- UNIFIED REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

    <!-- CUSTOM ORDER POPUP MODAL -->
    <div id="customPopup">
        <div class="custom-modal-box">
            <button class="close-modal-btn" onclick="closePopup()">&times;</button>
            <h3 class="modal-title" id="popupTitle">Custom Order</h3>
            <p class="modal-subtitle">Share your exact specifications with our artisan team</p>

            <form method="POST" action="save_custom_order.php">
                <input type="hidden" name="product_type" id="productType">

                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:18px;">
                    <input type="text" name="name" class="bb-input" placeholder="Your Full Name" required>
                    <input type="email" name="email" class="bb-input" placeholder="Your Email Address" required>
                    <input type="text" name="phone" class="bb-input" placeholder="Your Phone Number" required>
                    
                    <div id="dynamicFields" style="display:flex; flex-direction:column; gap:12px;"></div>
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" class="bb-btn bb-btn-primary" style="flex:1;">
                        <i class="fa-regular fa-paper-plane"></i> Submit Request
                    </button>
                    <button type="button" class="bb-btn bb-btn-secondary" onclick="closePopup()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT JAVASCRIPT -->
    <script>
        // Realtime Search
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase().trim();
            let cards = document.querySelectorAll("[data-product-card]");

            cards.forEach(function(card) {
                let text = card.innerText.toLowerCase();
                if(text.includes(filter)) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            });
        });

        // Open Custom Gift Modal
        function openPopup(element) {
            let product = element.getAttribute("data-product");

            document.getElementById("customPopup").style.display = "flex";
            document.getElementById("popupTitle").innerText = "Customize " + product;
            document.getElementById("productType").value = product;

            let fields = document.getElementById("dynamicFields");
            fields.innerHTML = "";
            let html = "";

            if (product === "Cake") {
                html = `
                    <input type="text" name="color" class="bb-input" placeholder="Theme / Cake Color (e.g. Pastel Pink)">
                    <input type="text" name="flavor" class="bb-input" placeholder="Flavor Choice (e.g. Red Velvet, Chocolate Fudge)">
                    <textarea name="custom" class="bb-textarea" rows="2" placeholder="Message on cake or specific design instructions"></textarea>
                `;
            } else if (product === "Bouquet of Flowers") {
                html = `
                    <input type="text" name="flower_type" class="bb-input" placeholder="Flower Preference (Roses, Lilies, Orchids, Carnations)">
                    <input type="text" name="flower_color" class="bb-input" placeholder="Color Palette (Red, White, Pastel Peach)">
                    <input type="text" name="wrapper_color" class="bb-input" placeholder="Wrapper Ribbon & Paper Color">
                `;
            } else if (product === "Cup") {
                html = `
                    <input type="text" name="cup_color" class="bb-input" placeholder="Cup Base Color">
                    <input type="text" name="cup_type" class="bb-input" placeholder="Type (Ceramic, Magic Mug, Insulated Travel)">
                    <textarea name="material" class="bb-textarea" rows="2" placeholder="Custom text, quotes, or photo print details"></textarea>
                `;
            } else if (product === "Flower Vase") {
                html = `
                    <input type="text" name="vase_color" class="bb-input" placeholder="Vase Color Preference">
                    <input type="text" name="vase_material" class="bb-input" placeholder="Material (Crystal, Ceramic, Terracotta)">
                    <textarea name="vase_design" class="bb-textarea" rows="2" placeholder="Height and design style"></textarea>
                `;
            } else if (product === "Jewellery") {
                html = `
                    <input type="text" name="jewellery_type" class="bb-input" placeholder="Item Type (Necklace, Bracelet, Earring set)">
                    <input type="text" name="material" class="bb-input" placeholder="Metal Tone (Rose Gold, 925 Sterling Silver, Pearl)">
                    <textarea name="design" class="bb-textarea" rows="2" placeholder="Engraving or charm details"></textarea>
                `;
            } else if (product === "Phone Case") {
                html = `
                    <input type="text" name="phone_model" class="bb-input" placeholder="Exact Phone Model (e.g. iPhone 15 Pro, S24)">
                    <input type="text" name="case_color" class="bb-input" placeholder="Background Color Preference">
                    <textarea name="design" class="bb-textarea" rows="2" placeholder="Print name, pattern, or resin flower design"></textarea>
                `;
            } else if (product === "Perfume") {
                html = `
                    <input type="text" name="fragrance_type" class="bb-input" placeholder="Fragrance Family (Floral, Citrus, Woody, Vanilla)">
                    <input type="text" name="intensity" class="bb-input" placeholder="Intensity (Subtle Daily, Long-lasting Eau De Parfum)">
                    <textarea name="notes" class="bb-textarea" rows="2" placeholder="Custom label name or personalized note"></textarea>
                `;
            } else if (product === "Photo Frame") {
                html = `
                    <input type="text" name="frame_size" class="bb-input" placeholder="Frame Dimensions (e.g. 6x8, 8x10 inches)">
                    <input type="text" name="frame_color" class="bb-input" placeholder="Frame Texture & Color (Rustic Wood, Gold Leaf, White)">
                    <textarea name="photo_details" class="bb-textarea" rows="2" placeholder="Collage details or quote to include"></textarea>
                `;
            } else if (product === "Wishing Card") {
                html = `
                    <input type="text" name="card_theme" class="bb-input" placeholder="Occasion / Theme">
                    <textarea name="message" class="bb-textarea" rows="3" placeholder="Exact heartfelt message to be handwritten"></textarea>
                `;
            }

            fields.innerHTML = html;
        }

        function closePopup() {
            document.getElementById("customPopup").style.display = "none";
        }

        window.onclick = function(e) {
            const popup = document.getElementById("customPopup");
            if (e.target === popup) {
                closePopup();
            }
        };
    </script>
</body>
</html>