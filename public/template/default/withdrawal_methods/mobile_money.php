<?php
$mobile_money = v2\Models\UserWithdrawalMethod::for($auth->id, 'mobile_money');

$mobile_money = @$mobile_money->MethodDetails;
$providers = v2\Models\UserWithdrawalMethod::$method_options['mobile_money']['providers'] ?>

<div class="form-group">
    <label>Mobile Number</label>
    <input type="text" placeholder=" Mobile number" value="<?= $mobile_money['mobile_number'] ?? ''; ?>" name="details[mobile_number]" required="" class="form-control">
</div>


<div class="form-group">
    <label>Network</label>
    <select name="details[provider]" class="form-control" required>

        <option value="">Select</option>
        <?php foreach ($providers as  $provider) : ?>
            <option value="<?= $provider['provider']; ?>" <?= ($provider['provider'] == @$mobile_money['provider']) ? 'selected' : ''; ?>><?= $provider['provider']; ?></option>
        <?php endforeach; ?>

    </select>

</div>