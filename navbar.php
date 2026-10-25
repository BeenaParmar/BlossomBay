<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "db.php";

$nav_user_id = $_SESSION['user_id'] ?? 0;
$nav_cart_count = 0;
if ($nav_user_id > 0 && isset($conn)) {
    $cart_cnt_res = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id='$nav_user_id'");
    if ($cart_cnt_res) {
        $nav_cart_row = $cart_cnt_res->fetch_assoc();
        $nav_cart_count = (int)($nav_cart_row['total'] ?? 0);
    }
}
?>
<nav class="bb-navbar">
    <div class="bb-nav-left">
        <a href="BBhome.php" class="bb-logo" title="BlossomBay Home">
            <img src="images/logo.png.png" alt="BlossomBay Logo">
        </a>
        <div class="bb-dropdown">
            <button class="bb-dropbtn">
                <span>Occasions</span> <i class="fa-solid fa-chevron-down" style="font-size: 11px;"></i>
            </button>
            <div class="bb-dropdown-content">
                <a href="BBoccasionbirthday.php"><i class="fa-solid fa-cake-candles"></i> Birthday</a>
                <a href="BBoccasionanniversary.php"><i class="fa-solid fa-champagne-glasses"></i> Anniversary</a>
                <a href="BBoccasionvalentine.php"><i class="fa-solid fa-heart"></i> Valentine's Day</a>
                <a href="BBoccasionwedding.php"><i class="fa-solid fa-ring"></i> Wedding</a>
                <a href="BBoccasionfestive.php"><i class="fa-solid fa-wand-magic-sparkles"></i> Festive</a>
                <a href="BBoccasionspecial.php"><i class="fa-solid fa-star"></i> Special Surprises</a>
            </div>
        </div>
    </div>

    <div class="bb-nav-right">
        <a href="BBglobe.php" class="bb-nav-btn" title="Send Gifts Across the Globe">
            <i class="fa-solid fa-earth-americas"></i> <span>Send Gifts</span>
        </a>
        <a href="rewards.php" class="bb-nav-btn" title="Your Reward Points">
            <i class="fa-solid fa-gift"></i> <span>Rewards</span>
        </a>
        <a href="my_orders.php" class="bb-nav-btn" title="My Orders & Tracking">
            <i class="fa-solid fa-box-open"></i> <span>Orders</span>
        </a>
        <a href="wishlist.php" class="bb-nav-icon-link" title="Wishlist">
            <i class="fa-regular fa-heart"></i>
        </a>
        <a href="cart.php" class="bb-nav-icon-link" title="Cart">
            <i class="fa-solid fa-bag-shopping"></i>
            <?php if ($nav_cart_count > 0): ?>
                <span class="bb-badge-count"><?php echo $nav_cart_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="profile.php" class="bb-nav-icon-link" title="My Profile">
            <i class="fa-regular fa-user"></i>
        </a>
    </div>
</nav>
