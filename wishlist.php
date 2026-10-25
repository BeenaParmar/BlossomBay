<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: BBlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
  "SELECT p.id, p.name, p.price, p.image, p.description
   FROM wishlist w
   JOIN products p ON w.product_id = p.id
   WHERE w.user_id = ?"
);

if(!$stmt){
    die("SQL Error: ".$conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_count = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .wishlist-wrapper {
            max-width: 1080px;
            width: 92%;
            margin: 35px auto 60px auto;
            flex: 1;
        }

        .page-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header-title {
            font-family: var(--font-serif);
            font-size: 30px;
            color: #3b232c;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .wishlist-badge {
            background: white;
            border: 1px solid var(--border-color);
            padding: 8px 18px;
            border-radius: var(--radius-full);
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            box-shadow: var(--shadow-subtle);
        }

        .wishlist-card {
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: var(--shadow-subtle);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .wishlist-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }

        .wishlist-img-wrap {
            width: 100%;
            height: 200px;
            overflow: hidden;
            position: relative;
            background: #fdfaf8;
        }

        .wishlist-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .wishlist-card:hover .wishlist-img-wrap img {
            transform: scale(1.06);
        }

        .wishlist-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .wishlist-name {
            font-size: 15px;
            font-weight: 700;
            color: #2b2b2b;
            margin-bottom: 6px;
        }

        .wishlist-price {
            font-size: 17px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .wishlist-actions {
            margin-top: auto;
            display: flex;
            gap: 8px;
        }

        .btn-move-cart {
            flex: 1;
            padding: 9px 12px;
            font-size: 13px;
        }

        .btn-remove-wishlist {
            background: #fee2e2;
            color: #b91c1c;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: var(--radius-full);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .btn-remove-wishlist:hover {
            background: #ef4444;
            color: white;
        }

        .empty-wishlist-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 60px 30px;
            text-align: center;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            max-width: 550px;
            margin: 40px auto;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="wishlist-wrapper">
        <div class="page-header-row">
            <h1 class="page-header-title">
                <i class="fa-solid fa-heart" style="color:var(--primary);"></i> My Saved Wishlist
            </h1>
            <div class="wishlist-badge">
                <i class="fa-regular fa-bookmark"></i> <?php echo $wishlist_count; ?> <?php echo ($wishlist_count == 1) ? 'Item' : 'Items'; ?> Saved
            </div>
        </div>

        <?php if ($wishlist_count > 0): ?>
        <div class="bb-product-grid">
            <?php while ($row = $result->fetch_assoc()): 
                $p_id = (int)$row['id'];
                $p_name = htmlspecialchars($row['name']);
                $p_price = number_format((float)$row['price'], 2);
                $p_img = !empty($row['image']) ? $row['image'] : 'images/Bbouquet.jpg';
            ?>
            <div class="wishlist-card">
                <div class="wishlist-img-wrap">
                    <a href="product_details.php?id=<?php echo $p_id; ?>">
                        <img src="<?php echo htmlspecialchars($p_img); ?>" alt="<?php echo $p_name; ?>" onerror="this.src='images/Bbouquet.jpg';">
                    </a>
                </div>

                <div class="wishlist-body">
                    <h3 class="wishlist-name"><?php echo $p_name; ?></h3>
                    <div class="wishlist-price">₹<?php echo $p_price; ?></div>

                    <div class="wishlist-actions">
                        <button type="button" class="bb-btn bb-btn-primary btn-move-cart" onclick="moveToCart(<?php echo $p_id; ?>)">
                            <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                        </button>
                        <button type="button" class="btn-remove-wishlist" onclick="removeFromWishlist(<?php echo $p_id; ?>)" title="Remove item">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div style="text-align:center; margin-top:30px;">
            <a href="BBhome.php" class="bb-btn bb-btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Continue Shopping
            </a>
        </div>

        <?php else: ?>

        <div class="empty-wishlist-card">
            <div class="empty-icon">
                <i class="fa-regular fa-heart"></i>
            </div>
            <h2 style="font-family:var(--font-serif); font-size:24px; color:#333; margin-bottom:8px;">Your Wishlist is Empty</h2>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:22px;">
                Save your favorite flowers, custom cakes, and luxury surprises so you can easily find them later!
            </p>
            <a href="BBhome.php" class="bb-btn bb-btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Explore Collections
            </a>
        </div>

        <?php endif; ?>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

    <script>
        function removeFromWishlist(productId){
            if(!confirm("Remove this item from your wishlist?")) return;

            fetch("remove_wishlist.php",{
                method:"POST",
                headers:{ "Content-Type":"application/json" },
                body:JSON.stringify({ product_id: productId })
            })
            .then(res => res.text())
            .then(resp => {
                if(resp.includes("success")){
                    location.reload();
                }else{
                    alert(resp);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Error removing item");
            });
        }

        function moveToCart(productId){
            fetch('add_to_cart.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({product_id: productId})
            })
            .then(res => res.text())
            .then(data=>{
                if(data.trim() === "success"){
                    alert("Added to cart! 🌸");
                    location.reload();
                } else {
                    alert(data);
                }
            });
        }
    </script>
</body>
</html>
