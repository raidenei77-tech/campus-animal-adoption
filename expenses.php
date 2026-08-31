<?php
require 'auth.php'; require_volunteer(); require 'db.php'; require 'layout.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $amount=(float)($_POST['amount']??0);$type=trim($_POST['expense_type']??'');$desc=trim($_POST['description']??'');$animalRaw=(int)($_POST['animal_id']??0);$animal=$animalRaw?:null;$date=$_POST['expense_date']??'';$uid=(int)$_SESSION['user_id'];$don=(int)($_POST['donation_id']??0);
    if($amount<=0||$type===''||$desc===''||!valid_date($date)) redirect_with_message('expenses.php','Enter valid expense information.');
    if($don){$c=$conn->prepare("SELECT donation_id FROM donations WHERE donation_id=? AND status='received'");$c->bind_param('i',$don);$c->execute();if(!$c->get_result()->fetch_assoc()) redirect_with_message('expenses.php','The selected donation is not available for use.');}
    $conn->begin_transaction();
    try{$s=$conn->prepare('INSERT INTO expenses(paid_by,animal_id,expense_date,amount,description,expense_type) VALUES(?,?,?,?,?,?)');$s->bind_param('iisdss',$uid,$animal,$date,$amount,$desc,$type);$s->execute();$eid=$conn->insert_id;if($don){$u=$conn->prepare('INSERT INTO donation_usage(expense_id,donation_id) VALUES(?,?)');$u->bind_param('ii',$eid,$don);$u->execute();}$conn->commit();redirect_with_message('expenses.php','Expense recorded.');}catch(Throwable $e){$conn->rollback();throw $e;}
}

$animals=$conn->query('SELECT animal_id,name FROM animal ORDER BY name');
$dons=$conn->query("SELECT d.donation_id,d.amount,COALESCE(SUM(e.amount),0) used_amount FROM donations d LEFT JOIN donation_usage du ON d.donation_id=du.donation_id LEFT JOIN expenses e ON du.expense_id=e.expense_id WHERE d.status='received' GROUP BY d.donation_id,d.amount ORDER BY d.donation_id DESC");
$rows=$conn->query('SELECT e.*,u.full_name,a.name animal_name,du.donation_id FROM expenses e JOIN users u ON e.paid_by=u.user_id LEFT JOIN animal a ON e.animal_id=a.animal_id LEFT JOIN donation_usage du ON e.expense_id=du.expense_id ORDER BY e.expense_date DESC,e.expense_id DESC');
$tot=$conn->query('SELECT COALESCE(SUM(amount),0) total FROM expenses')->fetch_assoc()['total'];
page_top('Expenses');
?>
<div class="section-head"><h1 class="page-title">Expenses & Fund Usage</h1><span class="tag">Total spent: ৳<?=number_format((float)$tot,2)?></span></div>
<form class="form-card form-grid" method="post"><?=csrf_field()?>
<input type="date" name="expense_date" value="<?=date('Y-m-d')?>" required><input type="number" step="0.01" min="0.01" name="amount" placeholder="Amount" required><input name="expense_type" placeholder="Expense type" required><select name="animal_id"><option value="">No specific animal</option><?php while($a=$animals->fetch_assoc()):?><option value="<?=$a['animal_id']?>"><?=h($a['name'])?></option><?php endwhile;?></select>
<select name="donation_id"><option value="">Not linked to a donation</option><?php while($d=$dons->fetch_assoc()): $remaining=(float)$d['amount']-(float)$d['used_amount'];?><option value="<?=$d['donation_id']?>">Donation #<?=$d['donation_id']?> (৳<?=number_format((float)$d['amount'],2)?>; remaining approx. ৳<?=number_format($remaining,2)?>)</option><?php endwhile;?></select>
<textarea class="full" name="description" placeholder="Expense description" required></textarea><button class="btn full">Record Expense</button></form>
<div class="section-head"><h2>Expense history</h2></div>
<div class="table-wrap"><table><tr><th>Date</th><th>Type</th><th>Amount</th><th>Animal</th><th>Paid by</th><th>Donation used</th><th>Description</th></tr><?php while($r=$rows->fetch_assoc()):?><tr><td><?=h($r['expense_date'])?></td><td><?=h($r['expense_type'])?></td><td>৳<?=number_format((float)$r['amount'],2)?></td><td><?=h($r['animal_name']??'—')?></td><td><?=h($r['full_name'])?></td><td><?=h($r['donation_id']?'#'.$r['donation_id']:'—')?></td><td><?=h($r['description'])?></td></tr><?php endwhile;?></table></div>
<?php page_bottom(); ?>
