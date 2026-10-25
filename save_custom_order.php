<?php
include "db.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $product_type = $_POST['product_type'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Remove common fields
    unset($_POST['product_type'], $_POST['name'], $_POST['email'], $_POST['phone']);

    // Remaining fields = customization
    $details = json_encode($_POST);

    $sql = "INSERT INTO custom_orders (product_type, name, email, phone, details)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $product_type, $name, $email, $phone, $details);

    if($stmt->execute()){
        echo "<script>alert('Custom order submitted successfully'); window.location='BBhome.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

}
?>