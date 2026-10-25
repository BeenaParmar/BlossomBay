<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "db.php";

$type = $_GET['type'] ?? 'self';
$is_other = ($type === 'other');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Location | BlossomBay</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .location-wrapper {
            max-width: 650px;
            width: 92%;
            margin: 35px auto 60px auto;
            flex: 1;
        }

        .location-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 35px;
            box-shadow: var(--shadow-subtle);
            border: 1px solid var(--border-color);
        }

        .location-card h2 {
            font-family: var(--font-serif);
            font-size: 26px;
            color: #3b232c;
            margin-bottom: 6px;
            text-align: center;
        }

        .location-card p.subtitle {
            font-size: 14px;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 25px;
        }

        .map-frame {
            width: 100%;
            height: 220px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            margin-bottom: 22px;
        }

        .form-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- REUSABLE NAVBAR -->
    <?php include "navbar.php"; ?>

    <main class="location-wrapper">
        <div class="location-card">
            <h2>
                <i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> 
                <?php echo $is_other ? "Surprise Recipient's Address" : "My Delivery Address"; ?>
            </h2>
            <p class="subtitle">
                <?php echo $is_other ? "Enter recipient shipping information and your custom gift message" : "Provide your full address for doorstep courier arrival"; ?>
            </p>

            <!-- Embedded Map -->
            <iframe 
                src="https://maps.google.com/maps?q=india&t=&z=5&ie=UTF8&iwloc=&output=embed"
                class="map-frame"
                loading="lazy">
            </iframe>

            <form action="save_location.php" method="POST">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">

                <div class="form-grid">
                    <input type="text" name="address1" class="bb-input" placeholder="House / Flat / Building Name & Floor" required>
                    <input type="text" name="address2" class="bb-input" placeholder="Street, Area, Landmark" required>
                    <input type="text" name="address3" class="bb-input" placeholder="Pincode / Postal Code / Additional Directions">

                    <div class="form-row">
                        <select name="country" id="country" class="bb-select" onchange="loadCities()" required>
                            <option value="">Select Country</option>
                            <option value="India" selected>India</option>
                            <option value="USA">USA</option>
                            <option value="UK">UK</option>
                            <option value="Canada">Canada</option>
                        </select>

                        <select name="city" id="city" class="bb-select" required>
                            <option value="">Select City</option>
                            <option value="Mumbai" selected>Mumbai</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Pune">Pune</option>
                            <option value="Bangalore">Bangalore</option>
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:700; color:#555; margin-bottom:5px;">
                            Preferred Delivery Date
                        </label>
                        <input type="date" name="delivery_date" class="bb-input" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    </div>

                    <textarea name="message" class="bb-textarea" rows="3" placeholder="<?php echo $is_other ? "Write a heartfelt note for the card (e.g. Happy Birthday sweetheart! From Maya)..." : "Specific gate or delivery instructions for our courier partner..."; ?>"></textarea>

                    <button type="submit" class="bb-btn bb-btn-primary" style="padding:14px; font-size:15px; margin-top:10px;">
                        <i class="fa-solid fa-floppy-disk"></i> Confirm & Save Delivery Location
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- REUSABLE FOOTER -->
    <?php include "footer.php"; ?>

    <script>
        function loadCities() {
            let country = document.getElementById("country").value;
            let city = document.getElementById("city");
            city.innerHTML = "<option value=''>Select City</option>";

            let data = {
                "India": ["Mumbai","Delhi","Pune","Bangalore","Hyderabad","Chennai","Kolkata"],
                "USA": ["New York","Los Angeles","Chicago","San Francisco"],
                "UK": ["London","Manchester","Birmingham"],
                "Canada": ["Toronto","Vancouver","Montreal"]
            };

            if(data[country]){
                data[country].forEach(c => {
                    let opt = document.createElement("option");
                    opt.value = c;
                    opt.text = c;
                    city.appendChild(opt);
                });
            }
        }
    </script>
</body>
</html>