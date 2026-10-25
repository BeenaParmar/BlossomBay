<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: BBlogin.html");
    exit;
}

// Fetch ALL home products
$result = $conn->query("SELECT * FROM products WHERE category='home'");

if(!$result){
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Home</title>

<style>
/* KEEP YOUR CSS EXACTLY SAME — NO CHANGE */
:root{
--bg-1:#FFF8F2;
--pastel-pink:#FFDDE6;
--pastel-peach:#FFEFD6;
--pastel-mint:#E8FFF4;
--accent:#C48EBA;
--muted:#6b6b6b;
--glass:rgba(255,255,255,0.75);
--glass-2:rgba(255,255,255,0.60);
--radius:14px;
--shadow:0 8px 24px rgba(17,24,39,0.08);
font-family:system-ui;
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

<!-- NAVBAR (UNCHANGED) -->
<nav class="navbar">
<div class="logo">
<a href="BBhome.php">
<img src="images/logo.png.png">
</a>
</div>

<div class="nav-title">Home</div>

<div class="nav-right">

<div class="search-box">
<input type="text" id="searchInput" placeholder="Search gifts...">
</div>

<a href="wishlist.php" class="nav-icon">❤️</a>

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

</div>
</nav>

<hr>

<!-- SLIDER (UNCHANGED) -->
<center>
<div class="slider">
<div class="slides">
<img src="images/slider1.jpg">
<img src="images/slider2.jpg">
<img src="images/slider3.jpg">
<img src="images/slider4.jpg">
</div>
</div>
</center>

<hr>

<!-- OCCASIONS (UNCHANGED) -->
<center><h1 class="h1">Ocassions</h1></center>

<center>
<table>
<tr class="table-image">
<td><a href="BBoccasionbirthday.php"><img src="images/occasion1.jpg"></a></td>
<td><a href="BBoccasionanniversary.php"><img src="images/occasion2.jpg"></a></td>
<td><a href="BBoccasionwedding.php"><img src="images/occasion3.jpg"></a></td>
</tr>

<tr class="table-image">
<td><a href="BBoccasionvalentine.php"><img src="images/occasion4.jpg"></a></td>
<td><a href="BBspecial.php"><img src="images/occasion5.jpg"></a></td>
<td><a href="BBooccasionfestive.php"><img src="images/occasion6.jpg"></a></td>
</tr>
</table>
</center>

<hr>

<!-- ================= BESTSELLER ================= -->

<center><h1 class="best">Bestseller</h1></center>

<?php
// Reset pointer using array (important for reuse)
$products = [];
while($row = $result->fetch_assoc()){
    $products[] = $row;
}

$index = 0;
?>

<!-- CAKES -->
<center><h2 class="best">Cakes</h2></center>
<table class="bestcake">
<tr class="bestie">
<?php for($i=0; $i<6 && isset($products[$index]); $i++, $index++) { ?>
<td>
<a href="product_details.php?id=<?php echo $products[$index]['id']; ?>">
<img src="<?php echo $products[$index]['image']; ?>" width="150" height="200">
</a>
</td>
<?php } ?>
</tr>
</table>

<!-- BOUQUETS -->
<center><h2 class="best">Bouquets</h2></center>
<table class="bestcake">
<tr class="bestie">
<?php for($i=0; $i<6 && isset($products[$index]); $i++, $index++) { ?>
<td>
<a href="product_details.php?id=<?php echo $products[$index]['id']; ?>">
<img src="<?php echo $products[$index]['image']; ?>" width="150" height="200">
</a>
</td>
<?php } ?>
</tr>
</table>

<hr>

<!-- COMBOS -->
<center><h2 class="best">Combos</h2></center>
<table class="bestcake">
<tr class="bestie">
<?php for($i=0; $i<6 && isset($products[$index]); $i++, $index++) { ?>
<td>
<a href="product_details.php?id=<?php echo $products[$index]['id']; ?>">
<img src="<?php echo $products[$index]['image']; ?>" width="150" height="200">
</a>
</td>
<?php } ?>
</tr>
</table>

<hr>

<!-- CUSTOM GIFTS -->
<center><h2 class="best">Custom gifts</h2></center>

<table>
<tr class="table-image">
<?php 
$count = 0;
while(isset($products[$index])) { ?>
<td>
<a href="product_details.php?id=<?php echo $products[$index]['id']; ?>">
<img src="<?php echo $products[$index]['image']; ?>" width="300" height="350">
</a>
</td>
<?php 
$index++;
$count++;
if($count % 3 == 0) echo "</tr><tr class='table-image'>";
} 
?>
</tr>
</table>

<!-- FOOTER (UNCHANGED) -->
<footer>
<!-- keep your original footer exactly -->
</footer>

<!-- JS (SAFE) -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
let filter = this.value.toLowerCase();
let cards = document.querySelectorAll("td");

cards.forEach(function(card) {
if(card.innerText.toLowerCase().includes(filter)) {
card.style.display = "";
} else {
card.style.display = "none";
}
});
});
</script>

</body>
</html>