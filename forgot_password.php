<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body{
  font-family: 'Poppins', sans-serif;
  min-height: 100vh;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;

  background:
    radial-gradient(circle at top left, #ffe6ef 20%, transparent 40%),
    radial-gradient(circle at bottom right, #fde2f3 20%, transparent 40%),
    linear-gradient(to bottom right,#fff1f6,#f8d6e6,#fff8fc);
}

/* Floating flower animation */
body::before,
body::after{
  content:"🌸";
  position:absolute;
  font-size:60px;
  opacity:0.15;
  animation: float 10s infinite ease-in-out;
}

body::before{
  top:10%;
  left:15%;
}

body::after{
  bottom:15%;
  right:10%;
  animation-delay: 3s;
}

@keyframes float{
  0%{ transform: translateY(0); }
  50%{ transform: translateY(-20px); }
  100%{ transform: translateY(0); }
}

.container{
  width:420px;
  padding:35px;
  border-radius:24px;

  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(8px);

  box-shadow:
    0 10px 30px rgba(204,91,130,0.25),
    inset 0 0 30px rgba(255,230,239,0.6);

  animation: popIn 0.7s ease;
}

@keyframes popIn{
  from{
    opacity:0;
    transform: scale(0.95) translateY(20px);
  }
  to{
    opacity:1;
    transform: scale(1) translateY(0);
  }
}

h2{
  text-align:center;
  color:#c14d79;
  font-weight:600;
  margin-bottom:25px;
  position:relative;
}

h2::after{
  content:"";
  width:60px;
  height:4px;
  background:#f4b6c6;
  display:block;
  margin:8px auto 0;
  border-radius:5px;
}

/* Inputs */
input{
  width:100%;
  padding:14px;
  margin-top:14px;
  border-radius:14px;

  border:2px solid #f4c6d4;
  background:#fff9fb;
  outline:none;

  transition: all 0.3s ease;
}

input:focus{
  border-color:#cc5b82;
  box-shadow:0 0 12px rgba(204,91,130,0.35);
  transform: scale(1.02);
}

/* Button */
button{
  padding:14px;
  margin-top:20px;
  border-radius:30px;
  width:100%;

  background: linear-gradient(135deg,#cc5b82,#f29ab5);
  color:white;
  border:none;

  font-size:15px;
  font-weight:600;
  cursor:pointer;

  transition: all 0.3s ease;
}

button:hover{
  transform: translateY(-2px) scale(1.02);
  box-shadow:0 8px 20px rgba(204,91,130,0.45);
}

button:active{
  transform: scale(0.98);
}

/* Messages */
.success{
  color:#2f8f46;
  background:#e8f7ed;
  padding:8px 12px;
  border-radius:10px;
  margin-top:10px;
  text-align:center;
}

.error{
  color:#b42318;
  background:#fdeaea;
  padding:8px 12px;
  border-radius:10px;
  margin-top:10px;
  text-align:center;
}

.hidden{
  display:none;
}
</style>
</head>

<body>

<div class="container">
<h2>Forgot Password</h2>

<!-- STEP 1 -->
<div id="step1">
  <input type="text" id="contact" placeholder="Email or Mobile">
<button type="button" onclick="sendOTP()">Send OTP</button>
  <p id="msg1"></p>
</div>

<!-- STEP 2 -->
<div id="step2" class="hidden">
  <input type="text" id="otpInput" placeholder="Enter OTP">
  <button type="button" onclick="verifyOTP()">Verify OTP</button>
  <p id="msg2"></p>
</div>

<!-- STEP 3 -->
<div id="step3" class="hidden">
  <input type="password" id="newPass" placeholder="New Password">
  <input type="password" id="confirmPass" placeholder="Confirm Password">
  <button onclick="submitPassword()">Change Password</button>
  <p id="passMsg"></p>
</div>
</div>

<script>
  console.log("JS loaded");


function sendOTP() {
    const contact = document.getElementById("contact").value.trim();

    if (contact === "") {
        alert("Please enter mobile number or email");
        return;
    }

    fetch("send_otp.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "contact=" + encodeURIComponent(contact)
    })
    .then(res => res.text())
    .then(data => {
        alert("OTP sent successfully!\n" + data);

        // 🔴 THIS IS THE IMPORTANT PART
        document.getElementById("step1").classList.add("hidden");
        document.getElementById("step2").classList.remove("hidden");
    })
    .catch(() => alert("Server error"));
}





function verifyOTP() {
    const enteredOTP = document.getElementById("otpInput").value.trim();

    fetch("verify_otp.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "otp=" + encodeURIComponent(enteredOTP)
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            alert("OTP Verified ✅");
            document.getElementById("step2").classList.add("hidden");
            document.getElementById("step3").classList.remove("hidden");
        } else {
            alert("Invalid OTP ❌");
        }
    });
}


function submitPassword() {
    const newPass = document.getElementById("newPass").value;
    const confirmPass = document.getElementById("confirmPass").value;

    const rules = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=!]).{6,}$/;

    if (!rules.test(newPass)) {
        alert("Password must have uppercase, lowercase, number & special char");
        return;
    }

    if (newPass !== confirmPass) {
        alert("Passwords do not match");
        return;
    }

    fetch("update_password.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "password=" + encodeURIComponent(newPass)
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            alert("Password updated successfully 🎉");
            window.location.href = "BBlogin.html";
        } else {
            alert("Something went wrong ❌");
        }
    });
}



function showMsg(id, type, msg) {
    const el = document.getElementById(id);
    el.className = type;
    el.textContent = msg;
}
</script>


</body>
</html>
