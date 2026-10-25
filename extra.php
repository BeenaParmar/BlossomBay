<?php
session_start();
include "db.php";   // ADD THIS LINE

if (!isset($_SESSION['user_id'])) {
    header("Location: BBlogin.html");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>title</title>
    <style>
      :root{
      --bg-1: #FFF8F2;   /* soft cream */
      --pastel-pink: #FFDDE6;
      --pastel-peach: #FFEFD6;
      --pastel-mint: #E8FFF4;
      --accent: #C48EBA; /* muted mauve */
      --muted: #6b6b6b;
      --glass: rgba(255,255,255,0.75);
      --glass-2: rgba(255,255,255,0.60);
      --radius: 14px;
      --shadow: 0 8px 24px rgba(17,24,39,0.08);
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 40px;
    background: linear-gradient(135deg, #d86c8b, #ff9bb3);
    color: white;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.nav-title {
    font-size: 22px;
    font-weight: bold;
}

.logo img {
    height: 50px;   /* Adjust size as needed */
    width: auto;
    display: block;
}

.logo img {
    height: 50px;
    transition: 0.3s;
}

.logo img:hover {
    transform: scale(1.05);
}

/* RIGHT SECTION */
.nav-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* Icons */
/* ICON STYLE */
.nav-icon {
    position: relative;
    font-size: 20px;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.nav-icon:hover {
    transform: scale(1.15);
}

/* CART BADGE */
.cart-icon {
    position: relative;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -10px;
    background: white;
    color: #d86c8b;
    font-size: 12px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 50%;
}

/* SEARCH */
.search-box input {
    padding: 6px 12px;
    border-radius: 20px;
    border: none;
    outline: none;
}
.dropdown {
    position: relative;
    display: inline-block;
}

.dropbtn {
    background-color: white;
    color: #d86c8b;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: white;
    min-width: 180px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
    border-radius: 10px;
    overflow: hidden;
}

.dropdown-content a {
    color: #333;
    padding: 12px 15px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #FFDDE6;
}

.dropdown:hover .dropdown-content {
    display: block;
}

            .slider {
    width: 100%;
    height: 500px;         /* Change height if needed */
    overflow: hidden;
    position: relative;
    border-radius: 12px;
}

.slides {
    width: 400%;           /* 4 images = 400% */
    height: 500px;
    display: flex;
    animation: slide 12s infinite;
}

.slide-box {
        width: 50%;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
        color: white;
    }



.slides img {
    width: 60%;
    height: 450px;         /* Same as slider height */
    object-fit: cover;
    align-items: center;
}

@keyframes slide {
    0%   { margin-left: 0; }
    20%  { margin-left: 0; }

    25%  { margin-left: -100%; }
    45%  { margin-left: -100%; }

    50%  { margin-left: -200%; }
    70%  { margin-left: -200%; }

    75%  { margin-left: -300%; }
    95%  { margin-left: -300%; }
}

table{
  padding: 5px;
  background-color: #FFDDE6;
  border-radius: 20px;
}

.table-image {
    /* box-shadow:#b35f72;      */
    border-collapse: separate;
    border-spacing: 30px;          
    margin: 20px;
    background: #FFDDE6;           
    padding: 5px;
    border-radius: 10px; 
          
}



.table-image img {
    width: 250px;
    height: 250px;
    object-fit: cover;
    border-radius: 20px;           
    border: #b35f72;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    padding: 12px;
}

td{
  padding: 10px;
}
  .h1{
    background-color: rgb(248, 232, 205);
    height: 60px;
    text-align: center;
    padding-top: 7px;
    /* text-decoration: underline; */

  }

    td:hover{
      padding: 15px;
      background-color:#d8a8b3;
      border-radius: 20px;
    }
    .best{
      background-color: rgb(248, 232, 205);
      height: 60px;
      text-align: center;
      padding-top: 15px;
      width: 100%;
    }

    /* .bests{
      background-color:#C48EBA
      height: 60px;
      text-align: center;
      padding-top: 15px;
      width: 100%;
    } */

    .bestcake{
      margin-top: 25px;
      padding: 10px;
    }
    .bestie{
      border-radius: 20px;
      border: 2px solid black;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2); 
      padding: 8px;
object-fit: cover;
    }

.bestcake{
  /* border: 3px solid black;        */
    border-collapse: separate;
    border-spacing: 30px;          
    margin: 20px;
    background: #FFDDE6;           
    padding: 5px;
    border-radius: 10px;
    width: 70%;
}
    </style>
  </head>
  <body>
<nav class="navbar">
    <div class="logo">
        <a href="BBhome.php">
            <img src="images/logo.png.png" alt="">
        </a>
    </div>

     <div class="nav-title">
Home
    </div>

    <!-- RIGHT SECTION -->
<div class="nav-right">

    <!-- Search -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search gifts...">
    </div>

    <!-- Wishlist -->
    <a href="wishlist.php" class="nav-icon">
        <i class="fa-regular fa-heart"></i>
    </a>

    <!-- Cart -->
   <a href="cart.php" class="nav-icon cart-icon">
    🛒
    <?php
        $user_id = $_SESSION['user_id'];

        $countQuery = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id='$user_id'");
        $row = $countQuery->fetch_assoc();
        $total = $row['total'] ?? 0;
    ?>
    <span class="cart-count"><?php echo $total; ?></span>
</a>
        <div class="dropdown">
        <button class="dropbtn">Shop ▾</button>
        <div class="dropdown-content">
            <a href="BBdiscovery.php">Discovery</a>
            <a href="BBglobe.php">Globe</a>
            <a href="BBoneday.php">OneDay</a>
            <a href="profile.php">Profile</a>
        </div>
    </div>
</nav>
    <hr>
    <center>
      <div class="slider">
    <div class="slides">
       <img src="images/slider1.jpg" alt="">
       <img src="images/slider2.jpg" alt="">
       <img src="images/slider3.jpg" alt="">
       <img src="images/slider4.jpg" alt="">
    </div>
</div>
    </center>
    <hr>
    <center>
      <h1 class="h1">Ocassions</h1>
    </center>
<div >
<center>
<table>
  <tr class="table-image">
  <td><a href="BBoccasionbirthday.php"><img src="images/occasion1.jpg" width="300" height="350" border="1" alt=""></a></td>
	<td><a href="BBoccasionanniversary.php"><img src="images/occasion2.jpg" width="300" height="350" border="1" alt=""></a></td>
  <td><a href="BBoccasionwedding.php"><img src="https://www.logoai.com/uploads/output/2024/12/12/18cde5b3840421395ec3c17307e7e985.jpg" width="300" height="350" border="1" alt=""></a></td>

</tr>
<tr class="table-image">
  <td><a href="BBoccasionvalentine.php"><img src="https://cdn.vectorstock.com/i/1000v/36/12/happy-valentines-day-logo-creative-template-vector-21513612.jpg" width="300" height="350" border="1" alt=""></a></td>
	<td><a href="BBspecial.php"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSk4CXVely6YESCByV24NynlicDR9_el_sR1nFUrp4OmaFXi9IX_Ay4FNHlKQ92sG0OrEA&usqp=CAU" width="300" height="350" border="1" alt=""></a></td>
  <td><a href="BBooccasionfestive.php"><img src="https://c8.alamy.com/comp/PT81A2/festival-word-with-orange-color-suitable-for-card-icon-or-typography-logo-design-PT81A2.jpg" width="300" height="350" border="1" alt=""></a></td>

</tr>
</table>
</center>
<hr>
<center>
  <h1 class="best">Bestseller</h1>
</center>

<center>
  <h2 class="best" >Cakes</h2>
</center>

<div>
<table class="bestcake">
<tr class="bestie">
	<td><a href="details6.html"><img src="images/cake1.png" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details7.html"><img src="https://lirp.cdn-website.com/0bf608c4/dms3rep/multi/opt/girl%2Bdrip%2Bcake-00f775bb-640w.jpg" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details8.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQoLk_I_2PuAHscpl0r3ZN4XoZNZ2IWfHlR6E8Ix22gX96o8JHvsL8WVFHZkFHjjnyH-6c&usqp=CAU" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details9.html"><img src="https://cdn.dotpe.in/longtail/store-items/7695373/QL4TMJpu.jpeg" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details10.html"><img src="https://assets.giftalove.com/resources/common/giftimages/productimage2/love-cake-for-anniversary.jpg" width="150" height="200" border="0" alt=""></a></td>
  <td><a href="details11.html"><img src="https://bentocakesnoida.in/cdn/shop/files/WhatsAppImage2025-08-10at11.43.26AM.jpg?v=1755339557&width=1200" width="150" height="200" border="0" alt=""></a></td>
</tr>
</table>  
</div>
<center>
  <h2 class="best">Bouquets</h2>
</center>
<div>
<table class="bestcake">
<tr class="bestie">
	<td><a href="details12.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3z0Wzh4NOLRZAh-r8errJda-UofJC6DhbX-w-6C436ksXJsQ3TE4DpbSTXfgHyE8GpVo&usqp=CAU" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details13.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQujJw-0ObHhmcwqvjM0I4VEGZozZr5GLv2sgqkoCVF3f2r7nswZ6anWfVHZCy9VSdd2BQ&usqp=CAU" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details14.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTcyryXwDq1O0L9_P1iJyoOWukp-kZx8jo5xMVqQenyKuR0qbCVTgZRDYRPNNbKs3lohmQ&usqp=CAU" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details15.html"><img src="https://freshknots.in/wp-content/uploads/2022/12/2.jpg" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details16.html"><img src="https://external-preview.redd.it/aesthetic-bouquet-v0-uNx4MBydgH1jI_36JQ4msruOTq5l9JeIZSpMiin_Ob8.jpeg?width=640&crop=smart&auto=webp&s=7199bd451e7877af9969e6a581f380bddbab7719" width="150" height="200" border="0" alt=""></a></td>
  <td><a href="details17.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBeGIL61oM__bamkc5x8z4zA1edg81b4ZojLNdgw6FJHJ7QsJUBJyRHllu6hjRuyRQg-w&usqp=CAU" width="150" height="200" border="0" alt=""></a></td>
</tr>
</table>  
</div>
<hr>
<center>
  <h2 class="best">Combos</h2>
</center>
<div>
<table class="bestcake">
<tr class="bestie">
	<td><a href="details18.html"><img src="https://imgcdn.floweraura.com/DSC_6414.jpg" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details19.html"><img src="https://imgcdn.floweraura.com/vanilla-cake-n-pink-rose-bunch-for-mom-9779020co-A_0.jpg" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details20.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSoD-G0fHW2jkuSGXk8TbqOilTM2x7NyaS9xg&s" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details21.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQdWXLTPBslECiiOJCqUlmvqEQOPyqp1B64ow&s" width="150" height="200" border="0" alt=""></a></td>
	<td><a href="details22.html"><img src="https://m.media-amazon.com/images/I/71XU5A1FwaL.jpg" width="150" height="200" border="0" alt=""></a></td>
  <td><a href="details23.html"><img src="https://m.media-amazon.com/images/I/514YScSg0xL.jpg" width="150" height="200" border="0" alt=""></a></td>
</tr>
</table>  
</div>
<hr>
<center>
  <h2 class="best">Custom gifts</h2>
</center>
<center>
  <table>
<tr class="table-image">
	<td> <a href="details24.html"><img src="https://www.justbake.in/userfiles/wedding-cake-23.jpg" width="300" height="350" border="0" alt=""></a></td>
	<td><a href="details25.html"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTz-Mgem9SZu-EMZ0tWpDyyqUKhIC0B6zmbcg&s" width="300" height="350" border="0" alt=""></a></td>
	<td><a href="details26.html"><img src="https://market99.com/cdn/shop/files/coffee-mug-with-lid-320-ml-mugs-1-29021793812650.jpg?crop=center&height=1403&v=1737469190&width=1080" width="300" height="350" border="0" alt=""></a></td>
</tr>
<tr class="table-image">
  <td><a href="details27.html"><img src="https://media.landmarkshops.in/cdn-cgi/image/h=750,w=750,q=85,fit=cover/homecentre/1000013874143-1000013874142_04-2100.jpg" width="300" height="350" border="0" alt=""></a></td>	
	<td><a href="details28.html"><img src="https://manubhai.in/SocialMedia/post_artworks/1101201910475813thP2.jpg" width="300" height="350" border="0" alt=""></a></td>
	<td><a href="details29.html"><img src="https://brownliving.in/cdn/shop/products/sunflowers-biodegradable-eco-friendly-phone-case-mobile-cover-snflwr-13-010-tech-accessories-brown-living-251954.jpg?v=1682968047&width=1200" width="300" height="350" border="0" alt=""></a></td>
</tr>
<tr class="table-image">
  <td><a href="details30.html"><img src="https://images.stockcake.com/public/e/b/3/eb3d9618-4d24-4f60-bf9c-17168329eb84_large/elegant-perfume-bottle-stockcake.jpg" width="300" height="350" border="0" alt=""></a></td>	
	<td><a href="details31.html"><img src="https://cdn.thewirecutter.com/wp-content/media/2024/07/online-frames-2048px-1290-2x1-1.jpg?auto=webp&quality=75&crop=1.91:1&width=1200" width="300" height="350" border="0" alt=""></a></td>
	<td><a href="details32.html"><img src="https://m.media-amazon.com/images/I/61xgM6PW83L._AC_UF350,350_QL80_.jpg" width="300" height="350" border="0" alt=""></a></td>
</tr>

</table>


</center>

<!-- --------------- FOOTER SECTION --------------- -->
<footer style="
    background: linear-gradient(rgba(252, 163, 193, 0.9), rgba(250, 123, 109, 0.9));
    opacity: 1;           
    background-size: cover;
    background-position: center;
    padding: 40px 20px;
    font-family: 'Poppins', sans-serif;
">

    <div style="max-width: 1200px; margin: auto; display: flex; 
        flex-wrap: wrap; justify-content: space-between;">

        <!-- About Section -->
        <div style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <h3 style="color:#b35f72">🌸 BlossomBay</h3>
            <p style="line-height: 1.5;">
                A soft pastel-themed platform created with love.  
                Your comfort and seamless experience is our priority.
            </p>
        </div>

        <!-- Contact Section -->
        <div style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <h3 style="color:#b35f72">📞 Contact Us</h3>
            <p>WhatsApp: <a href="https://wa.me/919359537528" 
               style="text-decoration:none; color:#5a4a4a;">+91 93595 37528</a></p>

            <p>Instagram: <a href="https://instagram.com/blossombay.petals" target="_blank"
               style="text-decoration:none; color:#5a4a4a;">@BlossomBay.petals</a></p>

            <p>Email: <a href="mailto:blossombay.petals@gmail.com"
               style="text-decoration:none; color:#5a4a4a;"></a>blossombay.petals@gmail.com</p>
        </div>

        <!-- Quick Links -->
        <div style="flex: 1; min-width: 250px; margin-bottom: 20px;">
            <h3 style="color:#b35f72">🔗 Quick Links</h3>
            <ul style="list-style:none; padding:0; line-height: 1.6;">
                <li><a href="login.html" style="text-decoration:none; color:#5a4a4a;">Login</a></li>
                <li><a href="register.html" style="text-decoration:none; color:#5a4a4a;">Register</a></li>
            </ul>
        </div>
    </div>

    <div style="text-align:center; margin-top:20px; padding-top:10px;
        border-top:1px solid #d8a8b3;">
        © 2025 BlossomBay — All Rights Reserved 🌷
    </div>
</footer>

<script>

function openProduct(productId) {
    window.location.href = "product_details.php?id=" + productId;
}

document.addEventListener('click', function (e) {

    const btn = e.target.closest('.wishlist-btn');
    if (!btn) return;

    const productId = btn.dataset.productId;

    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === 'success') {
            btn.innerText = '❤️';
            btn.disabled = true;
        } else {
            alert(data);
        }
    });

});

function addToCart(productId) {

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === 'success') {
            alert("Added to Cart!");
        } else {
            alert(data);
        }
    });
}

document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll(".product-card");

    cards.forEach(function(card) {
        let title = card.querySelector("h3").innerText.toLowerCase();
        let description = card.querySelector("p").innerText.toLowerCase();

        if (title.includes(filter) || description.includes(filter)) {
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
});

</script>
</body>
</html>