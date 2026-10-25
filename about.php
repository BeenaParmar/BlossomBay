<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About BlossomBay — Brand Story & Vision</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .about-hero {
            background: linear-gradient(135deg, #fce4ec 0%, #fff0f5 100%);
            padding: 50px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
        }

        .about-hero h1 {
            font-family: var(--font-serif);
            font-size: 38px;
            color: #3b232c;
            margin-bottom: 8px;
        }

        .about-hero p {
            color: var(--text-muted);
            font-size: 16px;
            max-width: 620px;
            margin: 0 auto;
        }

        .about-wrapper {
            max-width: 860px;
            width: 92%;
            margin: 40px auto 60px auto;
            flex: 1;
        }

        .story-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 40px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            margin-bottom: 45px;
            text-align: center;
        }

        .story-card h2 {
            font-family: var(--font-serif);
            font-size: 28px;
            color: #3b232c;
            margin-bottom: 18px;
        }

        .story-card p {
            color: #4a4a4a;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 16px;
        }

        .founder-pill-box {
            display: inline-flex;
            gap: 12px;
            margin-top: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .founder-pill {
            background: var(--primary-soft);
            color: var(--primary);
            padding: 8px 18px;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 14px;
            border: 1px solid var(--border-color);
        }

        /* Timeline Styling */
        .timeline-section-title {
            text-align: center;
            font-family: var(--font-serif);
            font-size: 28px;
            color: #3b232c;
            margin-bottom: 35px;
        }

        .timeline {
            position: relative;
            max-width: 780px;
            margin: 0 auto;
        }

        .timeline::after {
            content: '';
            position: absolute;
            width: 3px;
            background: linear-gradient(to bottom, #ff9bb3, #d86c8b);
            top: 15px;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
        }

        .timeline-item {
            padding: 15px 30px;
            position: relative;
            width: 50%;
            box-sizing: border-box;
        }

        .timeline-item.left {
            left: 0;
            text-align: right;
        }

        .timeline-item.right {
            left: 50%;
            text-align: left;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            top: 26px;
            width: 16px;
            height: 16px;
            background: white;
            border: 3.5px solid var(--primary);
            border-radius: 50%;
            z-index: 2;
            box-shadow: 0 0 0 4px var(--primary-soft);
        }

        .timeline-item.left::before {
            right: -8px;
        }

        .timeline-item.right::before {
            left: -8px;
        }

        .timeline-content {
            background: white;
            padding: 22px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        .timeline-content:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
        }

        .timeline-content h3 {
            font-size: 17px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timeline-item.left .timeline-content h3 {
            justify-content: flex-end;
        }

        .timeline-content p {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .timeline::after {
                left: 20px;
            }
            .timeline-item {
                width: 100%;
                padding-left: 50px;
                padding-right: 15px;
                text-align: left !important;
            }
            .timeline-item.right {
                left: 0;
            }
            .timeline-item.left::before, .timeline-item.right::before {
                left: 12px !important;
            }
            .timeline-item.left .timeline-content h3 {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <!-- HERO -->
    <div class="about-hero">
        <h1>🌸 The BlossomBay Story</h1>
        <p>Curating warmth, floral artistry, and heartfelt gifting moments for memories that last forever</p>
    </div>

    <main class="about-wrapper">
        <div class="story-card">
            <h2>Handcrafted With Love</h2>
            <p>
                BlossomBay was founded in 2025 by <strong>Beena Parmar</strong> and <strong>Archita Mhatre</strong> with a passionate vision: to weave raw emotion, timeless floral elegance, and effortless simplicity into the world of online gifting.
            </p>
            <p>
                The platform was designed from the ground up to offer a soothing, serene shopping experience where choosing a birthday cake, sending an anniversary rose bouquet, or customizing personalized keepsakes feels like a joyous personal celebration.
            </p>
            <p>
                At BlossomBay, every petal is selected fresh on the morning of dispatch, and every ribbon is hand-tied. We believe a gift is never just an item — it is a tangible expression of love, gratitude, and belonging.
            </p>

            <div class="founder-pill-box">
                <span class="founder-pill"><i class="fa-solid fa-feather"></i> Co-Founder: Beena Parmar</span>
                <span class="founder-pill"><i class="fa-solid fa-feather"></i> Co-Founder: Archita Mhatre</span>
            </div>
        </div>

        <h2 class="timeline-section-title">🌷 The Journey Behind BlossomBay</h2>

        <div class="timeline">
            <div class="timeline-item left">
                <div class="timeline-content">
                    <h3>💡 The Spark & Vision</h3>
                    <p>Born from the desire to make meaningful, premium floral gifting seamless and heartfelt for every memorable milestone.</p>
                </div>
            </div>

            <div class="timeline-item right">
                <div class="timeline-content">
                    <h3>🎨 Soft Pastel Aesthetic</h3>
                    <p>A serene pastel design philosophy was crafted to bring calmness, luxury, and emotional warmth to every customer touchpoint.</p>
                </div>
            </div>

            <div class="timeline-item left">
                <div class="timeline-content">
                    <h3>🛠 Seamless Platform Features</h3>
                    <p>Integrated full-featured user accounts, live dispatch tracking, instant QR payments, interactive custom gifts, and the Blossom Rewards club.</p>
                </div>
            </div>

            <div class="timeline-item right">
                <div class="timeline-content">
                    <h3>🚀 Global Milestone</h3>
                    <p>Officially launched with nationwide deliveries, expanding collections across fresh flower bouquets, bespoke cakes, fragrances, and luxury hampers.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

</body>
</html>
