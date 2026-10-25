<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Clear admin session completely
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_user']);
unset($_SESSION['admin_name']);

// Redirect to customer storefront, completely leaving admin
header("Location: BBhome.php");
exit();
?>
