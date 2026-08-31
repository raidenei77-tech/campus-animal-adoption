<?php
require 'auth.php'; require_login(); require 'db.php'; require 'layout.php';
$uid=(int)$_SESSION['user_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $full=trim($_POST['full_name']??'');$email=trim($_POST['email']??'');$phone=trim($_POST['phone']??'');$bracu=trim($_POST['bracu_id']??'');
    if($full===''||!filter_var($email,FILTER_VALIDATE_EMAIL)) redirect_with_message('profile.php','Enter a valid name and email.');
    $phoneVal=$phone!==''?$phone:null;$bracuVal=$bracu!==''?$bracu:null;
    try{$s=$conn->prepare('UPDATE users SET full_name=?,email=?,phone=?,bracu_id=? WHERE user_id=?');$s->bind_param('ssssi',$full,$email,$phoneVal,$bracuVal,$uid);$s->execute();$_SESSION['full_name']=$full;redirect_with_message('profile.php','Profile updated.');}catch(mysqli_sql_exception $e){redirect_with_message('profile.php','Email or BRACU ID is already used by another account.');}
}
$s=$conn->prepare('SELECT * FROM users WHERE user_id=?');$s->bind_param('i',$uid);$s->execute();$u=$s->get_result()->fetch_assoc();
$cases=0;if($u['role']==='volunteer'){$q=$conn->prepare('SELECT number_of_cases_handled FROM volunteer_stats WHERE user_id=?');$q->bind_param('i',$uid);$q->execute();$x=$q->get_result()->fetch_assoc();$cases=(int)($x['number_of_cases_handled']??0);}
$myAnimals=$conn->prepare('SELECT COUNT(*) c FROM animal WHERE user_id=?');$myAnimals->bind_param('i',$uid);$myAnimals->execute();$animalsCount=(int)$myAnimals->get_result()->fetch_assoc()['c'];
$myReports=$conn->prepare('SELECT COUNT(*) c FROM reports WHERE reported_by=?');$myReports->bind_param('i',$uid);$myReports->execute();$reportsCount=(int)$myReports->get_result()->fetch_assoc()['c'];
page_top('Profile');
?>
<div class="profile-grid"><aside class="card profile-side"><img src="assets/cat-logo.png" alt="Profile"><h2><?=h($u['full_name'])?></h2><span class="tag"><?=h($u['role']==='volunteer'?'Volunteer':'General User')?></span><p class="muted">Registered animals: <?=$animalsCount?><br>Reports submitted: <?=$reportsCount?><?php if($u['role']==='volunteer'):?><br>Cases handled: <?=$cases?><?php endif;?></p></aside>
<section class="card"><h2>Account details</h2><form class="form-grid" method="post"><?=csrf_field()?><input name="full_name" value="<?=h($u['full_name'])?>" placeholder="Full name" required><input type="email" name="email" value="<?=h($u['email'])?>" placeholder="Email" required><input name="phone" value="<?=h($u['phone']??'')?>" placeholder="Phone"><input name="bracu_id" value="<?=h($u['bracu_id']??'')?>" placeholder="BRACU ID"><div class="full muted">Username: <b><?=h($u['username'])?></b> · Joined: <?=h($u['join_date'])?></div><button class="btn full">Update Profile</button></form></section></div>
<?php page_bottom(); ?>
