<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: BBlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT full_name, username, email, mobile, reward_points FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$initial = strtoupper(substr($user['full_name'] ?? $user['username'] ?? 'U', 0, 1));
$points = (int)($user['reward_points'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .profile-wrapper {
            max-width: 680px;
            width: 92%;
            margin: 40px auto 60px auto;
            flex: 1;
        }

        .profile-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 35px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
        }

        .avatar-banner {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 25px;
            border-bottom: 1px solid #f5ebeb;
            margin-bottom: 25px;
        }

        .avatar-circle {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d86c8b, #ff9bb3);
            color: white;
            font-size: 28px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(216, 108, 139, 0.3);
            flex-shrink: 0;
        }

        .user-titles h2 {
            font-family: var(--font-serif);
            font-size: 22px;
            color: #2b2b2b;
            margin-bottom: 4px;
        }

        .user-titles p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .reward-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff8eb;
            color: #b45309;
            border: 1px solid #fde68a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 6px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 25px;
            background: #faf7f8;
            padding: 18px;
            border-radius: var(--radius-md);
        }

        .info-field label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #8c8588;
            margin-bottom: 2px;
        }

        .info-field span {
            font-size: 14px;
            font-weight: 600;
            color: #2b2b2b;
        }

        .profile-nav-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 25px;
        }

        .profile-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: #2b2b2b;
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition);
        }

        .profile-nav-item:hover {
            border-color: var(--primary-light);
            background: var(--primary-soft);
            color: var(--primary);
            transform: translateX(4px);
        }

        .profile-nav-item .left-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-nav-item i.menu-icon {
            color: var(--primary);
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .profile-bottom-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f5ebeb;
        }

        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .profile-bottom-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="profile-wrapper">
        <div class="profile-card">
            <!-- Avatar Banner -->
            <div class="avatar-banner">
                <div class="avatar-circle">
                    <?php echo $initial; ?>
                </div>
                <div class="user-titles">
                    <h2><?php echo htmlspecialchars($user['full_name'] ?? 'BlossomBay Member'); ?></h2>
                    <p>@<?php echo htmlspecialchars($user['username'] ?? 'user'); ?></p>
                    <div class="reward-pill">
                        <i class="fa-solid fa-gift"></i> <?php echo $points; ?> Blossom Reward Points
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="info-grid">
                <div class="info-field">
                    <label>Full Name</label>
                    <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
                <div class="info-field">
                    <label>Email Address</label>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-field">
                    <label>Phone Number</label>
                    <span><?php echo htmlspecialchars($user['mobile']); ?></span>
                </div>
                <div class="info-field">
                    <label>Membership</label>
                    <span style="color:var(--primary); font-weight:700;">Blossom VIP</span>
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <a href="edit_profile.php" class="bb-btn bb-btn-primary" style="width:100%;">
                    <i class="fa-regular fa-pen-to-square"></i> Edit Profile Details
                </a>
            </div>

            <!-- Quick Navigation Menu -->
            <div class="profile-nav-menu">
                <a href="my_orders.php" class="profile-nav-item">
                    <div class="left-group">
                        <i class="fa-solid fa-box-open menu-icon"></i>
                        <span>My Orders & Live Dispatch Tracking</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size:12px; color:#aaa;"></i>
                </a>

                <a href="wishlist.php" class="profile-nav-item">
                    <div class="left-group">
                        <i class="fa-solid fa-heart menu-icon"></i>
                        <span>Saved Wishlist Items</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size:12px; color:#aaa;"></i>
                </a>

                <a href="rewards.php" class="profile-nav-item">
                    <div class="left-group">
                        <i class="fa-solid fa-award menu-icon"></i>
                        <span>Rewards Club (<?php echo $points; ?> Points)</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size:12px; color:#aaa;"></i>
                </a>

                <a href="BBglobe.php" class="profile-nav-item">
                    <div class="left-group">
                        <i class="fa-solid fa-earth-americas menu-icon"></i>
                        <span>Send Gifts Across the Globe</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size:12px; color:#aaa;"></i>
                </a>

                <a href="about.php" class="profile-nav-item">
                    <div class="left-group">
                        <i class="fa-solid fa-spa menu-icon"></i>
                        <span>About BlossomBay & Brand Story</span>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="font-size:12px; color:#aaa;"></i>
                </a>
            </div>

            <!-- Bottom Actions -->
            <div class="profile-bottom-actions">
                <a href="logout.php" class="bb-btn bb-btn-secondary" style="flex:1;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out
                </a>
                <a href="delete_account.php" class="bb-btn bb-btn-danger" style="flex:1;" onclick="return confirm('Permanently delete your BlossomBay account? All order history and reward points will be removed.');">
                    <i class="fa-regular fa-trash-can"></i> Delete Account
                </a>
            </div>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>
