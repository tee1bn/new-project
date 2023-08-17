<?php
$page_title = "Payout Methods ";
include_once 'includes/header.php'; ?>
<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Payout Methods</h4>
        <span>Enter your preferred payout method.</span>
    </div>
    <div class="">
        <?php include_once 'includes/affiliate_nav.php'; ?>
    </div>
</div>

<div class="mb-5">




    <?php



    $affiliate_settings = $auth->getAffiliateSettings();
    $preferred_currency = $affiliate_settings['settings']['currency'];
    $payout_detail = "{$preferred_currency}_bank";


    foreach (v2\Models\UserWithdrawalMethod::$method_options as $key => $option) :

        if ($key != $payout_detail) {
            continue;
        }
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" data-toggle="collapse" data-target="#make_deposit<?= $option['name']; ?>">
                        <span href="javascript:void;" class="card-title"><?= $option['name']; ?> Information</span>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>

                    </div>
                    <div class="card-body  collapse show" id="make_deposit<?= $option['name']; ?>">

                        <form class="col-12 ajax_for" method="POST" action="<?= domain; ?>/withdrawals/submit_withdrawal_information">

                            <input type="hidden" name="method" value="<?= MIS::dec_enc('encrypt', $key); ?>">

                            <?= $this->csrf_field(); ?>

                            <?php

                            $this->view($option['view'], [], true, true);; ?>

                            <?= $this->use_2fa_protection(); ?>



                            <div class="form-group">
                                <button type="submit" class="btn btn-outline-secondary">Save</button>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>

    <?php endforeach; ?>


</div>









<?php include_once 'includes/footer.php'; ?>