<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Floral Registration — Online Flower Store</title>

  <style>
    :root{
      --bg-1: #FFF8F2;
      --pastel-pink: #FFDDE6;
      --pastel-peach: #FFEFD6;
      --pastel-mint: #E8FFF4;
      --accent: #C48EBA;
      --muted: #6b6b6b;
      --radius: 14px;
      --shadow: 0 8px 24px rgba(17,24,39,0.08);
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    html,body{
      height:100%;
      margin:0;
      background:
        radial-gradient(1200px 600px at 10% 10%, var(--pastel-peach) 0%, transparent 30%),
        radial-gradient(1000px 500px at 90% 90%, var(--pastel-mint) 0%, transparent 25%),
        linear-gradient(180deg, var(--bg-1) 0%, #FFFDFC 100%);
      color:#222;
    }

    .floral-decor {
      position:fixed;
      inset:0;
      pointer-events:none;
      overflow:hidden;
      z-index:0;
    }
    .floral-decor svg{
      position:absolute;
      width:400px;
      opacity:0.9;
      filter: drop-shadow(0 6px 18px rgba(0,0,0,0.06));
    }
    .floral-decor .top-left{ top:-30px; left:-30px; transform: rotate(-10deg); }
    .floral-decor .bottom-right{ right:-40px; bottom:-40px; transform: scale(1.1) rotate(10deg); opacity:0.95;}

    .container{
      min-height:100%;
      display:flex;
      align-items:center;
      justify-content:center;
      padding:48px 20px;
      position:relative;
      z-index:1;
    }

    .card {
      width:100%;
      max-width:880px;
      display:grid;
      grid-template-columns: 1fr 420px;
      gap:28px;
      background: rgba(255,255,255,0.75);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow:hidden;
      backdrop-filter: blur(8px);
    }

    .promo {
      padding:36px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      background:
        linear-gradient(135deg, rgba(255,255,255,0.02), rgba(255,255,255,0.02));
      color:#2b2b2b;
    }
    .promo h1{
      margin:0;
      font-size:1.6rem;
      color:#2b2b2b;
    }
    .promo p{
      color:var(--muted);
      line-height:1.5;
    }

    .form-wrap{
      padding:28px;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    form.register-form{
      width:100%;
      max-width:360px;
    }

    .form-title{
      font-size:1.3rem;
      margin-bottom:10px;
      color:#222;
    }

    .field{
      display:flex;
      flex-direction:column;
      gap:8px;
      margin-bottom:14px;
    }

    label{
      font-size:0.85rem;
      color:#444;
    }

    input{
      padding:12px 14px;
      border-radius:10px;
      border:1px solid rgba(16,24,40,0.08);
      background:#fff;
      outline:none;
      font-size:0.95rem;
      box-shadow:0 2px 8px rgba(16,24,40,0.03);
      transition: box-shadow .18s, transform .12s;
    }

    input:focus{
      box-shadow: 0 6px 18px rgba(196,142,186,0.14);
      transform: translateY(-1px);
      border-color: rgba(196,142,186,0.4);
    }

    button{
      padding:12px 16px;
      border-radius:10px;
      border:none;
      font-weight:600;
      font-size:1rem;
      cursor:pointer;
      background:linear-gradient(90deg,#FFDDE6,#C48EBA);
      color:#4a2136;
      box-shadow:0 8px 20px rgba(196,142,186,0.12);
      transition:transform .12s, box-shadow .12s;
    }

    button:hover{
      transform:translateY(-1px);
      box-shadow:0 12px 28px rgba(196,142,186,0.18);
    }

    .alt{
      text-align:center;
      margin-top:10px;
      font-size:0.93rem;
      color:var(--muted);
    }

    .alt a{
      color:#7a3b5e;
      text-decoration:none;
      font-weight:600;
    }
    .alt a:hover{text-decoration:underline;}

    @media(max-width:880px){
      .card{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>

  <!-- Decorative SVGs -->
  <div class="floral-decor" aria-hidden="true">
    <svg class="top-left" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="grad1" x1="0" x2="1">
          <stop offset="0" stop-color="#FFDDE6"/>
          <stop offset="1" stop-color="#FFEFD6"/>
        </linearGradient>
      </defs>
      <circle cx="60" cy="60" r="40" fill="url(#grad1)" />
      <path d="M20 100 C40 80, 80 80, 100 100" stroke="#C48EBA" stroke-width="3" fill="none" stroke-linecap="round"/>
    </svg>

    <svg class="bottom-right" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="grad2" x1="0" x2="1">
          <stop offset="0" stop-color="#E8FFF4"/>
          <stop offset="1" stop-color="#FFDDE6" />
        </linearGradient>
      </defs>
      <rect x="30" y="30" rx="32" ry="32" width="140" height="140" fill="url(#grad2)" opacity="0.85"/>
      <circle cx="120" cy="60" r="18" fill="#C48EBA" />
    </svg>
  </div>

  <div class="container">
    <div class="card">
      <section class="promo">
        <h1>Create Your Floral Account</h1>
        <p>Register to explore our fresh floral collections, get exclusive discounts, and send love with every bouquet.</p>
        <p style="font-size:0.9rem; color:var(--muted);">Already have an account?  
          <a href="BBlogin.php" style="color:#7a3b5e; text-decoration:none; font-weight:600;">Login here</a>.
        </p>
      </section>

      <aside class="form-wrap">
        <form class="register-form" action="register.php" method="post">
          <div class="form-title">Registration Form</div>

          <div class="field">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
          </div>

          <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
          </div>

          <div class="field">
            <label for="mobile">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" placeholder="Enter your mobile number" pattern="[0-9]{10}" required>
          </div>

          <div class="field">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required>
          </div>

          <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
          </div>

          <div class="field">
            <label for="confirm">Confirm Password</label>
            <input type="password" id="confirm" name="confirm" placeholder="Re-enter password" required>
          </div>

          <button type="submit">Register</button>

          <div class="alt">
            Already registered?
            <a href="BBlogin.php">Login here</a>
          </div>
        </form>
      </aside>
    </div>
  </div>

  <!-- <script>
    function registerSubmit(e){
      e.preventDefault();
      const pass = document.getElementById('password').value;
      const confirm = document.getElementById('confirm').value;
      const mobile = document.getElementById('mobile').value;

      if(pass !== confirm){
        alert('Passwords do not match!');
        return false;
      }
      if(!/^[0-9]{10}$/.test(mobile)){
        alert('Please enter a valid 10-digit mobile number!');
        return false;
      }

      alert('Registration successful! Redirecting to login page...');
      window.location.href = 'BBlogin.html';
      return false;
    }
  </script> -->

</body>
</html>
