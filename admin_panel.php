<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

// Strict Admin Gate
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$admin_user = $_SESSION['admin_name'] ?? $_SESSION['admin_user'] ?? 'Administrator';
$alert_msg = "";
$alert_type = "";

// ----------------------------------------------------
// HANDLE ACTIONS (Order Status, Payments, Products)
// ----------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. UPDATE ORDER STATUS (Delivery status)
    if ($action === 'update_order_status') {
        $order_id = intval($_POST['order_id'] ?? 0);
        $new_status = trim($_POST['order_status'] ?? '');
        $allowed = ['Placed', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

        if ($order_id > 0 && in_array($new_status, $allowed)) {
            $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $order_id);
            if ($stmt->execute()) {
                $alert_msg = "Order #$order_id delivery status updated to '$new_status'.";
                $alert_type = "success";
            } else {
                $alert_msg = "Failed to update order status.";
                $alert_type = "danger";
            }
        }
    }

    // 2. UPDATE PAYMENT STATUS (e.g. COD Received / Mark Paid)
    if ($action === 'update_payment_status') {
        $order_id = intval($_POST['order_id'] ?? 0);
        $new_payment = trim($_POST['payment_status'] ?? '');
        
        if ($order_id > 0 && in_array($new_payment, ['Paid', 'Pending'])) {
            $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_payment, $order_id);
            if ($stmt->execute()) {
                // If marked Paid, award 5 loyalty reward points to user if not already given
                if ($new_payment === 'Paid') {
                    $o_res = $conn->query("SELECT user_id, reward_given FROM orders WHERE id = '$order_id'");
                    if ($o_res && $o_row = $o_res->fetch_assoc()) {
                        if ($o_row['reward_given'] == 0) {
                            $uid = $o_row['user_id'];
                            $conn->query("UPDATE users SET reward_points = reward_points + 5 WHERE id = '$uid'");
                            $conn->query("UPDATE orders SET reward_given = 1 WHERE id = '$order_id'");
                        }
                    }
                }
                $alert_msg = "Payment status for Order #$order_id marked as '$new_payment'.";
                $alert_type = "success";
            } else {
                $alert_msg = "Failed to update payment status.";
                $alert_type = "danger";
            }
        }
    }

    // 3. TOGGLE PRODUCT STOCK (In Stock / Out of Stock)
    if ($action === 'toggle_stock') {
        $product_id = intval($_POST['product_id'] ?? 0);
        $current_stock = intval($_POST['current_stock'] ?? 0);
        $new_stock = ($current_stock > 0) ? 0 : 15; // Flip between 0 (Out of stock) and 15 (In stock)

        $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_stock, $product_id);
        if ($stmt->execute()) {
            $alert_msg = "Product #$product_id stock updated to " . ($new_stock > 0 ? "In Stock ($new_stock items)" : "Out of Stock") . ".";
            $alert_type = "success";
        }
    }

    // 4. ADD NEW PRODUCT
    if ($action === 'add_product') {
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'Home');
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 15);
        $description = trim($_POST['description'] ?? '');
        $details = trim($_POST['details'] ?? '');
        $image_path = 'images/Bbouquet.jpg'; // default fallback

        // Handle File Upload if provided
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image_file']['name']);
            $target_dest = 'images/' . $file_name;
            if (move_uploaded_file($file_tmp, $target_dest)) {
                $image_path = $target_dest;
            }
        } elseif (!empty($_POST['image_url'])) {
            $image_path = trim($_POST['image_url']);
        }

        if (!empty($name) && $price > 0) {
            $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, description, details, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdisss", $name, $category, $price, $stock, $description, $details, $image_path);
            if ($stmt->execute()) {
                $alert_msg = "Product '$name' added successfully to catalog!";
                $alert_type = "success";
            } else {
                $alert_msg = "Failed to add product: " . $conn->error;
                $alert_type = "danger";
            }
        } else {
            $alert_msg = "Please provide valid product name and price.";
            $alert_type = "danger";
        }
    }

    // 5. EDIT PRODUCT
    if ($action === 'edit_product') {
        $product_id = intval($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? 'Home');
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $details = trim($_POST['details'] ?? '');
        
        $image_update = "";
        $img_val = "";
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image_file']['name']);
            $target_dest = 'images/' . $file_name;
            if (move_uploaded_file($file_tmp, $target_dest)) {
                $image_update = ", image = ?";
                $img_val = $target_dest;
            }
        } elseif (!empty($_POST['image_url'])) {
            $image_update = ", image = ?";
            $img_val = trim($_POST['image_url']);
        }

        if ($product_id > 0 && !empty($name) && $price > 0) {
            if (!empty($image_update)) {
                $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ?, details = ?" . $image_update . " WHERE id = ?");
                $stmt->bind_param("ssdisssi", $name, $category, $price, $stock, $description, $details, $img_val, $product_id);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ?, description = ?, details = ? WHERE id = ?");
                $stmt->bind_param("ssdissi", $name, $category, $price, $stock, $description, $details, $product_id);
            }
            if ($stmt->execute()) {
                $alert_msg = "Product #$product_id updated successfully!";
                $alert_type = "success";
            } else {
                $alert_msg = "Failed to update product.";
                $alert_type = "danger";
            }
        }
    }

    // 6. DELETE PRODUCT
    if ($action === 'delete_product') {
        $product_id = intval($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            if ($stmt->execute()) {
                $alert_msg = "Product #$product_id has been removed from catalog.";
                $alert_type = "success";
            } else {
                $alert_msg = "Failed to remove product: " . $conn->error;
                $alert_type = "danger";
            }
        }
    }
}

// ----------------------------------------------------
// FETCH AGGREGATED METRICS & STATISTICS
// ----------------------------------------------------
$total_rev_res = $conn->query("SELECT SUM(grand_total) as rev FROM orders WHERE payment_status = 'Paid'");
$total_revenue = number_format((float)($total_rev_res->fetch_assoc()['rev'] ?? 0), 2);

$total_orders_res = $conn->query("SELECT COUNT(*) as cnt FROM orders");
$total_orders = (int)($total_orders_res->fetch_assoc()['cnt'] ?? 0);

$pending_deliv_res = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE order_status IN ('Placed', 'Confirmed', 'Processing', 'Shipped')");
$pending_deliveries = (int)($pending_deliv_res->fetch_assoc()['cnt'] ?? 0);

$cod_pending_res = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE payment_status != 'Paid' AND (payment_method LIKE '%COD%' OR payment_method LIKE '%Cash%')");
$cod_pending_count = (int)($cod_pending_res->fetch_assoc()['cnt'] ?? 0);

$total_prods_res = $conn->query("SELECT COUNT(*) as cnt FROM products");
$total_products = (int)($total_prods_res->fetch_assoc()['cnt'] ?? 0);

$out_of_stock_res = $conn->query("SELECT COUNT(*) as cnt FROM products WHERE stock <= 0");
$out_of_stock_count = (int)($out_of_stock_res->fetch_assoc()['cnt'] ?? 0);

// Fetch All Orders with User details
$orders_query = $conn->query("
    SELECT o.*, u.full_name as customer_name, u.email as customer_email, u.mobile as customer_mobile
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
");

// Fetch All Products
$products_query = $conn->query("SELECT * FROM products ORDER BY id DESC");

// Fetch Custom Gift Requests
$custom_orders_query = $conn->query("SELECT * FROM custom_orders ORDER BY id DESC LIMIT 50");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Control Center | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --admin-dark: #2d1822;
            --admin-sidebar: #3b232c;
            --admin-accent: #9c4a62;
            --admin-bg: #fcf6f8;
        }

        body {
            background-color: var(--admin-bg);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Top Admin Bar */
        .admin-topbar {
            background: linear-gradient(135deg, #2d1822 0%, #4a2136 100%);
            color: white;
            padding: 12px 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-brand img {
            height: 42px;
            filter: brightness(1.1);
        }

        .admin-tag {
            background: #9c4a62;
            color: white;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        /* KPI Stat Cards */
        .admin-content {
            max-width: 1240px;
            width: 94%;
            margin: 30px auto 60px auto;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }

        .metric-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 20px 18px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .metric-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .metric-icon-wrap.rev { background: #dcfce7; color: #15803d; }
        .metric-icon-wrap.orders { background: #e0f2fe; color: #0284c7; }
        .metric-icon-wrap.pending { background: #fef3c7; color: #b45309; }
        .metric-icon-wrap.cod { background: #fee2e2; color: #b91c1c; }
        .metric-icon-wrap.prods { background: #fdf2f4; color: #9c4a62; }

        .metric-data h4 {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            color: #8c8588;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .metric-data .metric-value {
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
        }

        /* Navigation Tabs */
        .admin-tabs-nav {
            display: flex;
            gap: 10px;
            background: white;
            padding: 8px 12px;
            border-radius: var(--radius-full);
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .admin-tab-btn {
            background: transparent;
            border: none;
            padding: 10px 22px;
            border-radius: var(--radius-full);
            font-size: 14px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .admin-tab-btn.active, .admin-tab-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* Alert Banners */
        .alert-box {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Tables & Cards */
        .admin-section-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 26px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-subtle);
            margin-bottom: 30px;
        }

        .section-top-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .section-top-toolbar h3 {
            font-family: var(--font-serif);
            font-size: 22px;
            color: #2b2b2b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-table-container {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
            text-align: left;
        }

        .admin-table th {
            background: #faf7f8;
            padding: 12px 14px;
            color: #555;
            font-weight: 700;
            border-bottom: 2px solid var(--border-color);
            white-space: nowrap;
        }

        .admin-table td {
            padding: 14px;
            border-bottom: 1px solid #f5ebeb;
            vertical-align: middle;
        }

        .admin-table tr:hover td {
            background: #fdfafb;
        }

        /* Stock Pill & Switch */
        .stock-toggle-btn {
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .stock-in {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .stock-in:hover {
            background: #bbf7d0;
        }

        .stock-out {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
        .stock-out:hover {
            background: #fecaca;
        }

        /* Status Dropdowns */
        .status-select {
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border-color);
            font-size: 13px;
            font-weight: 600;
            outline: none;
            background: white;
            cursor: pointer;
        }

        /* Quick Action Buttons */
        .quick-action-btn {
            background: var(--primary-soft);
            color: var(--primary);
            border: 1px solid var(--border-color);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }

        .quick-action-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* Modals */
        .admin-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            animation: bbFadeIn 0.2s ease;
        }

        .admin-modal-card {
            background: white;
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 580px;
            padding: 30px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            border: 1px solid var(--border-color);
        }

        .modal-close-x {
            position: absolute;
            top: 18px;
            right: 18px;
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #64748b;
        }

        .modal-close-x:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- TOP ADMIN NAVBAR -->
    <header class="admin-topbar">
        <div class="admin-brand">
            <a href="admin_panel.php" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <img src="images/logo.png.png" alt="BlossomBay">
            </a>
            <span class="admin-tag"><i class="fa-solid fa-shield-halved"></i> Control Portal</span>
        </div>

        <div class="admin-user-info">
            <span style="font-size:13px; font-weight:600;">
                <i class="fa-regular fa-user-circle" style="color:#ff9bb3;"></i> <?php echo htmlspecialchars($admin_user); ?>
            </span>
            <a href="BBhome.php" target="_blank" class="bb-btn bb-btn-secondary" style="padding:7px 15px; font-size:12px;" title="View Live Customer Storefront">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Storefront
            </a>
            <a href="admin_logout.php" class="bb-btn bb-btn-danger" style="padding:7px 15px; font-size:12px;">
                <i class="fa-solid fa-power-off"></i> Logout
            </a>
        </div>
    </header>

    <main class="admin-content">

        <!-- ALERT BANNER -->
        <?php if (!empty($alert_msg)): ?>
        <div class="alert-box alert-<?php echo $alert_type; ?>">
            <div>
                <i class="fa-solid <?php echo ($alert_type === 'success') ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?>"></i>
                <?php echo htmlspecialchars($alert_msg); ?>
            </div>
            <button type="button" style="background:none; border:none; cursor:pointer; font-size:16px;" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php endif; ?>

        <!-- METRIC KPI CARDS -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon-wrap rev"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                <div class="metric-data">
                    <h4>Total Revenue</h4>
                    <div class="metric-value">₹<?php echo $total_revenue; ?></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon-wrap orders"><i class="fa-solid fa-box-open"></i></div>
                <div class="metric-data">
                    <h4>Total Orders</h4>
                    <div class="metric-value"><?php echo $total_orders; ?></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon-wrap pending"><i class="fa-solid fa-truck-ramp-box"></i></div>
                <div class="metric-data">
                    <h4>In Progress</h4>
                    <div class="metric-value"><?php echo $pending_deliveries; ?></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon-wrap cod"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                <div class="metric-data">
                    <h4>COD Pending</h4>
                    <div class="metric-value"><?php echo $cod_pending_count; ?></div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon-wrap prods"><i class="fa-solid fa-spa"></i></div>
                <div class="metric-data">
                    <h4>Total Products</h4>
                    <div class="metric-value"><?php echo $total_products; ?></div>
                </div>
            </div>
        </div>

        <!-- TABS BAR -->
        <div class="admin-tabs-nav">
            <button class="admin-tab-btn active" onclick="switchTab('orders-tab', this)">
                <i class="fa-solid fa-boxes-packing"></i> Orders & Delivery (<?php echo $total_orders; ?>)
            </button>
            <button class="admin-tab-btn" onclick="switchTab('products-tab', this)">
                <i class="fa-solid fa-layer-group"></i> Products & Inventory (<?php echo $total_products; ?>)
            </button>
            <button class="admin-tab-btn" onclick="switchTab('custom-tab', this)">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Custom Gift Orders
            </button>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 1: ORDERS & DELIVERY STATUS & PAYMENT MANAGEMENT   -->
        <!-- ======================================================= -->
        <div id="orders-tab" class="tab-content-block">
            <div class="admin-section-card">
                <div class="section-top-toolbar">
                    <h3><i class="fa-solid fa-dolly" style="color:var(--primary);"></i> Order Management & Payment Tracking</h3>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <input type="text" id="orderSearch" class="bb-input" style="width:240px; padding:8px 14px;" placeholder="Search Order # or Name..." onkeyup="filterOrdersTable()">
                        <select id="statusFilter" class="status-select" onchange="filterOrdersTable()">
                            <option value="">All Delivery Statuses</option>
                            <option value="Placed">Placed</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Processing">Processing</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <select id="payFilter" class="status-select" onchange="filterOrdersTable()">
                            <option value="">All Payments</option>
                            <option value="Paid">Paid Online</option>
                            <option value="Pending">Pending (COD/Unpaid)</option>
                        </select>
                    </div>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Tracking ID</th>
                                <th>Customer</th>
                                <th>Date & Total</th>
                                <th>Items</th>
                                <th>Payment Details</th>
                                <th>Delivery Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($orders_query->num_rows > 0):
                                while ($o = $orders_query->fetch_assoc()):
                                    $oid = (int)$o['id'];
                                    $onum = htmlspecialchars($o['order_number']);
                                    $otrk = htmlspecialchars($o['tracking_id'] ?? 'TRK-BB-'.$oid);
                                    $cname = htmlspecialchars($o['customer_name'] ?? 'Guest Customer');
                                    $cphone = htmlspecialchars($o['customer_mobile'] ?? '');
                                    $cemail = htmlspecialchars($o['customer_email'] ?? '');
                                    $odate = date("M d, Y", strtotime($o['order_date'] ?? date('Y-m-d')));
                                    $ototal = number_format((float)$o['grand_total'], 2);
                                    $del_status = $o['order_status'] ?? 'Placed';
                                    $pay_status = $o['payment_status'] ?? 'Pending';
                                    $pay_method = $o['payment_method'] ?? 'COD';
                                    $is_cod = (stripos($pay_method, 'COD') !== false || stripos($pay_method, 'Cash') !== false);

                                    // Fetch item summary
                                    $items_res = $conn->query("SELECT product_name, quantity, price FROM order_items WHERE order_id = '$oid'");
                                    $items_text = [];
                                    while($it = $items_res->fetch_assoc()){
                                        $items_text[] = $it['product_name'] . " (x" . $it['quantity'] . ")";
                                    }
                                    $items_display = !empty($items_text) ? implode(", ", $items_text) : "1x Gourmet Gift";
                            ?>
                            <tr data-order-row 
                                data-num="<?php echo strtolower($onum); ?>" 
                                data-name="<?php echo strtolower($cname); ?>"
                                data-status="<?php echo $del_status; ?>"
                                data-pay="<?php echo $pay_status; ?>">
                                <td>
                                    <b style="color:var(--primary); font-family:monospace;">#<?php echo $onum; ?></b>
                                </td>
                                <td>
                                    <span style="font-family:monospace; background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:12px;">
                                        <?php echo $otrk; ?>
                                    </span>
                                </td>
                                <td>
                                    <b><?php echo $cname; ?></b>
                                    <?php if(!empty($cphone)): ?>
                                        <div style="font-size:12px; color:#64748b;"><i class="fa-solid fa-phone"></i> <?php echo $cphone; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-size:12px; color:#64748b;"><?php echo $odate; ?></div>
                                    <b style="font-size:14px; color:var(--primary);">₹<?php echo $ototal; ?></b>
                                </td>
                                <td style="max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo htmlspecialchars($items_display); ?>">
                                    <?php echo htmlspecialchars($items_display); ?>
                                </td>
                                <td>
                                    <!-- Payment Info & Received COD Toggle -->
                                    <div style="margin-bottom:6px;">
                                        <?php if ($pay_status === 'Paid'): ?>
                                            <span style="background:#dcfce7; color:#15803d; font-size:11px; font-weight:700; padding:3px 8px; border-radius:12px;">
                                                <i class="fa-solid fa-check"></i> Paid
                                            </span>
                                        <?php else: ?>
                                            <span style="background:#fffbeb; color:#b45309; font-size:11px; font-weight:700; padding:3px 8px; border-radius:12px;">
                                                <i class="fa-solid fa-clock"></i> Payment Pending
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div style="font-size:11.5px; color:#64748b; margin-bottom:6px;">
                                        <?php echo $is_cod ? 'Cash on Delivery' : 'Online (QR / Prepaid)'; ?>
                                    </div>

                                    <!-- Quick Mark Paid / Change Payment -->
                                    <form method="POST" action="admin_panel.php" style="display:inline;">
                                        <input type="hidden" name="action" value="update_payment_status">
                                        <input type="hidden" name="order_id" value="<?php echo $oid; ?>">
                                        <?php if ($pay_status !== 'Paid'): ?>
                                            <input type="hidden" name="payment_status" value="Paid">
                                            <button type="submit" class="quick-action-btn" style="background:#dcfce7; color:#15803d; border-color:#bbf7d0;" title="Click once cash is collected">
                                                <i class="fa-solid fa-hand-holding-dollar"></i> Collect & Mark Paid
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="payment_status" value="Pending">
                                            <button type="submit" class="quick-action-btn" style="font-size:11px; color:#888;" title="Revert to Pending">
                                                Set Pending
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td>
                                    <!-- Delivery Status Form -->
                                    <form method="POST" action="admin_panel.php" style="display:flex; align-items:center; gap:6px;">
                                        <input type="hidden" name="action" value="update_order_status">
                                        <input type="hidden" name="order_id" value="<?php echo $oid; ?>">
                                        <select name="order_status" class="status-select" onchange="this.form.submit()">
                                            <option value="Placed" <?php echo ($del_status === 'Placed') ? 'selected' : ''; ?>>Placed</option>
                                            <option value="Confirmed" <?php echo ($del_status === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="Processing" <?php echo ($del_status === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                            <option value="Shipped" <?php echo ($del_status === 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="Delivered" <?php echo ($del_status === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo ($del_status === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="my_orders.php" target="_blank" class="quick-action-btn" title="View Customer Invoice">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr><td colspan="8" style="text-align:center; padding:30px;">No customer orders placed yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 2: PRODUCTS, STOCK MANAGEMENT & ADD / REMOVE        -->
        <!-- ======================================================= -->
        <div id="products-tab" class="tab-content-block" style="display:none;">
            <div class="admin-section-card">
                <div class="section-top-toolbar">
                    <h3><i class="fa-solid fa-boxes-stacked" style="color:var(--primary);"></i> Catalog & Stock Management</h3>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <input type="text" id="prodSearch" class="bb-input" style="width:220px; padding:8px 14px;" placeholder="Search product name..." onkeyup="filterProductsTable()">
                        <select id="catFilter" class="status-select" onchange="filterProductsTable()">
                            <option value="">All Categories</option>
                            <option value="Home">Home</option>
                            <option value="Birthday">Birthday</option>
                            <option value="Anniversary">Anniversary</option>
                            <option value="Valentine">Valentine</option>
                            <option value="Wedding">Wedding</option>
                            <option value="Festive">Festive</option>
                            <option value="Special">Special</option>
                        </select>
                        <button type="button" class="bb-btn bb-btn-primary" style="padding:8px 18px; font-size:13px;" onclick="openAddProductModal()">
                            <i class="fa-solid fa-plus"></i> Add New Product
                        </button>
                    </div>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table" id="productsTable">
                        <thead>
                            <tr>
                                <th>PID</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock Status</th>
                                <th>Inventory Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($products_query->num_rows > 0):
                                while ($p = $products_query->fetch_assoc()):
                                    $pid = (int)$p['id'];
                                    $pname = htmlspecialchars($p['name']);
                                    $pcat = htmlspecialchars($p['category']);
                                    $pprice = number_format((float)$p['price'], 2);
                                    $pstock = (int)($p['stock'] ?? 0);
                                    $pimg = !empty($p['image']) ? $p['image'] : 'images/Bbouquet.jpg';
                                    $pdesc = htmlspecialchars($p['description'] ?? '');
                                    $pdet = htmlspecialchars($p['details'] ?? '');
                                    $in_stock = ($pstock > 0);
                            ?>
                            <tr data-prod-row 
                                data-name="<?php echo strtolower($pname); ?>" 
                                data-cat="<?php echo strtolower($pcat); ?>"
                                data-stock="<?php echo $in_stock ? 'in' : 'out'; ?>">
                                <td><b>#<?php echo $pid; ?></b></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($pimg); ?>" 
                                         alt="<?php echo $pname; ?>" 
                                         style="width:45px; height:45px; border-radius:8px; object-fit:cover; border:1px solid #eee;"
                                         onerror="this.src='images/Bbouquet.jpg';">
                                </td>
                                <td>
                                    <b><?php echo $pname; ?></b>
                                </td>
                                <td>
                                    <span style="background:var(--primary-soft); color:var(--primary); font-size:11.5px; font-weight:700; padding:3px 10px; border-radius:12px;">
                                        <?php echo $pcat; ?>
                                    </span>
                                </td>
                                <td><b>₹<?php echo $pprice; ?></b></td>
                                <td>
                                    <!-- Stock Toggle Button -->
                                    <form method="POST" action="admin_panel.php" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_stock">
                                        <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                        <input type="hidden" name="current_stock" value="<?php echo $pstock; ?>">
                                        <?php if ($in_stock): ?>
                                            <button type="submit" class="stock-toggle-btn stock-in" title="Click to mark as Out of Stock">
                                                <i class="fa-solid fa-circle-check"></i> In Stock
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="stock-toggle-btn stock-out" title="Click to mark as In Stock">
                                                <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                                <td>
                                    <span><?php echo $pstock; ?> units</span>
                                </td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" class="quick-action-btn" onclick='openEditModal(<?php echo json_encode($p); ?>)'>
                                            <i class="fa-regular fa-pen-to-square"></i> Edit
                                        </button>
                                        <!-- Delete Form -->
                                        <form method="POST" action="admin_panel.php" onsubmit="return confirm('Permanently remove product &quot;<?php echo addslashes($pname); ?>&quot; from catalog?');" style="display:inline;">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                                            <button type="submit" class="quick-action-btn" style="color:#b91c1c; background:#fee2e2; border-color:#fca5a5;" title="Remove Product">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr><td colspan="8" style="text-align:center; padding:30px;">No products found in catalog.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- TAB 3: CUSTOM GIFT REQUESTS                            -->
        <!-- ======================================================= -->
        <div id="custom-tab" class="tab-content-block" style="display:none;">
            <div class="admin-section-card">
                <div class="section-top-toolbar">
                    <h3><i class="fa-solid fa-wand-magic-sparkles" style="color:var(--primary);"></i> Personalized Custom Orders</h3>
                    <p style="color:var(--text-muted); font-size:13px;">Customer requests submitted via homepage customization tool</p>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Type</th>
                                <th>Customer Name</th>
                                <th>Contact Details</th>
                                <th>Customization Notes</th>
                                <th>Date Requested</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($custom_orders_query && $custom_orders_query->num_rows > 0):
                                while ($c = $custom_orders_query->fetch_assoc()):
                            ?>
                            <tr>
                                <td>#<?php echo $c['id']; ?></td>
                                <td><b><?php echo htmlspecialchars($c['product_type']); ?></b></td>
                                <td><?php echo htmlspecialchars($c['name']); ?></td>
                                <td>
                                    <div><i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($c['email']); ?></div>
                                    <div><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($c['phone']); ?></div>
                                </td>
                                <td style="max-width:320px;"><?php echo nl2br(htmlspecialchars($c['details'])); ?></td>
                                <td><?php echo date("M d, Y • h:i A", strtotime($c['created_at'])); ?></td>
                            </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                            <tr><td colspan="6" style="text-align:center; padding:30px;">No custom orders recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- ======================================================= -->
    <!-- MODAL: ADD NEW PRODUCT                                  -->
    <!-- ======================================================= -->
    <div class="admin-modal" id="addProductModal">
        <div class="admin-modal-card">
            <button class="modal-close-x" onclick="closeAddProductModal()">&times;</button>
            <h3 style="font-family:var(--font-serif); font-size:22px; margin-bottom:4px;">Add New Product</h3>
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">Fill in details to introduce a new bloom, cake, or gift to BlossomBay</p>

            <form method="POST" action="admin_panel.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_product">

                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Product Name</label>
                        <input type="text" name="name" class="bb-input" placeholder="e.g. Royal Scarlet Rose Bouquet" required>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Category</label>
                            <select name="category" class="bb-select" required>
                                <option value="Home">Home / Bestseller</option>
                                <option value="Birthday">Birthday</option>
                                <option value="Anniversary">Anniversary</option>
                                <option value="Valentine">Valentine</option>
                                <option value="Wedding">Wedding</option>
                                <option value="Festive">Festive</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="bb-input" placeholder="799.00" required>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Initial Stock Quantity</label>
                        <input type="number" name="stock" class="bb-input" value="15" required>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Short Description</label>
                        <textarea name="description" class="bb-textarea" rows="2" placeholder="Fragrant, vibrant red roses wrapped in signature pastel paper..."></textarea>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Handcrafting Details / Specifications</label>
                        <textarea name="details" class="bb-textarea" rows="2" placeholder="Contains 12 fresh stem roses with gypsy fillers and satin bow."></textarea>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Product Image</label>
                        <input type="file" name="image_file" class="bb-input" accept="image/*" style="margin-bottom:6px;">
                        <input type="text" name="image_url" class="bb-input" placeholder="Or enter existing path (e.g. images/Bbouquet.jpg)">
                    </div>

                    <div style="margin-top:15px; display:flex; gap:10px;">
                        <button type="submit" class="bb-btn bb-btn-primary" style="flex:1;">
                            <i class="fa-solid fa-plus"></i> Publish Product
                        </button>
                        <button type="button" class="bb-btn bb-btn-secondary" onclick="closeAddProductModal()">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================= -->
    <!-- MODAL: EDIT PRODUCT                                     -->
    <!-- ======================================================= -->
    <div class="admin-modal" id="editProductModal">
        <div class="admin-modal-card">
            <button class="modal-close-x" onclick="closeEditModal()">&times;</button>
            <h3 style="font-family:var(--font-serif); font-size:22px; margin-bottom:4px;">Edit Product #<span id="editPidDisplay"></span></h3>
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">Modify product pricing, stock count, and catalog attributes</p>

            <form method="POST" action="admin_panel.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit_product">
                <input type="hidden" name="product_id" id="edit_pid">

                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Product Name</label>
                        <input type="text" name="name" id="edit_name" class="bb-input" required>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Category</label>
                            <select name="category" id="edit_category" class="bb-select" required>
                                <option value="Home">Home</option>
                                <option value="Birthday">Birthday</option>
                                <option value="Anniversary">Anniversary</option>
                                <option value="Valentine">Valentine</option>
                                <option value="Wedding">Wedding</option>
                                <option value="Festive">Festive</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Price (₹)</label>
                            <input type="number" step="0.01" name="price" id="edit_price" class="bb-input" required>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Stock Count (Set 0 for Out of Stock)</label>
                        <input type="number" name="stock" id="edit_stock" class="bb-input" required>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Description</label>
                        <textarea name="description" id="edit_description" class="bb-textarea" rows="2"></textarea>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Details</label>
                        <textarea name="details" id="edit_details" class="bb-textarea" rows="2"></textarea>
                    </div>

                    <div>
                        <label style="display:block; font-size:12.5px; font-weight:700; color:#4a4a4a; margin-bottom:4px;">Change Image (Optional)</label>
                        <input type="file" name="image_file" class="bb-input" accept="image/*" style="margin-bottom:6px;">
                        <input type="text" name="image_url" id="edit_image_url" class="bb-input" placeholder="Or image path">
                    </div>

                    <div style="margin-top:15px; display:flex; gap:10px;">
                        <button type="submit" class="bb-btn bb-btn-primary" style="flex:1;">
                            <i class="fa-solid fa-check"></i> Save Changes
                        </button>
                        <button type="button" class="bb-btn bb-btn-secondary" onclick="closeEditModal()">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- CLIENT SCRIPT -->
    <script>
        // Tab Switching
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-content-block').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.admin-tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabId).style.display = 'block';
            btn.classList.add('active');
        }

        // Filter Orders Table
        function filterOrdersTable() {
            let q = document.getElementById('orderSearch').value.toLowerCase().trim();
            let status = document.getElementById('statusFilter').value;
            let pay = document.getElementById('payFilter').value;
            let rows = document.querySelectorAll('[data-order-row]');

            rows.forEach(r => {
                let matchQ = !q || r.dataset.num.includes(q) || r.dataset.name.includes(q);
                let matchStatus = !status || r.dataset.status === status;
                let matchPay = !pay || r.dataset.pay === pay;

                if (matchQ && matchStatus && matchPay) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        // Filter Products Table
        function filterProductsTable() {
            let q = document.getElementById('prodSearch').value.toLowerCase().trim();
            let cat = document.getElementById('catFilter').value.toLowerCase();
            let rows = document.querySelectorAll('[data-prod-row]');

            rows.forEach(r => {
                let matchQ = !q || r.dataset.name.includes(q);
                let matchCat = !cat || r.dataset.cat.includes(cat);

                if (matchQ && matchCat) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        // Modal Controls
        function openAddProductModal() {
            document.getElementById('addProductModal').style.display = 'flex';
        }
        function closeAddProductModal() {
            document.getElementById('addProductModal').style.display = 'none';
        }

        function openEditModal(prod) {
            document.getElementById('edit_pid').value = prod.id;
            document.getElementById('editPidDisplay').textContent = prod.id;
            document.getElementById('edit_name').value = prod.name || '';
            document.getElementById('edit_category').value = prod.category || 'Home';
            document.getElementById('edit_price').value = prod.price || '';
            document.getElementById('edit_stock').value = prod.stock || 0;
            document.getElementById('edit_description').value = prod.description || '';
            document.getElementById('edit_details').value = prod.details || '';
            document.getElementById('edit_image_url').value = prod.image || '';
            document.getElementById('editProductModal').style.display = 'flex';
        }
        function closeEditModal() {
            document.getElementById('editProductModal').style.display = 'none';
        }

        // Close on outside click
        window.onclick = function(e) {
            if (e.target.classList.contains('admin-modal')) {
                closeAddProductModal();
                closeEditModal();
            }
        };
    </script>
</body>
</html>
