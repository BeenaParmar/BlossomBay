<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: BBlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check and award points for new paid orders
$result = $conn->query("
    SELECT id FROM orders 
    WHERE user_id = '$user_id' 
    AND payment_status = 'Paid'
    AND reward_given = 0
");

$newOrders = $result ? $result->num_rows : 0;
if($newOrders > 0){
    $pointsToAdd = $newOrders * 5;
    $conn->query("
        UPDATE users 
        SET reward_points = reward_points + $pointsToAdd 
        WHERE id = '$user_id'
    ");
    $conn->query("
        UPDATE orders 
        SET reward_given = 1 
        WHERE user_id = '$user_id'
        AND reward_given = 0
        AND payment_status = 'Paid'
    ");
}

// Get user points
$stmt = $conn->prepare("SELECT reward_points FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$points = (int)($user['reward_points'] ?? 0);
$progress_percent = min(($points / 500) * 100, 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards Club | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .rewards-wrapper {
            max-width: 650px;
            width: 92%;
            margin: 40px auto 60px auto;
            flex: 1;
        }

        .reward-main-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 35px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .reward-badge-icon {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd700, #ffb703);
            color: white;
            font-size: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(255, 183, 3, 0.4);
            margin-bottom: 20px;
        }

        .rewards-title {
            font-family: var(--font-serif);
            font-size: 28px;
            color: #3b232c;
            margin-bottom: 6px;
        }

        .points-display {
            font-size: 42px;
            font-weight: 800;
            color: var(--primary);
            margin: 15px 0 5px 0;
        }

        .points-sub {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 25px;
        }

        .progress-box {
            width: 100%;
            height: 16px;
            background: #fde8ee;
            border-radius: var(--radius-full);
            overflow: hidden;
            margin: 15px 0;
            border: 1px solid var(--border-color);
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ff9bb3, #9c4a62);
            width: <?php echo $progress_percent; ?>%;
            transition: width 0.6s ease;
            border-radius: var(--radius-full);
        }

        .perks-checklist {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #f5ebeb;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .perk-row {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: #4a4a4a;
        }

        .perk-row i {
            color: #15803d;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="rewards-wrapper">
        <div class="reward-main-card">
            <div class="reward-badge-icon">
                <i class="fa-solid fa-crown"></i>
            </div>

            <h1 class="rewards-title">Blossom Loyalty Club</h1>
            <p style="color:var(--text-muted); font-size:14px;">Earn 5 points for every paid order to unlock luxury surprises</p>

            <div class="points-display">
                <?php echo $points; ?> <span style="font-size:20px; font-weight:600; color:#555;">PTS</span>
            </div>
            <div class="points-sub">Current Member Balance</div>

            <!-- PROGRESS BAR -->
            <div class="progress-box">
                <div class="progress-fill"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:12px; font-weight:700; color:#64748b;">
                <span>0 PTS</span>
                <span><?php echo round($progress_percent); ?>% towards 500 PTS</span>
                <span>500 PTS (Free Gift)</span>
            </div>

            <?php if(isset($_GET['success'])): ?>
            <div style="background:#dcfce7; color:#15803d; padding:12px; border-radius:12px; font-weight:700; margin-top:20px;">
                🎉 Reward redeemed successfully! Your complimentary gift has been scheduled.
            </div>
            <?php endif; ?>

            <div style="margin-top:25px;">
                <?php if($points >= 500): ?>
                    <div style="background:#fff8eb; border:1px solid #fde68a; padding:15px; border-radius:14px; margin-bottom:15px;">
                        <h4 style="color:#b45309; margin-bottom:4px;">🎉 Congratulations! Milestone Reached!</h4>
                        <p style="font-size:13px; color:#92400e;">You have earned enough points to unlock a complimentary celebration bouquet.</p>
                    </div>

                    <form action="redeem_rewards.php" method="POST">
                        <button type="submit" class="bb-btn bb-btn-primary" style="padding:14px 30px; font-size:15px;">
                            <i class="fa-solid fa-gift"></i> Redeem 500 Points for Free Gift
                        </button>
                    </form>
                <?php else: ?>
                    <p style="font-size:14px; color:var(--primary-dark); font-weight:600; margin-bottom:15px;">
                        <i class="fa-solid fa-sparkles"></i> Earn <b><?php echo 500 - $points; ?></b> more points on your next purchases to claim a free gift!
                    </p>
                    <a href="BBhome.php" class="bb-btn bb-btn-primary">
                        <i class="fa-solid fa-bag-shopping"></i> Shop & Earn Points
                    </a>
                <?php endif; ?>
            </div>

            <div class="perks-checklist">
                <div class="perk-row">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Automatic 5 points on every successfully paid order</span>
                </div>
                <div class="perk-row">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Exclusive early access to seasonal Valentine & Festive drops</span>
                </div>
                <div class="perk-row">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Points never expire as long as your account remains active</span>
                </div>
            </div>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>