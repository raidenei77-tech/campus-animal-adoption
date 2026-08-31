<?php
require 'auth.php'; require_login(); require 'db.php'; require 'layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (isset($_POST['take_case'])) {
        require_volunteer();
        $rid=(int)$_POST['take_case']; $uid=(int)$_SESSION['user_id'];
        $conn->begin_transaction();
        try {
            $s=$conn->prepare("UPDATE reports SET handled_by=?,status='in_progress' WHERE report_id=? AND handled_by IS NULL AND status='open'");
            $s->bind_param('ii',$uid,$rid); $s->execute();
            if($s->affected_rows===1){$v=$conn->prepare('UPDATE volunteer_stats SET number_of_cases_handled=number_of_cases_handled+1 WHERE user_id=?');$v->bind_param('i',$uid);$v->execute();}
            $conn->commit();
            redirect_with_message('reports.php',$s->affected_rows===1?'Case assigned to you.':'This case was already assigned.');
        } catch(Throwable $e){$conn->rollback();throw $e;}
    }
    if (isset($_POST['close_case'])) {
        require_volunteer();
        $rid=(int)$_POST['close_case']; $uid=(int)$_SESSION['user_id'];
        $s=$conn->prepare("UPDATE reports SET status='closed' WHERE report_id=? AND handled_by=? AND status<>'closed'");
        $s->bind_param('ii',$rid,$uid); $s->execute();
        redirect_with_message('reports.php',$s->affected_rows?'Case closed.':'Could not close this case.');
    }

    $type=trim($_POST['report_type']??''); $desc=trim($_POST['description']??''); $loc=trim($_POST['location']??'');
    $animalRaw=(int)($_POST['animal_id']??0); $animal=$animalRaw?:null; $uid=(int)$_SESSION['user_id'];
    if($type===''||$desc===''||$loc==='') redirect_with_message('reports.php','Please complete all required report fields.');
    $s=$conn->prepare('INSERT INTO reports(reported_by,animal_id,report_type,description,location) VALUES(?,?,?,?,?)');
    $s->bind_param('iisss',$uid,$animal,$type,$desc,$loc); $s->execute();
    redirect_with_message('reports.php','Report submitted successfully.');
}

$status=$_GET['status']??''; $mine=isset($_GET['mine'])&&$_GET['mine']==='1';
$sql='SELECT r.*,a.name animal_name,u.full_name reporter,v.full_name handler FROM reports r LEFT JOIN animal a ON r.animal_id=a.animal_id JOIN users u ON r.reported_by=u.user_id LEFT JOIN users v ON r.handled_by=v.user_id WHERE 1=1';
$params=[];$types='';
if(in_array($status,['open','in_progress','closed'],true)){$sql.=' AND r.status=?';$params[]=$status;$types.='s';}
if($mine){$sql.=' AND (r.reported_by=? OR r.handled_by=?)';$params[]=(int)$_SESSION['user_id'];$params[]=(int)$_SESSION['user_id'];$types.='ii';}
$sql.=' ORDER BY r.report_id DESC';
$stmt=$conn->prepare($sql); if($params)$stmt->bind_param($types,...$params); $stmt->execute(); $reports=$stmt->get_result();
$animals=$conn->query('SELECT animal_id,name FROM animal ORDER BY name');
page_top('Reports');
?>
<div class="section-head"><h1 class="page-title">Rescue Reports</h1></div>
<form class="form-card form-grid" method="post"><?=csrf_field()?>
<select name="report_type" required><option value="">Report type</option><option>Injured animal</option><option>Stray animal</option><option>Lost animal</option><option>Abuse concern</option><option>Other</option></select>
<select name="animal_id"><option value="">No existing animal</option><?php while($a=$animals->fetch_assoc()):?><option value="<?=$a['animal_id']?>" <?=((int)($_GET['animal_id']??0)===$a['animal_id'])?'selected':''?>><?=h($a['name'])?></option><?php endwhile;?></select>
<input class="full" name="location" placeholder="Location" required maxlength="500"><textarea class="full" name="description" rows="4" placeholder="Describe the situation" required></textarea><button class="btn full">Submit report</button>
</form>
<div class="section-head"><h2>Reports</h2></div>
<form class="filter-bar" method="get"><select name="status"><option value="">All statuses</option><option value="open" <?=$status==='open'?'selected':''?>>Open</option><option value="in_progress" <?=$status==='in_progress'?'selected':''?>>In progress</option><option value="closed" <?=$status==='closed'?'selected':''?>>Closed</option></select><label><input type="checkbox" name="mine" value="1" <?=$mine?'checked':''?>> My reports/cases</label><button class="btn small">Filter</button><a class="btn small secondary" href="reports.php">Reset</a></form>
<div class="table-wrap"><table><tr><th>ID</th><th>Type</th><th>Animal</th><th>Reporter</th><th>Handler</th><th>Status</th><th>Location</th><th>Action</th></tr>
<?php while($r=$reports->fetch_assoc()):?><tr><td>#<?=$r['report_id']?></td><td><?=h($r['report_type'])?></td><td><?=h($r['animal_name']??'—')?></td><td><?=h($r['reporter'])?></td><td><?=h($r['handler']??'Unassigned')?></td><td><span class="tag"><?=h(str_replace('_',' ',$r['status']))?></span></td><td><?=h($r['location'])?></td><td><?php if(is_volunteer()&&!$r['handled_by']&&$r['status']==='open'):?><form method="post"><?=csrf_field()?><button class="btn small" name="take_case" value="<?=$r['report_id']?>">Take case</button></form><?php elseif(is_volunteer()&&(int)$r['handled_by']===(int)$_SESSION['user_id']&&$r['status']!=='closed'):?><form method="post"><?=csrf_field()?><button class="btn small secondary" name="close_case" value="<?=$r['report_id']?>">Close</button></form><?php else:?>—<?php endif;?></td></tr><?php endwhile;?>
</table></div>
<?php page_bottom(); ?>
