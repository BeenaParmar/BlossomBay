<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: BBlogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info for header personalization
$user_name = "Valued Customer";
$u_stmt = $conn->prepare("SELECT full_name, username FROM users WHERE id = ?");
if ($u_stmt) {
    $u_stmt->bind_param("i", $user_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    if ($u_row = $u_res->fetch_assoc()) {
        $user_name = !empty($u_row['full_name']) ? $u_row['full_name'] : $u_row['username'];
    }
}

// Fetch orders
$stmt = $conn->prepare("
    SELECT * FROM orders 
    WHERE user_id = ? 
    ORDER BY id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$total_orders = $orders_result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | BlossomBay</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #9c4a62;
            --primary-light: #f7c6d8;
            --primary-soft: #fdf2f4;
            --primary-dark: #7a3247;
            --accent: #e07a5f;
            --bg-page: #FFF8F2;
            --card-bg: #FFFFFF;
            --text-main: #2b2b2b;
            --text-muted: #6b7280;
            --border-color: #f0e6e8;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-subtle: 0 4px 20px rgba(156, 74, 98, 0.06);
            --shadow-hover: 0 12px 30px rgba(156, 74, 98, 0.12);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /* Top Navigation Header */
        .header-bar {
            background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
            padding: 14px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 15px rgba(156, 74, 98, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(8px);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-logo img {
            height: 48px;
            width: auto;
            display: block;
            transition: var(--transition);
        }

        .header-logo img:hover {
            transform: scale(1.04);
        }

        .back-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.85);
            color: var(--primary);
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .back-nav-btn:hover {
            background: white;
            color: var(--primary-dark);
            transform: translateX(-2px);
            box-shadow: 0 4px 10px rgba(156, 74, 98, 0.15);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-greeting {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-dark);
            background: rgba(255, 255, 255, 0.6);
            padding: 6px 14px;
            border-radius: 20px;
        }

        /* Main Container */
        .main-wrapper {
            max-width: 1080px;
            width: 92%;
            margin: 35px auto 60px auto;
            flex: 1;
        }

        /* Page Hero Banner */
        .page-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title-group h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #3b232c;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title-group p {
            color: var(--text-muted);
            font-size: 15px;
            margin-top: 4px;
        }

        .order-stats-badge {
            background: white;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Filter & Search Bar */
        .toolbar {
            background: white;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 260px;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 14px;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1px solid #e2e8f0;
            border-radius: 25px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
            background: #fafafa;
        }

        .search-input:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(156, 74, 98, 0.12);
        }

        .filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            color: var(--text-muted);
            padding: 7px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Order Card Component */
        .order-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            margin-bottom: 26px;
            overflow: hidden;
            transition: var(--transition);
        }

        .order-card:hover {
            box-shadow: var(--shadow-hover);
            border-color: #ebd3dc;
        }

        /* Order Card Header */
        .order-header {
            background: linear-gradient(to right, #faf5f7, #ffffff);
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .order-meta-grid {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 22px;
        }

        .meta-group {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
            color: #8c8588;
            margin-bottom: 2px;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 700;
            color: #2b2b2b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .order-id-badge {
            font-family: monospace;
            background: var(--primary-soft);
            color: var(--primary-dark);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 13px;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .tracking-chip {
            background: #edf2f7;
            color: #4a5568;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 12px;
            font-family: monospace;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: var(--transition);
        }

        .tracking-chip:hover {
            background: #e2e8f0;
            color: #2d3748;
        }

        /* Badges */
        .status-badge-group {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: capitalize;
            letter-spacing: 0.2px;
        }

        /* Delivery Status Badges */
        .badge-placed {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .badge-confirmed {
            background: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }
        .badge-processing, .badge-shipped {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .badge-delivered {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-cancelled {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Payment Status Badges */
        .badge-paid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-cod {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fef3c7;
        }
        .badge-pending {
            background: #ffedd5;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        /* Tracking Step Timeline */
        .tracking-timeline-bar {
            padding: 16px 24px;
            background: #fffcfd;
            border-bottom: 1px solid var(--border-color);
        }

        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            max-width: 650px;
            margin: 0 auto;
        }

        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 13px;
            left: 20px;
            right: 20px;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .timeline-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
        }

        .step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: white;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 6px;
            transition: var(--transition);
        }

        .step-name {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        .timeline-step.completed .step-dot {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .timeline-step.completed .step-name {
            color: var(--primary-dark);
            font-weight: 700;
        }

        .timeline-step.active .step-dot {
            background: #fff;
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 3px rgba(156, 74, 98, 0.2);
            font-weight: bold;
        }

        /* Order Items Body */
        .order-items-wrapper {
            padding: 10px 24px;
        }

        .product-item-card {
            display: flex;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid #f5ebeb;
            gap: 20px;
            position: relative;
            transition: var(--transition);
        }

        .product-item-card:last-child {
            border-bottom: none;
        }

        .product-image-container {
            width: 86px;
            height: 86px;
            min-width: 86px;
            border-radius: var(--radius-md);
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #eee;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .product-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-item-card:hover .product-image-container img {
            transform: scale(1.06);
        }

        .product-info {
            flex: 1;
            min-width: 0;
        }

        .product-top-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 4px;
            flex-wrap: wrap;
        }

        .product-title {
            font-size: 16px;
            font-weight: 700;
            color: #2b2b2b;
            text-decoration: none;
        }

        .product-pid {
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: monospace;
            letter-spacing: 0.3px;
        }

        .product-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-pricing {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .unit-price {
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 15px;
        }

        .qty-pill {
            background: #f7eef1;
            color: var(--primary);
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .subtotal-text {
            color: #64748b;
            font-size: 13px;
        }

        /* Order Card Footer */
        .order-footer {
            background: #faf7f8;
            padding: 16px 24px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .payment-mode-indicator {
            font-size: 13px;
            color: #555;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .payment-mode-indicator i {
            color: var(--primary);
        }

        .grand-total-row {
            font-size: 18px;
            font-weight: 800;
            color: #2b2b2b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .grand-total-row span.amount {
            color: var(--primary);
            font-size: 20px;
        }

        /* Action Buttons */
        .order-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-track {
            background: white;
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-track:hover {
            background: var(--primary-soft);
            color: var(--primary-dark);
        }

        .btn-invoice {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-invoice:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .btn-cancel {
            background: #fff;
            color: #dc2626;
            border-color: #fca5a5;
        }

        .btn-cancel:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-shop-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: white;
            padding: 12px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            margin: 20px auto 0 auto;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(156, 74, 98, 0.25);
        }

        .btn-shop-more:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(156, 74, 98, 0.35);
        }

        /* Empty State */
        .empty-orders-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 60px 30px;
            text-align: center;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            margin: 40px auto;
            max-width: 600px;
        }

        .empty-icon {
            width: 90px;
            height: 90px;
            background: var(--primary-soft);
            color: var(--primary);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 20px;
        }

        .empty-orders-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            color: #333;
            margin-bottom: 10px;
        }

        .empty-orders-card p {
            color: var(--text-muted);
            font-size: 15px;
            margin-bottom: 25px;
        }

        /* Tracking Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.2s ease;
        }

        .modal-box {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 520px;
            padding: 28px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: slideUp 0.25s ease;
        }

        .modal-close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 16px;
            transition: var(--transition);
        }

        .modal-close-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .modal-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .modal-header p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .tracking-status-list {
            margin-top: 15px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .track-item {
            display: flex;
            gap: 14px;
        }

        .track-item .icon-col {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .track-item .dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .track-item .line {
            width: 2px;
            flex: 1;
            background: #e2e8f0;
            margin: 4px 0;
            min-height: 25px;
        }

        .track-item.done .dot {
            background: #10b981;
        }

        .track-item.current .dot {
            background: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-soft);
        }

        .track-item.future .dot {
            background: #cbd5e1;
        }

        .track-content h4 {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        .track-content p {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #1e293b;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 2000;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-bar {
                padding: 12px 20px;
            }
            .page-title-group h1 {
                font-size: 26px;
            }
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .product-item-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .product-image-container {
                width: 100%;
                height: 180px;
            }
            .order-footer {
                flex-direction: column;
                align-items: flex-start;
            }
            .order-actions {
                width: 100%;
                justify-content: flex-start;
            }
            .timeline-steps {
                display: none; /* Hide stepper on very small screens for clean readability */
            }
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION -->
    <?php include "navbar.php"; ?>

    <!-- MAIN ORDERS CONTAINER -->
    <main class="main-wrapper">

        <!-- HERO SECTION -->
        <div class="page-hero">
            <div class="page-title-group">
                <h1><i class="fa-solid fa-box-open" style="color: var(--primary);"></i> My Orders</h1>
                <p>Track delivery status, view invoices, and manage past purchases</p>
            </div>
            <div class="order-stats-badge">
                <i class="fa-solid fa-receipt"></i>
                <span id="order-count-text"><?php echo $total_orders; ?> Total <?php echo ($total_orders == 1) ? 'Order' : 'Orders'; ?></span>
            </div>
        </div>

        <?php if ($total_orders > 0): ?>

        <!-- SEARCH AND FILTER TOOLBAR -->
        <div class="toolbar">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="orderSearchInput" class="search-input" placeholder="Search by Order #, Tracking ID, or Product name..." onkeyup="filterOrders()">
            </div>
            <div class="filter-pills">
                <button class="filter-btn active" onclick="setFilter('all', this)">All</button>
                <button class="filter-btn" onclick="setFilter('Placed', this)">Placed</button>
                <button class="filter-btn" onclick="setFilter('Paid', this)">Paid</button>
                <button class="filter-btn" onclick="setFilter('COD', this)">COD</button>
                <button class="filter-btn" onclick="setFilter('Cancelled', this)">Cancelled</button>
            </div>
        </div>

        <!-- ORDERS LIST -->
        <div id="ordersContainer">

            <?php 
            while($order = $orders_result->fetch_assoc()): 
                $order_id = $order['id'];
                $order_num = htmlspecialchars($order['order_number']);
                $grand_total = number_format((float)$order['grand_total'], 2);
                $order_status = !empty($order['order_status']) ? trim($order['order_status']) : 'Placed';
                $payment_status = !empty($order['payment_status']) ? trim($order['payment_status']) : 'Pending';
                
                // Formatted Date
                $date_raw = $order['order_date'] ?? date('Y-m-d H:i:s');
                $formatted_date = date("M d, Y • h:i A", strtotime($date_raw));
                
                // Tracking ID
                $tracking_id = !empty($order['tracking_id']) ? $order['tracking_id'] : ("TRK-BB-" . preg_replace('/[^\d]/', '', $order_num));
                
                // Payment Method
                $payment_method = !empty($order['payment_method']) ? $order['payment_method'] : (($payment_status === 'Paid') ? 'Online Payment (QR)' : 'Cash on Delivery (COD)');
                $is_cod = (stripos($payment_method, 'COD') !== false || stripos($payment_method, 'Cash') !== false || ($payment_status !== 'Paid'));
                $payment_tag = $is_cod ? 'COD' : 'Paid';

                // Query items for this order with rich product details fallback
                $items_stmt = $conn->prepare("
                    SELECT oi.*, 
                           COALESCE(oi.product_id, p.id, 1) AS final_pid,
                           COALESCE(NULLIF(oi.image, ''), p.image, 'images/Bbouquet.jpg') AS final_img,
                           COALESCE(NULLIF(oi.description, ''), p.description, 'Delightful BlossomBay specialty choice') AS final_desc
                    FROM order_items oi
                    LEFT JOIN products p ON (oi.product_id = p.id OR oi.product_name = p.name)
                    WHERE oi.order_id = ?
                ");
                $items_stmt->bind_param("i", $order_id);
                $items_stmt->execute();
                $items = $items_stmt->get_result();
                $items_count = $items->num_rows;
            ?>

            <div class="order-card" 
                 data-order-num="<?php echo strtolower($order_num); ?>"
                 data-tracking="<?php echo strtolower($tracking_id); ?>"
                 data-status="<?php echo htmlspecialchars($order_status); ?>"
                 data-payment="<?php echo $payment_tag; ?>">

                <!-- ORDER HEADER -->
                <div class="order-header">
                    <div class="order-meta-grid">
                        <div class="meta-group">
                            <span class="meta-label">Order Number</span>
                            <span class="meta-value">
                                <span class="order-id-badge">#<?php echo $order_num; ?></span>
                                <button type="button" style="background:none; border:none; cursor:pointer; color:#9c4a62; font-size:13px;" onclick="copyToClipboard('<?php echo $order_num; ?>', 'Order number copied!')" title="Copy Order Number">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </span>
                        </div>

                        <div class="meta-group">
                            <span class="meta-label">Order Placed</span>
                            <span class="meta-value" style="font-weight:600; color:#475569;">
                                <i class="fa-regular fa-calendar-days" style="color:#94a3b8;"></i> <?php echo $formatted_date; ?>
                            </span>
                        </div>

                        <div class="meta-group">
                            <span class="meta-label">Tracking ID</span>
                            <span class="meta-value">
                                <span class="tracking-chip" onclick="openTrackingModal('<?php echo $order_num; ?>', '<?php echo $tracking_id; ?>', '<?php echo $order_status; ?>')" title="Click to view live tracking details">
                                    <i class="fa-solid fa-truck-fast"></i> <?php echo htmlspecialchars($tracking_id); ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- STATUS BADGES -->
                    <div class="status-badge-group">
                        <!-- Delivery Status Badge -->
                        <?php 
                        $status_class = 'badge-placed';
                        $status_icon = 'fa-clock';
                        if ($order_status === 'Delivered') {
                            $status_class = 'badge-delivered';
                            $status_icon = 'fa-circle-check';
                        } elseif ($order_status === 'Cancelled') {
                            $status_class = 'badge-cancelled';
                            $status_icon = 'fa-circle-xmark';
                        } elseif ($order_status === 'Confirmed') {
                            $status_class = 'badge-confirmed';
                            $status_icon = 'fa-thumbs-up';
                        } elseif ($order_status === 'Shipped') {
                            $status_class = 'badge-shipped';
                            $status_icon = 'fa-truck-arrow-right';
                        }
                        ?>
                        <span class="badge <?php echo $status_class; ?>" id="status-badge-<?php echo $order_num; ?>">
                            <i class="fa-solid <?php echo $status_icon; ?>"></i> <?php echo htmlspecialchars($order_status); ?>
                        </span>

                        <!-- Payment Status Badge -->
                        <?php if ($payment_status === 'Paid'): ?>
                            <span class="badge badge-paid" title="Payment successfully verified">
                                <i class="fa-solid fa-check-double"></i> Paid Online
                            </span>
                        <?php else: ?>
                            <span class="badge badge-cod" title="Payment pending upon doorstep delivery">
                                <i class="fa-solid fa-hand-holding-dollar"></i> Cash on Delivery (COD)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PROGRESS TIMELINE (If not cancelled) -->
                <?php if ($order_status !== 'Cancelled'): ?>
                <div class="tracking-timeline-bar">
                    <div class="timeline-steps">
                        <div class="timeline-step <?php echo in_array($order_status, ['Placed', 'Confirmed', 'Processing', 'Shipped', 'Delivered']) ? 'completed' : ''; ?>">
                            <div class="step-dot"><i class="fa-solid fa-check"></i></div>
                            <span class="step-name">Order Placed</span>
                        </div>
                        <div class="timeline-step <?php echo in_array($order_status, ['Confirmed', 'Processing', 'Shipped', 'Delivered']) ? 'completed' : ''; ?>">
                            <div class="step-dot"><i class="fa-solid fa-receipt"></i></div>
                            <span class="step-name">Confirmed</span>
                        </div>
                        <div class="timeline-step <?php echo in_array($order_status, ['Processing', 'Shipped', 'Delivered']) ? 'completed' : ''; ?>">
                            <div class="step-dot"><i class="fa-solid fa-box"></i></div>
                            <span class="step-name">Packed</span>
                        </div>
                        <div class="timeline-step <?php echo in_array($order_status, ['Shipped', 'Delivered']) ? 'completed' : ''; ?>">
                            <div class="step-dot"><i class="fa-solid fa-truck"></i></div>
                            <span class="step-name">Shipped</span>
                        </div>
                        <div class="timeline-step <?php echo ($order_status === 'Delivered') ? 'completed' : ''; ?>">
                            <div class="step-dot"><i class="fa-solid fa-house"></i></div>
                            <span class="step-name">Delivered</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ORDER ITEMS LIST -->
                <div class="order-items-wrapper">
                    <?php 
                    if ($items_count > 0):
                        while($item = $items->fetch_assoc()): 
                            $item_name = htmlspecialchars($item['product_name']);
                            $item_pid = htmlspecialchars($item['final_pid']);
                            $item_price = number_format((float)$item['price'], 2);
                            $item_qty = (int)$item['quantity'];
                            $item_subtotal = number_format((float)($item['price'] * $item_qty), 2);
                            $item_img = !empty($item['final_img']) ? $item['final_img'] : 'images/Bbouquet.jpg';
                            $item_desc = htmlspecialchars($item['final_desc']);
                    ?>
                    <div class="product-item-card" data-product-name="<?php echo strtolower($item_name); ?>">
                        <div class="product-image-container">
                            <img src="<?php echo htmlspecialchars($item_img); ?>" 
                                 alt="<?php echo $item_name; ?>"
                                 onerror="this.onerror=null; this.src='images/Bbouquet.jpg';">
                        </div>

                        <div class="product-info">
                            <div class="product-top-row">
                                <h3 class="product-title"><?php echo $item_name; ?></h3>
                                <span class="product-pid"><i class="fa-solid fa-tag"></i> PID: #PRD-<?php echo $item_pid; ?></span>
                            </div>

                            <p class="product-desc"><?php echo $item_desc; ?></p>

                            <div class="product-pricing">
                                <span class="unit-price">₹<?php echo $item_price; ?></span>
                                <span class="qty-pill">Qty: <?php echo $item_qty; ?></span>
                                <span class="subtotal-text">• Item Total: <b>₹<?php echo $item_subtotal; ?></b></span>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <div style="padding: 20px; text-align:center; color:#888;">
                        <i class="fa-solid fa-box-open" style="font-size:24px; color:#ccc; margin-bottom:6px;"></i>
                        <p>Order details recorded. Quantity items: 1</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ORDER CARD FOOTER -->
                <div class="order-footer">
                    <div class="footer-left">
                        <div class="payment-mode-indicator">
                            <?php if ($is_cod): ?>
                                <i class="fa-solid fa-money-bill-wave"></i> Payment Method: <b>Cash on Delivery (COD)</b>
                            <?php else: ?>
                                <i class="fa-solid fa-qrcode"></i> Payment Method: <b>Online / QR (Prepaid)</b>
                            <?php endif; ?>
                        </div>
                        <div class="grand-total-row">
                            Grand Total: <span class="amount">₹<?php echo $grand_total; ?></span>
                        </div>
                    </div>

                    <div class="order-actions">
                        <button type="button" class="action-btn btn-track" onclick="openTrackingModal('<?php echo $order_num; ?>', '<?php echo $tracking_id; ?>', '<?php echo $order_status; ?>')">
                            <i class="fa-solid fa-route"></i> Track Order
                        </button>

                        <button type="button" class="action-btn btn-invoice" onclick="printOrderInvoice('<?php echo $order_num; ?>')">
                            <i class="fa-solid fa-file-invoice"></i> Invoice
                        </button>

                        <?php if (in_array($order_status, ['Placed', 'Confirmed'])): ?>
                        <button type="button" class="action-btn btn-cancel" id="cancel-btn-<?php echo $order_num; ?>" onclick="cancelOrder('<?php echo $order_num; ?>')">
                            <i class="fa-solid fa-ban"></i> Cancel Order
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <?php endwhile; ?>

        </div>

        <?php else: ?>

        <!-- EMPTY ORDERS STATE -->
        <div class="empty-orders-card">
            <div class="empty-icon">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h2>No Orders Placed Yet</h2>
            <p>You haven't placed any orders yet. Discover our fresh flowers, decadent cakes, and luxury gift hampers today!</p>
            <a href="BBhome.php" class="btn-shop-more">
                <i class="fa-solid fa-gift"></i> Start Shopping Now
            </a>
        </div>

        <?php endif; ?>

        <!-- BOTTOM EXPLORE LINK -->
        <?php if ($total_orders > 0): ?>
        <div style="text-align: center; margin-top: 30px;">
            <a href="BBhome.php" class="btn-shop-more">
                <i class="fa-solid fa-cart-plus"></i> Continue Shopping
            </a>
        </div>
        <?php endif; ?>

    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

    <!-- LIVE TRACKING POPUP MODAL -->
    <div class="modal-overlay" id="trackingModal">
        <div class="modal-box">
            <button class="modal-close-btn" onclick="closeTrackingModal()">&times;</button>
            <div class="modal-header">
                <h3>Live Delivery Tracking</h3>
                <p id="modalOrderInfo">Tracking Details for Order</p>
            </div>

            <div style="background:#f8fafc; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700;">Tracking Number</span>
                    <div style="font-weight:700; color:#1e293b; font-family:monospace; font-size:14px;" id="modalTrackingId">TRK-BB-000000</div>
                </div>
                <div>
                    <span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700;">Estimated Delivery</span>
                    <div style="font-weight:700; color:#9c4a62; font-size:13px;" id="modalEstDelivery">2-3 Business Days</div>
                </div>
            </div>

            <div class="tracking-status-list">
                <div class="track-item done">
                    <div class="icon-col">
                        <div class="dot"><i class="fa-solid fa-check"></i></div>
                        <div class="line"></div>
                    </div>
                    <div class="track-content">
                        <h4>Order Placed & Verified</h4>
                        <p>We received your order and payment verification.</p>
                    </div>
                </div>

                <div class="track-item" id="modalStepConfirmed">
                    <div class="icon-col">
                        <div class="dot"><i class="fa-solid fa-check"></i></div>
                        <div class="line"></div>
                    </div>
                    <div class="track-content">
                        <h4>Order Confirmed & Prepared</h4>
                        <p>Floral stylists and bakers are preparing your items fresh.</p>
                    </div>
                </div>

                <div class="track-item" id="modalStepShipped">
                    <div class="icon-col">
                        <div class="dot"><i class="fa-solid fa-check"></i></div>
                        <div class="line"></div>
                    </div>
                    <div class="track-content">
                        <h4>Handed Over to Courier Partner</h4>
                        <p>Package is transit with BlossomBay Express Delivery.</p>
                    </div>
                </div>

                <div class="track-item" id="modalStepDelivered">
                    <div class="icon-col">
                        <div class="dot"><i class="fa-solid fa-check"></i></div>
                    </div>
                    <div class="track-content">
                        <h4>Delivered to Recipient</h4>
                        <p>Package safely handed over at doorstep.</p>
                    </div>
                </div>
            </div>

            <div style="margin-top: 25px; text-align: center;">
                <button type="button" class="btn-shop-more" style="margin:0; width:100%; justify-content:center;" onclick="closeTrackingModal()">
                    Got It
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div class="toast" id="toastMessage">
        <i class="fa-solid fa-circle-check" style="color:#4ade80;"></i>
        <span id="toastText">Action successful</span>
    </div>

    <script>
        // Copy to clipboard helper
        function copyToClipboard(text, msg) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(msg);
            }).catch(() => {
                showToast("Copied: " + text);
            });
        }

        // Show Toast Notification
        function showToast(message) {
            const toast = document.getElementById('toastMessage');
            const toastText = document.getElementById('toastText');
            toastText.textContent = message;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }

        // Tracking Modal Functions
        function openTrackingModal(orderNum, trackingId, status) {
            document.getElementById('modalOrderInfo').textContent = 'Order #' + orderNum + ' • Live Dispatch Status';
            document.getElementById('modalTrackingId').textContent = trackingId;
            
            const stepConf = document.getElementById('modalStepConfirmed');
            const stepShip = document.getElementById('modalStepShipped');
            const stepDel = document.getElementById('modalStepDelivered');

            // Reset classes
            [stepConf, stepShip, stepDel].forEach(el => {
                el.className = 'track-item future';
            });

            if (status === 'Confirmed' || status === 'Processing' || status === 'Shipped' || status === 'Delivered') {
                stepConf.className = 'track-item done';
            }
            if (status === 'Shipped' || status === 'Delivered') {
                stepShip.className = 'track-item done';
            }
            if (status === 'Delivered') {
                stepDel.className = 'track-item done';
            }

            document.getElementById('trackingModal').style.display = 'flex';
        }

        function closeTrackingModal() {
            document.getElementById('trackingModal').style.display = 'none';
        }

        // Close on clicking outside modal
        window.onclick = function(e) {
            const modal = document.getElementById('trackingModal');
            if (e.target === modal) {
                closeTrackingModal();
            }
        };

        // Filter by Tab
        let currentFilter = 'all';
        function setFilter(filter, btn) {
            currentFilter = filter;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterOrders();
        }

        // Filter & Search Orders Logic
        function filterOrders() {
            const query = document.getElementById('orderSearchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.order-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const orderNum = card.dataset.orderNum || '';
                const tracking = card.dataset.tracking || '';
                const status = card.dataset.status || '';
                const payment = card.dataset.payment || '';
                
                // Get all product names inside this card
                const productElements = card.querySelectorAll('[data-product-name]');
                let productMatch = false;
                productElements.forEach(pe => {
                    if (pe.dataset.productName && pe.dataset.productName.includes(query)) {
                        productMatch = true;
                    }
                });

                const matchesQuery = !query || 
                    orderNum.includes(query) || 
                    tracking.includes(query) || 
                    productMatch;

                let matchesFilter = true;
                if (currentFilter === 'Placed') {
                    matchesFilter = (status === 'Placed');
                } else if (currentFilter === 'Paid') {
                    matchesFilter = (payment === 'Paid');
                } else if (currentFilter === 'COD') {
                    matchesFilter = (payment === 'COD');
                } else if (currentFilter === 'Cancelled') {
                    matchesFilter = (status === 'Cancelled');
                }

                if (matchesQuery && matchesFilter) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const countText = document.getElementById('order-count-text');
            if (countText) {
                countText.textContent = visibleCount + (visibleCount === 1 ? ' Order' : ' Orders');
            }
        }

        // Cancel Order with API integration
        function cancelOrder(orderNum) {
            if (!confirm(`Are you sure you want to cancel order #${orderNum}?`)) {
                return;
            }

            fetch('cancel_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_number: orderNum })
            })
            .then(res => res.text())
            .then(res => {
                if (res.trim() === 'success') {
                    showToast(`Order #${orderNum} has been cancelled.`);
                    const badge = document.getElementById(`status-badge-${orderNum}`);
                    if (badge) {
                        badge.className = 'badge badge-cancelled';
                        badge.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Cancelled';
                    }
                    const cancelBtn = document.getElementById(`cancel-btn-${orderNum}`);
                    if (cancelBtn) {
                        cancelBtn.remove();
                    }
                } else {
                    alert('Unable to cancel this order: ' + res);
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while cancelling the order.');
            });
        }

        // Print Order Invoice
        function printOrderInvoice(orderNum) {
            window.print();
        }
    </script>
</body>
</html>