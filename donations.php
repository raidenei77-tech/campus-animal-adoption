<?php
require 'auth.php'; require_login(); require 'db.php'; require 'layout.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    if(isset($_POST['update_status'])){
        require_volunteer();
        $did=(int)$_POST['update_status']; $status=$_POST['status']??'';
        if(!in_array($status,['pending','received','cancelled'],true)) redirect_with_message('donations.php','Invalid donation status.');
        $s=$conn->prepare('UPDATE donations SET status=? WHERE donation_id=?');$s->bind_param('si',$status,$did);$s->execute();
        redirect_with_message('donations.php','Donation status updated.');
    }

    $amount=(float)($_POST['amount']??0);$type=$_POST['donation_type']??'money';$purpose=trim($_POST['purpose']??'');$items=trim($_POST['item_details']??'');$uid=(int)$_SESSION['user_id'];
    if(!in_array($type,['money','food','medicine','supplies','other'],true)) redirect_with_message('donations.php','Invalid donation type.');
    if($type==='money' && $amount<=0) redirect_with_message('donations.php','Enter a positive amount for a money donation.');
    if($type!=='money' && $items==='') redirect_with_message('donations.php','Describe the donated item(s).');
    if($amount<0) $amount=0;

    $conn->begin_transaction();
    try{
        $s=$conn->prepare("INSERT INTO donations(donor_id,status,amount,donation_date,user_id) VALUES(?,'received',?,CURDATE(),?)");$s->bind_param('idi',$uid,$amount,$uid);$s->execute();$did=$conn->insert_id;
        $d=$conn->prepare('INSERT INTO donation_details(donation_id,donation_type,purpose,item_details) VALUES(?,?,?,?)');$d->bind_param('isss',$did,$type,$purpose,$items);$d->execute();
        $conn->commit();redirect_with_message('donations.php','Thank you! Donation recorded.');
    }catch(Throwable $e){$conn->rollback();throw $e;}
}

$where=is_volunteer()?'1=1':'d.donor_id='.(int)$_SESSION['user_id'];
$rows=$conn->query("SELECT d.*,u.full_name,dd.donation_type,dd.purpose,dd.item_details FROM donations d JOIN users u ON d.donor_id=u.user_id LEFT JOIN donation_details dd ON d.donation_id=dd.donation_id WHERE $where ORDER BY d.donation_id DESC");
$tot=$conn->query("SELECT COALESCE(SUM(amount),0) total FROM donations WHERE status='received'")->fetch_assoc()['total'];
page_top('Donations');
?>
<div class="section-head"><h1 class="page-title">Donations</h1><span class="tag">Money received: ৳<?=number_format((float)$tot,2)?></span></div>
<form class="form-card form-grid" method="post"><?=csrf_field()?>
<select name="donation_type"><option value="money">Money</option><option value="food">Food</option><option value="medicine">Medicine</option><option value="supplies">Supplies</option><option value="other">Other</option></select>
<input type="number" step="0.01" min="0" name="amount" placeholder="Amount (money donation)"><input class="full" name="purpose" placeholder="Purpose"><textarea class="full" name="item_details" placeholder="Item details (required for non-money donations)"></textarea><button class="btn full">Record Donation ❤️</button>
</form>
<div class="section-head"><h2><?=is_volunteer()?'Donation history':'My donations'?></h2></div>
<div class="table-wrap"><table><tr><th>ID</th><th>Donor</th><th>Type</th><th>Amount</th><th>Purpose / Item</th><th>Date</th><th>Status</th><?php if(is_volunteer()):?><th>Action</th><?php endif;?></tr>
<?php while($r=$rows->fetch_assoc()):?><tr><td>#<?=$r['donation_id']?></td><td><?=h($r['full_name'])?></td><td><?=h($r['donation_type']??'money')?></td><td>৳<?=number_format((float)$r['amount'],2)?></td><td><?=h(trim(($r['purpose']??'').' '.($r['item_details']??''))?:'—')?></td><td><?=h($r['donation_date'])?></td><td><span class="tag"><?=h($r['status'])?></span></td><?php if(is_volunteer()):?><td><form method="post" class="actions"><?=csrf_field()?><input type="hidden" name="update_status" value="<?=$r['donation_id']?>"><select name="status"><option value="pending" <?=$r['status']==='pending'?'selected':''?>>Pending</option><option value="received" <?=$r['status']==='received'?'selected':''?>>Received</option><option value="cancelled" <?=$r['status']==='cancelled'?'selected':''?>>Cancelled</option></select><button class="btn small">Save</button></form></td><?php endif;?></tr><?php endwhile;?>
</table></div>
<?php page_bottom(); ?>
