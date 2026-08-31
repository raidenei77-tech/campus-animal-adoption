<?php
require 'auth.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Campus Animal Adoption Center</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
<div class="auth-card">
  <div class="cat-logo"><img src="assets/cat-logo.png" alt="Cat Adoption Center logo"></div>
  <h1>Welcome to<br>Campus Animal Adoption Center!</h1>
  <p class="subtitle">Please login to continue</p>
  <?php if ($msg): ?><div class="notice"><?= h($msg) ?></div><?php endif; ?>
  <form action="login.php" method="post">
    <?= csrf_field() ?>
    <div class="input-group"><span class="input-icon">👤</span><input name="username" autocomplete="username" placeholder="Username" required></div>
    <div class="input-group"><span class="input-icon">🔒</span><input type="password" id="password" name="password" autocomplete="current-password" placeholder="Password" required><button class="eye-button" type="button" aria-label="Show or hide password" onclick="const p=document.getElementById('password'); p.type=p.type==='password'?'text':'password'">👁</button></div>
    <button class="btn" style="width:100%" type="submit">Login 🐾</button>
  </form>
  <p style="margin-top:20px">Don't have an account? <a href="signup.php"><b>Sign up</b></a> ♥</p>
</div>
</body>
</html>
