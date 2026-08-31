<?php
require 'auth.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }
$err = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up - Campus Animal Adoption Center</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
<div class="auth-card">
  <div class="cat-logo"><img src="assets/cat-logo.png" alt="Cat logo"></div>
  <h1>Create Account 🐾</h1><p class="subtitle">Join our campus animal-care community</p>
  <?php if ($err): ?><div class="error"><?= h($err) ?></div><?php endif; ?>
  <form action="register.php" method="post">
    <?= csrf_field() ?>
    <div class="input-group"><span class="input-icon">👤</span><input name="full_name" placeholder="Full Name" required maxlength="200"></div>
    <div class="input-group"><span class="input-icon">📧</span><input type="email" name="email" placeholder="Email" required maxlength="200"></div>
    <div class="input-group"><span class="input-icon">📱</span><input name="phone" placeholder="Phone Number" maxlength="20"></div>
    <div class="input-group"><span class="input-icon">🎓</span><input name="bracu_id" placeholder="BRACU ID (optional)" maxlength="50"></div>
    <div class="input-group"><span class="input-icon">🐾</span><input name="username" placeholder="Username" required minlength="3" maxlength="100"></div>
    <div class="input-group"><span class="input-icon">🔒</span><input type="password" name="password" minlength="6" placeholder="Password (minimum 6 characters)" required></div>
    <div class="role-section"><label><input type="radio" name="role" value="generalUser" checked> General User</label><label><input type="radio" name="role" value="volunteer"> Volunteer</label></div>
    <button class="btn" style="width:100%">Sign Up 🐾</button>
  </form>
  <p style="margin-top:18px">Already have an account? <a href="index.php"><b>Login</b></a></p>
</div>
</body></html>
