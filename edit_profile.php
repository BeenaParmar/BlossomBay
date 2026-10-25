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

$stmt = $conn->prepare("SELECT full_name, email, mobile FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .edit-wrapper {
            max-width: 500px;
            width: 92%;
            margin: 45px auto 60px auto;
            flex: 1;
        }

        .edit-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 35px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
        }

        .edit-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .edit-header h2 {
            font-family: var(--font-serif);
            font-size: 26px;
            color: #3b232c;
            margin-bottom: 4px;
        }

        .edit-header p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #4a4a4a;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="edit-wrapper">
        <div class="edit-card">
            <div class="edit-header">
                <h2><i class="fa-regular fa-id-badge" style="color:var(--primary);"></i> Edit Profile</h2>
                <p>Update your personal information for shipping and communication</p>
            </div>

            <form action="update_profile.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="bb-input" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="bb-input" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" class="bb-input" value="<?= htmlspecialchars($user['mobile']) ?>" required>
                </div>

                <div style="margin-top:25px; display:flex; flex-direction:column; gap:10px;">
                    <button type="submit" class="bb-btn bb-btn-primary" style="padding:14px;">
                        <i class="fa-solid fa-check"></i> Save Changes
                    </button>
                    <a href="profile.php" class="bb-btn bb-btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>
