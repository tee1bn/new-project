<?php

use v2\Classes\CurrencyArray;


$page_title = "Dashboard";
include_once 'includes/header.php';

$currencies = (new CurrencyArray)->currencies;
$balance_and_sub = $auth->unitBalanceAndSub();
$unit_balance = (int) $balance_and_sub['unit_balance'];
?>

<?php include_once 'includes/auth_nav.php'; ?>

<div>

    <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
            <?php include_once 'includes/breadcrumb.php'; ?>
            <h4 class="mg-b-0 tx-spacing--1">Dashboard</h4>
        </div>
        <?= MIS::generate_form([], "$domain/user/refresh_balance", "Query Payment", null, null); ?>
    </div>



    <?php $this->view('composed/notice_for_rollable_units', [], true, true); ?>


    <div class="row row-xs">
        <div class="col-md-12  col-lg-6 mg-t-10">
            <div class="card card-body">
                <h6 class=" tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-0">UNIT <i class="fa fa-coins"></i>
                    <div style="display: inline;float:right">
                        <span class="tx-medium tx-danger"><?= $balance_and_sub['latest_sub']->NotificationText ?? ''; ?></span>

                    </div>
                </h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <h5 class="tx-rubik mg-b-0 mg-r-5 lh-1"> <?= $unit_balance ?></h5>
                </div>

            </div>
        </div>
        <div class="col-md-12  col-lg-6 mg-t-10">
            <div class="card card-body">
                <h6 class="tx-uppercae tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-0">ACCOUNT PLAN <i class="fa fa-briefcase"></i>
                    <div style="display: inline;float:right">
                        Usage: <?= $auth->subscription == NULL ? 'N/A' :  $auth->subscription->plan_usage ?? 0; ?>
                        <br>
                        <span class="tx-medium tx-danger"><?= $auth->subscription->NotificationText ?? ''; ?></span>
                    </div>
                </h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <a href="<?= domain; ?>/user/pay_selected_plan/<?= $auth->subscription['details']['id']; ?>">

                        <h5 class="tx-rubik mg-b-0 mg-r-5 lh-1"> <?= $auth->subscription['details']['name'] ?? 'No Plan'; ?></h5>
                    </a>
                </div>
            </div>
        </div>


        <div class="col-md-12  col-lg-6 mg-t-10">
            <div class="card card-body" onclick="copy_text('<?= $auth->referral_link(); ?>')">
                <h6 class="tx- tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-0"><i class="fa fa-users"></i> Referrals </small>
                    <div style="display: inline;float:right">
                        Copy referral link <i class="fa fa-link"></i><br>
                        <small class="float-right">Get 2unit when your referrals subscribe.</small>
                    </div>
                </h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <h6 class="tx-rubik mg-b-0 mg-r-5 lh-1">
                        <?= $auth->all_downlines_by_path('enrolment', false, 1)->count(); ?> <small>Users</small>
                    </h6>
                </div>
            </div>
        </div><!-- col -->

        <div class=" col-md-12 col-lg-6 mg-t-10">
            <div class="card card-body">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Email <i class="fa fa-envelope"></i> <?= $auth->emailVerificationStatus; ?>

                </h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <p class="tx-11 tx-color-03 mg-b-0">
                        <?php if ($auth && !$auth->has_verified_email()) : ?>
                            <?= MIS::generate_form([], "$domain/register/verify_email", 'Click to Verify Email'); ?>
                        <?php else : ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>

<div class="mg-t-45 text-center">
    <a href="<?= $domain; ?>/user/account_plan" class="btn-xs btn btn-outline-primary">Click to subscribe</a>
    <a href="<?= $domain; ?>/user/orders" class="btn-xs btn btn-outline-primary">My Recent Orders</a>
</div>

</script>
<?php include_once 'includes/footer.php'; ?>