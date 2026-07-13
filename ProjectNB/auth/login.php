<?php
session_start();
$base = dirname(dirname($_SERVER['PHP_SELF']));
require_once __DIR__ . '/../core/connect_database.php';

$error = false;
if (isset($_POST['login']) && $conn) {
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $sql = "SELECT * FROM user_pro WHERE user_name = '$user' AND user_password = '$pass'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $_SESSION['admin'] = "ok";
        header("Location: $base/views/showproduct.php");
        exit;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — NB Store</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: #0a0a0a; font-family: 'DM Sans', sans-serif; font-weight: 300; min-height: 100vh; display: grid; grid-template-columns: 1fr 1fr; }
  .left { background: #cf2b2b; display: flex; flex-direction: column; justify-content: flex-end; padding: 64px; position: relative; overflow: hidden; }
  .left::before { content: 'NB'; position: absolute; top: -60px; left: -30px; font-family: 'Bebas Neue', sans-serif; font-size: 400px; color: rgba(0,0,0,.15); line-height: 1; pointer-events: none; }
  .left-label { font-size: 11px; letter-spacing: 4px; text-transform: uppercase; color: rgba(255,255,255,.6); margin-bottom: 12px; }
  .left-title { font-family: 'Bebas Neue', sans-serif; font-size: 72px; line-height: .9; color: #fff; letter-spacing: 2px; margin-bottom: 24px; }
  .left-sub { color: rgba(255,255,255,.7); font-size: 15px; line-height: 1.6; max-width: 300px; }
  .divider-line { width: 48px; height: 2px; background: rgba(255,255,255,.4); margin-bottom: 24px; }
  .right { display: flex; flex-direction: column; justify-content: center; padding: 80px 72px; }
  .form-eyebrow { font-size: 10px; letter-spacing: 4px; text-transform: uppercase; color: #cf2b2b; margin-bottom: 20px; }
  .form-title { font-family: 'Bebas Neue', sans-serif; font-size: 48px; letter-spacing: 2px; color: #f5f2ee; margin-bottom: 40px; line-height: 1; }
  .field { margin-bottom: 20px; }
  .field label { display: block; font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: #666; margin-bottom: 8px; }
  .field input { width: 100%; background: #161616; border: 1px solid #2a2a2a; color: #f5f2ee; padding: 14px 16px; font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 300; outline: none; transition: border-color .2s; }
  .field input:focus { border-color: #cf2b2b; }
  .field input::placeholder { color: #444; }
  .error-msg { background: rgba(207,43,43,.15); border: 1px solid rgba(207,43,43,.4); color: #ff7070; padding: 12px 16px; font-size: 13px; margin-bottom: 20px; }
  .submit-btn { width: 100%; padding: 16px; background: #cf2b2b; color: #fff; border: none; font-family: 'Bebas Neue', sans-serif; font-size: 20px; letter-spacing: 4px; cursor: pointer; transition: background .2s; margin-top: 8px; }
  .submit-btn:hover { background: #e83535; }
  .back-link { display: block; text-align: center; margin-top: 28px; color: #555; text-decoration: none; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; transition: color .2s; }
  .back-link:hover { color: #f5f2ee; }
  @media (max-width: 768px) { body { grid-template-columns: 1fr; } .left { display: none; } .right { padding: 60px 32px; } }
</style>
</head>
<body>
<div class="left">
  <p class="left-label">Admin Access Only</p>
  <div class="divider-line"></div>
  <h1 class="left-title">NEW<br>BALANCE<br>STORE</h1>
  <p class="left-sub">Manage your sneaker inventory, edit products, and oversee all store operations from here.</p>
</div>
<div class="right">
  <p class="form-eyebrow">Admin Panel</p>
  <h2 class="form-title">Sign In</h2>
  <?php if ($error): ?>
  <div class="error-msg">⚠ Incorrect username or password. Try again.</div>
  <?php endif; ?>
  <form method="post">
    <div class="field">
      <label>Username</label>
      <input type="text" name="user" placeholder="Enter username" required autocomplete="username">
    </div>
    <div class="field">
      <label>Password</label>
      <input type="password" name="pass" placeholder="Enter password" required autocomplete="current-password">
    </div>
    <button class="submit-btn" type="submit" name="login">Login</button>
  </form>
  <a href="<?= $base ?>/views/showproduct.php" class="back-link">← Back to Store</a>
</div>
</body>
</html>
