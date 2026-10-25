<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

$user_id = $_SESSION['user_id'] ?? 0;
if($user_id == 0){
    header("Location: BBlogin.php");
    exit;
}

$stmt = $conn->prepare("SELECT full_name, email, mobile FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Gifting | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .globe-wrapper {
            max-width: 800px;
            width: 92%;
            margin: 40px auto 60px auto;
            flex: 1;
        }

        .globe-hero-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 40px 30px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            text-align: center;
        }

        .globe-icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(156, 74, 98, 0.15);
        }

        .globe-title {
            font-family: var(--font-serif);
            font-size: 30px;
            color: #3b232c;
            margin-bottom: 8px;
        }

        .sender-box {
            background: #faf7f8;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 20px;
            max-width: 520px;
            margin: 25px auto;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sender-box .row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4a4a4a;
        }

        .sender-box .row i {
            color: var(--primary);
            width: 18px;
        }

        .gifting-options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            max-width: 650px;
            margin: 30px auto 0 auto;
        }

        .gifting-option-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 24px 20px;
            text-align: center;
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .gifting-option-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            background: var(--primary-soft);
        }

        .option-icon {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .option-title {
            font-size: 16px;
            font-weight: 700;
            color: #2b2b2b;
            margin-bottom: 6px;
        }

        .option-desc {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        @media (max-width: 600px) {
            .gifting-options-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="globe-wrapper">
        <div class="globe-hero-card">
            <div class="globe-icon-circle">
                <i class="fa-solid fa-earth-americas"></i>
            </div>

            <h1 class="globe-title">Send Gifts Across the Globe</h1>
            <p style="color:var(--text-muted); font-size:15px; max-width:550px; margin:0 auto;">
                Whether delivering fresh floral bouquets to your doorstep or surprising loved ones in other cities and countries, BlossomBay handles dispatch with white-glove care.
            </p>

            <!-- Sender Info Card -->
            <div class="sender-box">
                <div style="font-size:11px; text-transform:uppercase; font-weight:700; color:#8c8588; margin-bottom:4px;">
                    Registered Sender Profile
                </div>
                <div class="row">
                    <i class="fa-regular fa-user"></i>
                    <span><b>Name:</b> <?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
                <div class="row">
                    <i class="fa-regular fa-envelope"></i>
                    <span><b>Email:</b> <?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="row">
                    <i class="fa-solid fa-phone"></i>
                    <span><b>Contact:</b> <?php echo htmlspecialchars($user['mobile']); ?></span>
                </div>
            </div>

            <!-- Gifting Choice Cards -->
            <div class="gifting-options-grid">
                <a href="add_location.php?type=self" class="gifting-option-card">
                    <div class="option-icon">
                        <i class="fa-solid fa-house-chimney-user"></i>
                    </div>
                    <div class="option-title">Deliver to Myself</div>
                    <div class="option-desc">Add or choose your personal address for doorstep arrival.</div>
                </a>

                <a href="add_location.php?type=other" class="gifting-option-card">
                    <div class="option-icon">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <div class="option-title">Surprise Someone Else</div>
                    <div class="option-desc">Send blossoms or treats directly to a recipient with a personalized card message.</div>
                </a>
            </div>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>