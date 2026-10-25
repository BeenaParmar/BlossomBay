<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "user_auth_db");

if ($conn->connect_error) {
    die("DB FAILED");
}

/* FETCH PRODUCTS */
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {

        echo "<h2>".$row['name']."</h2>";
        echo "<p>".$row['description']."</p>";
        echo "<p>Price: ".$row['price']."</p>";
        echo "<img src='".$row['image']."' width='150'><hr>";

    }

} else {
    echo "No products found.";
}

$conn->close();
?>
