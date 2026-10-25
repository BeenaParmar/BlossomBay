<?php
session_start();

include("db.php");     // Your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username=? OR email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email']     = $user['email'];

            header("Location: BBhome.php");
            exit();

        } else {

            header("Location: BBlogin.php?error=Incorrect Password");
            exit();

        }

    } else {

        header("Location: BBlogin.php?error=User not found");
        exit();

    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Floral Login — Online Flower Store</title>

  <style>
    :root{
      --bg-1: #FFF8F2;   /* soft cream */
      --pastel-pink: #FFDDE6;
      --pastel-peach: #FFEFD6;
      --pastel-mint: #E8FFF4;
      --accent: #C48EBA; /* muted mauve */
      --muted: #6b6b6b;
      --glass: rgba(255,255,255,0.75);
      --glass-2: rgba(255,255,255,0.60);
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
      color: #222;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    /* Decorative floral SVGs container */
    .floral-decor {
      position:fixed;
      inset:0;
      pointer-events:none;
      overflow:hidden;
      z-index:0;
    }
    .floral-decor svg{
      position:absolute;
      width:420px;
      opacity:0.9;
      filter: drop-shadow(0 6px 18px rgba(0,0,0,0.06));
    }
    .floral-decor .top-left{ top:-30px; left:-30px; transform: rotate(-10deg); }
    .floral-decor .bottom-right{ right:-40px; bottom:-40px; transform: scale(1.1) rotate(10deg); opacity:0.95;}
    .floral-decor .bottom-left{ left:-60px; bottom:40%; transform: scale(.9) rotate(-6deg); opacity:0.7;}

    /* Center container */
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
      max-width:920px;
      display:grid;
      grid-template-columns: 1fr 420px;
      gap:28px;
      background: linear-gradient(180deg, var(--glass) 0%, var(--glass-2) 100%);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow:hidden;
      backdrop-filter: blur(8px) saturate(120%);
      border: 1px solid rgba(255,255,255,0.6);
    }

    /* Left panel: marketing */
    .promo {
      padding:36px;
      display:flex;
      flex-direction:column;
      justify-content:center;
      gap:16px;
      background:
        linear-gradient(135deg, rgba(255,255,255,0.02), rgba(255,255,255,0.02));
    }
    .logo {
      display:flex;
      align-items:center;
      gap:12px;
      font-weight:700;
      color:var(--accent);
      font-size:1.15rem;
    }
    .logo .mark {
      width:44px;
      height:44px;
      border-radius:10px;
      background: linear-gradient(135deg,#FFDDE6,#FFEFD6);
      display:grid;
      place-items:center;
      box-shadow: 0 4px 12px rgba(196,142,186,0.12);
    }
    .promo h1{
      margin:0;
      font-size:1.6rem;
      color:#2b2b2b;
    }
    .promo p{
      margin:0;
      color:var(--muted);
      line-height:1.5;
    }

    /* Right panel: form */
    .form-wrap{
      padding:28px;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    form.login-form{
      width:100%;
      max-width:360px;
    }
    .form-title{
      font-size:1.2rem;
      margin:0 0 8px 0;
      color:#222;
    }
    .form-sub{
      margin:0 0 18px 0;
      color:var(--muted);
      font-size:0.93rem;
    }

    .field{
      display:flex;
      flex-direction:column;
      gap:8px;
      margin-bottom:14px;
    }
    label{
      font-size:0.85rem;
      color: #444;
    }
    input[type="text"],
    input[type="password"],
    input[type="email"]{
      padding:12px 14px;
      border-radius:10px;
      border:1px solid rgba(16,24,40,0.06);
      background: #fff;
      outline:none;
      font-size:0.95rem;
      transition: box-shadow .18s, transform .12s;
      box-shadow: 0 2px 8px rgba(16,24,40,0.03);
    }
    input:focus{
      box-shadow: 0 6px 18px rgba(196,142,186,0.14);
      transform: translateY(-1px);
      border-color: rgba(196,142,186,0.4);
    }

    .row {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }

    .link-small{
      font-size:0.9rem;
      color:var(--accent);
      text-decoration:none;
    }
    .link-small:hover{ text-decoration:underline; }

    .actions{
      margin-top:6px;
      display:flex;
      flex-direction:column;
      gap:12px;
    }

    button.btn {
      padding:12px 16px;
      border-radius:10px;
      border: none;
      font-weight:600;
      font-size:1rem;
      cursor:pointer;
      background: linear-gradient(90deg,#FFDDE6,#C48EBA);
      color: #4a2136;
      box-shadow: 0 8px 20px rgba(196,142,186,0.12);
      transition: transform .12s, box-shadow .12s, opacity .12s;
    }
    button.btn:active{ transform: translateY(1px) scale(.998); }
    button.btn:disabled{ opacity:0.6; cursor:not-allowed; }

    .alt {
      display:flex;
      justify-content:center;
      gap:8px;
      align-items:center;
      font-size:0.92rem;
      color:var(--muted);
    }

    .register-link{
      color: #7a3b5e;
      text-decoration:none;
      font-weight:600;
    }
    .register-link:hover{ text-decoration:underline; }

    /* small helper */
    .eye-btn {
      background:transparent;
      border:none;
      cursor:pointer;
      font-size:0.95rem;
      color:var(--muted);
      padding:4px;
    }

    /* responsive */
    @media (max-width:880px){
      .card{ grid-template-columns: 1fr; max-width:720px; }
      .floral-decor svg{ width:300px; }
      .promo{ order:2; padding-top:10px; }
      .form-wrap{ order:1; padding-top:30px; }
    }

    @media (max-width:420px){
      .card{ margin:0 8px; }
      .floral-decor svg{ display:none; }
    }

    /* small accessibility focus */
    a:focus, button:focus, input:focus { outline: 3px solid rgba(196,142,186,0.18); outline-offset:2px; }
  </style>
</head>
<body>

  <!-- Decorative SVG florals -->
  <div class="floral-decor" aria-hidden="true">
    <!-- top-left floral -->
    <svg class="top-left" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" role="img">
      <defs>
        <linearGradient id="g1" x1="0" x2="1">
          <stop offset="0" stop-color="#FFDDE6"/>
          <stop offset="1" stop-color="#FFEFD6"/>
        </linearGradient>
      </defs>
      <g transform="translate(20,20)">
        <circle cx="40" cy="40" r="30" fill="url(#g1)" />
        <path d="M20 80 C40 60, 60 60, 80 80" stroke="#C48EBA" stroke-width="3" fill="none" stroke-linecap="round"/>
        <g fill="#C48EBA" opacity="0.8">
          <ellipse cx="110" cy="25" rx="18" ry="10"/>
          <ellipse cx="130" cy="45" rx="12" ry="6"/>
        </g>
      </g>
    </svg>

    <!-- bottom-right floral -->
    <svg class="bottom-right" viewBox="0 0 240 240" xmlns="http://www.w3.org/2000/svg" role="img">
      <defs>
        <linearGradient id="g2" x1="0" x2="1">
          <stop offset="0" stop-color="#E8FFF4"/>
          <stop offset="1" stop-color="#FFDDE6" />
        </linearGradient>
      </defs>
      <g transform="translate(10,10)">
        <rect x="0" y="0" rx="32" ry="32" width="180" height="180" fill="url(#g2)" opacity="0.85"/>
        <circle cx="140" cy="40" r="18" fill="#C48EBA" />
        <path d="M40 160 C60 140, 120 120, 160 150" stroke="#9FB8A8" stroke-width="3" fill="none"/>
      </g>
    </svg>

    <!-- subtle bloom bottom-left -->
    <svg class="bottom-left" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg" role="img">
      <g>
        <circle cx="70" cy="70" r="44" fill="#FFEFD6" opacity="0.95"/>
        <path d="M30 80 q40 -40 80 0" stroke="#C48EBA" stroke-width="2.5" fill="none"/>
      </g>
    </svg>
  </div>

  <div class="container">
    <div class="card" role="region" aria-label="Login container">

      <section class="promo" aria-hidden="false">
        <div class="logo" aria-hidden="false">
          <div class="mark" aria-hidden="true">
            <!-- small floral icon -->
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="6" cy="8" r="2.6" fill="#C48EBA"/>
              <circle cx="12" cy="6" r="2.6" fill="#FFDDE6"/>
              <circle cx="18" cy="8" r="2.6" fill="#E8FFF4"/>
              <circle cx="12" cy="16" r="3.6" fill="#FFEFD6"/>
            </svg>
          </div>
          BlossomBay
        </div>

        <h1>Welcome back</h1>
        <p>Sign in to manage orders, browse fresh bouquets, and place same-day deliveries. Secure checkout and easy tracking.</p>

        <div style="margin-top:18px; font-size:0.92rem; color:var(--muted);">
          <strong>Tip:</strong> Use a strong password. Enable two-factor authentication in account settings after login.
        </div>
      </section>

      <aside class="form-wrap" aria-label="Login form">
        <form class="login-form" action="login.php" method="post" novalidate>
        <div class="form-title">Account Login</div>
<div id="error-message" style="color:red; font-size:0.9rem; margin-bottom:10px;"></div>
<div class="form-sub">Enter your credentials to continue</div>


          <div class="field">
            <label for="username">Username or Email</label>
            <input id="username" name="username" type="text" autocomplete="username" placeholder="you@example1234" required />
          </div>

          <div class="field" style="position:relative;">
            <label for="password">Password</label>
            <div style="display:flex; align-items:center;">
              <input id="password" name="password" type="password" autocomplete="current-password" placeholder="••••••••" required style="flex:1;" />
              <button type="button" class="eye-btn" aria-label="Toggle password visibility" onclick="togglePassword()" title="Show / hide password">👁️</button>
            </div>
          </div>

          <div class="row" style="margin-top:4px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <input id="remember" name="remember" type="checkbox" style="width:16px;height:16px;" />
              <label for="remember" style="font-size:0.92rem;color:var(--muted);">Remember me</label>
            </div>
            <div>
              <a href="forgot_password.php" class="link-small">Forgot password?</a>
            </div>
          </div>

          <div class="actions">
            <button type="submit" class="btn" aria-label="Login">Log In</button>
            <div class="alt">
              <span>New here?</span>
              <a href="BBregistration.php" class="register-link" >Create an account</a>
            </div>
          </div>

        </form>
      </aside>

    </div>
  </div>

 <script>
  // Toggle password visibility
  function togglePassword(){
    const pw = document.getElementById('password');
    if(!pw) return;
    pw.type = pw.type === 'password' ? 'text' : 'password';
  }

  // Show error message from URL
  window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    if(error){
      document.getElementById('error-message').textContent = error;
    }
  });
</script>

</body>
</html>
