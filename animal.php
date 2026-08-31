<?php
require 'auth.php'; require_login(); require 'db.php'; require 'layout.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect_with_message('animals.php', 'Animal not found.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    require_volunteer();
    $status = $_POST['status'] ?? '';
    if (!in_array($status,['reported','rescued','under_treatment','available','adopted'],true)) redirect_with_message('animal.php?id='.$id,'Invalid animal status.');
    $uid = (int)$_SESSION['user_id'];
    $conn->begin_transaction();
    try {
        $s=$conn->prepare('UPDATE animal SET status=? WHERE animal_id=?'); $s->bind_param('si',$status,$id); $s->execute();
        $h=$conn->prepare('INSERT INTO animal_status_history(animal_id,status,changed_by) VALUES(?,?,?)'); $h->bind_param('isi',$id,$status,$uid); $h->execute();
        $conn->commit();
        redirect_with_message('animal.php?id='.$id,'Status updated.');
    } catch(Throwable $e) { $conn->rollback(); throw $e; }
}

$s=$conn->prepare('SELECT a.*,u.full_name FROM animal a JOIN users u ON a.user_id=u.user_id WHERE animal_id=?'); $s->bind_param('i',$id); $s->execute(); $a=$s->get_result()->fetch_assoc();
if(!$a) redirect_with_message('animals.php','Animal not found.');
$hist=$conn->prepare('SELECT h.*,u.full_name FROM animal_status_history h LEFT JOIN users u ON h.changed_by=u.user_id WHERE animal_id=? ORDER BY changed_at DESC,status_id DESC'); $hist->bind_param('i',$id); $hist->execute(); $history=$hist->get_result();
page_top($a['name']);
?>
<div class="profile-grid">
<aside class="card profile-side"><img src="<?=h($a['photo']?'uploads/'.$a['photo']:'assets/cat-logo.png')?>" alt="<?=h($a['name'])?>"><h2><?=h($a['name'])?></h2><span class="tag"><?=h(str_replace('_',' ',$a['status']))?></span></aside>
<section class="card"><h2>Animal details</h2><p><b>Species:</b> <?=h($a['species'])?><br><b>Gender:</b> <?=h($a['gender'])?><br><b>Age:</b> <?=h($a['age']===null?'Unknown':$a['age'])?><br><b>Found at:</b> <?=h($a['location_found'])?><br><b>Pattern:</b> <?=h($a['pattern']?:'—')?><br><b>Body colour:</b> <?=h($a['body_colour']?:'—')?><br><b>Eye colour:</b> <?=h($a['eye_colour']?:'—')?><br><b>Registered:</b> <?=h($a['date_registered'])?> by <?=h($a['full_name'])?></p>
<div class="actions"><?php if($a['status']==='available'):?><a class="btn" href="adoptions.php?animal_id=<?=$id?>">Request adoption</a><?php endif;?><a class="btn secondary" href="reports.php?animal_id=<?=$id?>">Create report</a></div>
<?php if(is_volunteer()):?><hr><form method="post" class="inline-form"><?=csrf_field()?><label><b>Update status</b></label><select name="status"><?php foreach(['reported','rescued','under_treatment','available','adopted'] as $st):?><option value="<?=$st?>" <?=$a['status']===$st?'selected':''?>><?=h(ucwords(str_replace('_',' ',$st)))?></option><?php endforeach;?></select><button class="btn small">Update</button></form><?php endif;?></section>
</div>
<div class="section-head"><h2>Status history</h2></div>
<div class="table-wrap"><table><tr><th>Status</th><th>Changed at</th><th>Changed by</th></tr><?php while($row=$history->fetch_assoc()):?><tr><td><?=h(ucwords(str_replace('_',' ',$row['status'])))?></td><td><?=h($row['changed_at'])?></td><td><?=h($row['full_name']??'System')?></td></tr><?php endwhile;?></table></div>
<?php page_bottom(); ?>
