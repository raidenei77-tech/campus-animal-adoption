<?php
require 'auth.php'; require_login(); require 'db.php'; require 'layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (isset($_POST['review'])) {
        require_volunteer();
        $id=(int)$_POST['review']; $decision=$_POST['decision']??'';
        if(!in_array($decision,['approved','rejected'],true)) redirect_with_message('adoptions.php','Invalid review decision.');
        $uid=(int)$_SESSION['user_id'];
        $conn->begin_transaction();
        try {
            $find=$conn->prepare('SELECT animal_id,status FROM adoption_request WHERE adoption_id=? FOR UPDATE');$find->bind_param('i',$id);$find->execute();$req=$find->get_result()->fetch_assoc();
            if(!$req||$req['status']!=='pending'){throw new RuntimeException('Request is no longer pending.');}
            $s=$conn->prepare('UPDATE adoption_request SET status=?,reviewed_by=?,reviewed_at=NOW() WHERE adoption_id=?');$s->bind_param('sii',$decision,$uid,$id);$s->execute();
            if($decision==='approved'){
                $aid=(int)$req['animal_id'];
                $a=$conn->prepare("UPDATE animal SET status='adopted' WHERE animal_id=? AND status<>'adopted'");$a->bind_param('i',$aid);$a->execute();
                $h=$conn->prepare("INSERT INTO animal_status_history(animal_id,status,changed_by) VALUES(?,'adopted',?)");$h->bind_param('ii',$aid,$uid);$h->execute();
                $reject=$conn->prepare("UPDATE adoption_request SET status='rejected',reviewed_by=?,reviewed_at=NOW() WHERE animal_id=? AND adoption_id<>? AND status='pending'");$reject->bind_param('iii',$uid,$aid,$id);$reject->execute();
            }
            $conn->commit(); redirect_with_message('adoptions.php','Adoption request '.$decision.'.');
        } catch(Throwable $e){$conn->rollback();redirect_with_message('adoptions.php',$e->getMessage());}
    }

    $animal=(int)($_POST['animal_id']??0); $food=trim($_POST['food_habit']??''); $home=trim($_POST['home_details']??''); $uid=(int)$_SESSION['user_id'];
    if($animal<=0||$food==='') redirect_with_message('adoptions.php','Choose an animal and describe its food plan.');
    $check=$conn->prepare("SELECT status FROM animal WHERE animal_id=?");$check->bind_param('i',$animal);$check->execute();$animalRow=$check->get_result()->fetch_assoc();
    if(!$animalRow||$animalRow['status']!=='available') redirect_with_message('adoptions.php','This animal is not currently available for adoption.');
    $dup=$conn->prepare("SELECT adoption_id FROM adoption_request WHERE animal_id=? AND adopter_id=? AND status IN ('pending','approved') LIMIT 1");$dup->bind_param('ii',$animal,$uid);$dup->execute();
    if($dup->get_result()->fetch_assoc()) redirect_with_message('adoptions.php','You already have an active request for this animal.');
    $s=$conn->prepare('INSERT INTO adoption_request(animal_id,adopter_id,food_habit,home_details) VALUES(?,?,?,?)');$s->bind_param('iiss',$animal,$uid,$food,$home);$s->execute();
    redirect_with_message('adoptions.php','Adoption request submitted.');
}

$animals=$conn->query("SELECT animal_id,name,species FROM animal WHERE status='available' ORDER BY name");
$where=is_volunteer()?'1=1':'ar.adopter_id='.(int)$_SESSION['user_id'];
$reqs=$conn->query("SELECT ar.*,a.name animal_name,a.species,u.full_name adopter,rv.full_name reviewer FROM adoption_request ar JOIN animal a ON ar.animal_id=a.animal_id JOIN users u ON ar.adopter_id=u.user_id LEFT JOIN users rv ON ar.reviewed_by=rv.user_id WHERE $where ORDER BY ar.adoption_id DESC");
page_top('Adoptions');
?>
<div class="section-head"><h1 class="page-title">Adoption Center</h1></div>
<form class="form-card form-grid" method="post"><?=csrf_field()?>
<select name="animal_id" required><option value="">Choose available animal</option><?php while($a=$animals->fetch_assoc()):?><option value="<?=$a['animal_id']?>" <?=((int)($_GET['animal_id']??0)===$a['animal_id'])?'selected':''?>><?=h($a['name'].' ('.$a['species'].')')?></option><?php endwhile;?></select>
<input name="food_habit" placeholder="How will you feed the animal?" required maxlength="500"><textarea class="full" name="home_details" rows="4" placeholder="Tell us about your home, other pets, and care plan"></textarea><button class="btn full">Request Adoption ❤️</button></form>
<div class="section-head"><h2><?=is_volunteer()?'All adoption requests':'My adoption requests'?></h2></div>
<div class="table-wrap"><table><tr><th>ID</th><th>Animal</th><th>Adopter</th><th>Food habit</th><th>Status</th><th>Reviewed by</th><th>Action</th></tr>
<?php while($r=$reqs->fetch_assoc()):?><tr><td>#<?=$r['adoption_id']?></td><td><?=h($r['animal_name'])?></td><td><?=h($r['adopter'])?></td><td><?=h($r['food_habit'])?></td><td><span class="tag"><?=h($r['status'])?></span></td><td><?=h($r['reviewer']??'—')?></td><td><?php if(is_volunteer()&&$r['status']==='pending'):?><form method="post" class="actions"><?=csrf_field()?><input type="hidden" name="review" value="<?=$r['adoption_id']?>"><button class="btn small" name="decision" value="approved">Approve</button><button class="btn small danger" name="decision" value="rejected">Reject</button></form><?php else:?>—<?php endif;?></td></tr><?php endwhile;?>
</table></div>
<?php page_bottom(); ?>
