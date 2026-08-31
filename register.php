<?php
require 'auth.php';
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: signup.php'); exit; }
verify_csrf();

$full = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$bracu = trim($_POST['bracu_id'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'generalUser';

if ($full === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($username) < 3 || strlen($password) < 6 || !in_array($role, ['generalUser','volunteer'], true)) {
    header('Location: signup.php?err=' . urlencode('Please provide valid registration information.'));
    exit;
}

try {
    $stmt = $conn->prepare('INSERT INTO users(full_name,email,phone,bracu_id,join_date,username,password,role) VALUES(?,?,?,?,CURDATE(),?,?,?)');
    $phoneVal = $phone !== '' ? $phone : null;
    $bracuVal = $bracu !== '' ? $bracu : null;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('sssssss', $full, $email, $phoneVal, $bracuVal, $username, $hash, $role);
    $stmt->execute();
    $uid = $conn->insert_id;
    if ($role === 'volunteer') {
        $s = $conn->prepare('INSERT INTO volunteer_stats(user_id) VALUES(?)');
        $s->bind_param('i', $uid);
        $s->execute();
    }
    redirect_with_message('index.php', 'Registration successful. You can now log in.');
} catch (mysqli_sql_exception $e) {
    header('Location: signup.php?err=' . urlencode('Username, email or BRACU ID already exists.'));
    exit;
}
