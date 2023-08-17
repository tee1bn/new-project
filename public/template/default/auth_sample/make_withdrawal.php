<?php
$page_title = "Dashboard";



use v2\Models\Withdrawal;

$balances = Withdrawal::payoutBalanceFor($auth->id);

include_once 'includes/header.php'; ?>


<?php include_once 'includes/sidebar.php'; ?>




<div class="content-w">
    <?php include_once 'includes/topbar.php'; ?>


    <!-- <div class="content-panel-toggler"><i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span></div> -->
    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="element-wrapper">
                        <div class="element-actions">

                        </div>
                        <h6 class="element-header">Make Withdrawal</h6>
                        <div class="element-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row pt-2">
                                        <div class="col-md-6 "><a class="element-box el-tablo centered trend-in-corner smaller" href="#">
                                                <div class="label">Book Balance</div>
                                                <div class="value"> <?= $currency; ?><?= MIS::money_format($balances['payout_book_balance']); ?></div>
                                                <!-- <div class="trending trending-up"><span>12%</span><i class="os-icon os-icon-arrow-up6"></i></div> -->
                                            </a></div>

                                        <div class="col-md-6 "><a class="element-box el-tablo centered trend-in-corner smaller" href="#">
                                                <div class="label">Available Balance</div>
                                                <div class="value"> <?= $currency; ?><?= MIS::money_format($balances['payout_balance']); ?></div>
                                                <!-- <div class="trending trending-up"><span>12%</span><i class="os-icon os-icon-arrow-up6"></i></div> -->
                                            </a></div>


                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card element-box">
                                        <div class="card-header" data-toggle="collapse" data-target="#make_deposit">
                                            <!-- <h1 href="javascript:void;" class="card-title"> Information</h1> -->
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                                </ul>
                                            </div>

                                        </div>
                                        <div class="card-body collapse show" id="make_deposit">
                                            <div class="col-12">
                                                <small>Withdrawal Fee: <?= $balances['withdrawal_fee']; ?>% </small><br>
                                                <small>Minimum Withdrawal: <?= $currency; ?><?= MIS::money_format($balances['min_withdrawal']); ?></small><br>
                                                <hr>
                                            </div>
                                            <?php if ($balances['available_payout_balance'] > 0) : ?>

                                                <form class="col-12 ajax_for" method="POST" action="<?= domain; ?>/withrawals/submit_withdrawal_request">


                                                    <?= $this->csrf_field(); ?>

                                                    <div class="form-group">
                                                        <label>Amount (<?= $currency; ?>)</label>
                                                        <input type="number" step="1" min="<?= $balances['min_withdrawal']; ?>" name="amount" required="" class="form-control">
                                                    </div>


                                                    <div class="form-group">
                                                        <label>Select Wallet</label><small><a href="<?=domain;?>/user/my-wallet"> Create Wallet </a></small>
                                                        <select class="form-control" required="" name="method">
                                                            <option value="">Select Payment method</option>
                                                            <?php foreach (v2\Models\UserWithdrawalMethod::ForUser($auth->id)->get() as $key => $option) : ?>
                                                                <option value="<?= $option->id; ?>"><?= v2\Models\UserWithdrawalMethod::$method_options[$option['method']]['name']; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>


                                                    <!-- <?= $this->use_2fa_protection(); ?> -->


                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                                                    </div>

                                                </form>
                                            <?php else : ?>

                                                <div class="col-12">

                                                    <center>
                                                        You need <?= $currency; ?><?= MIS::money_format($balances['min_withdrawal']); ?> at least to be able to request a withdrawal.
                                                    </center>
                                                </div>


                                            <?php endif; ?>

                                        </div>

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <?php include_once 'includes/customiser.php'; ?>

        </div>

        <?php include_once 'includes/quick_links.php'; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>