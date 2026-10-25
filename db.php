<?php
$conn = new mysqli("localhost", "root", "", "user_auth_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>