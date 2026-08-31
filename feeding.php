<?php
require 'auth.php'; require_login(); require 'db.php'; require 'layout.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_volunteer(); verify_csrf();
    $animal=(int)($_POST['animal_id']??0); $food=trim($_POST['food_type']??''); $qty=trim($_POST['quantity']??''); $time=str_replace('T',' ',trim($_POST['feeding_time']??''));
    if($animal<=0||$food===''||$qty===''||$time==='') redirect_with_message('feeding.php','Complete the required feeding information.');
    $uid=(int)$_SESSION['user_id'];
    $conn->begin_transaction();
    try{
        $s=$conn->prepare('INSERT INTO feeding_schedule(animal_id,food_type,quantity,feeding_time,user_id) VALUES(?,?,?,?,?)');$s->bind_param('isssi',$animal,$food,$qty,$time,$uid);$s->execute();$fid=$conn->insert_id;
        $cons=trim($_POST['food_consumed']??'');$beh=trim($_POST['behavior']??'');$obs=trim($_POST['observation']??'');$oth=trim($_POST['others']??'');
        $n=$conn->prepare('INSERT INTO feeding_notes(feeding_id,food_consumed,behavior,observation,others) VALUES(?,?,?,?,?)');$n->bind_param('issss',$fid,$cons,$beh,$obs,$oth);$n->execute();
        $conn->commit(); redirect_with_message('feeding.php','Feeding schedule saved.');
    }catch(Throwable $e){$conn->rollback();throw $e;}
}
$animals=$conn->query("SELECT animal_id,name FROM animal WHERE status<>'adopted' ORDER BY name");
$rows=$conn->query('SELECT f.*,a.name,u.full_name,n.food_consumed,n.behavior,n.observation,n.others FROM feeding_schedule f JOIN animal a ON f.animal_id=a.animal_id JOIN users u ON f.user_id=u.user_id LEFT JOIN feeding_notes n ON f.feeding_id=n.feeding_id ORDER BY f.feeding_time DESC');
page_top('Feeding');
?>
<div class="section-head"><h1 class="page-title">Feeding Schedule</h1></div>
<?php if(is_volunteer()):?><form class="form-card form-grid" method="post"><?=csrf_field()?>
<select name="animal_id" required><option value="">Animal</option><?php while($a=$animals->fetch_assoc()):?><option value="<?=$a['animal_id']?>"><?=h($a['name'])?></option><?php endwhile;?></select>
<input name="food_type" placeholder="Food type" required maxlength="200"><input name="quantity" placeholder="Quantity" required maxlength="100"><input type="datetime-local" name="feeding_time" value="<?=date('Y-m-d\TH:i')?>" required>
<input name="food_consumed" placeholder="Food consumed" maxlength="200"><input name="behavior" placeholder="Behavior" maxlength="200"><textarea class="full" name="observation" placeholder="Observation"></textarea><textarea class="full" name="others" placeholder="Other notes"></textarea><button class="btn full">Save Feeding Entry</button>
</form><?php else:?><div class="notice">Feeding records are view-only for general users.</div><?php endif;?>
<div class="section-head"><h2>Feeding history</h2></div>
<div class="table-wrap"><table><tr><th>Animal</th><th>Time</th><th>Food</th><th>Quantity</th><th>Consumed</th><th>Behavior</th><th>Observation</th><th>Volunteer</th></tr><?php while($r=$rows->fetch_assoc()):?><tr><td><?=h($r['name'])?></td><td><?=h($r['feeding_time'])?></td><td><?=h($r['food_type'])?></td><td><?=h($r['quantity'])?></td><td><?=h($r['food_consumed']?:'—')?></td><td><?=h($r['behavior']?:'—')?></td><td><?=h($r['observation']?:'—')?></td><td><?=h($r['full_name'])?></td></tr><?php endwhile;?></table></div>
<?php page_bottom(); ?>
