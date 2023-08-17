<?php
$mobile_money = v2\Models\UserWithdrawalMethod::for($auth->id, 'airtel_tigo');

$mobile_money = @$mobile_money->MethodDetails;
?>
<div class="form-group">
    <label>Account Name</label>
    <input type="text" placeholder=" Account Name" value="<?= $mobile_money['account_name'] ?? ''; ?>" name="details[account_name]" required="" class="form-control">
</div>

<div class="form-group">
    <label>Mobile Number</label>
    <input type="text" placeholder=" Mobile number" value="<?= $mobile_money['mobile_number'] ?? ''; ?>" name="details[mobile_number]" required="" class="form-control">

</div>