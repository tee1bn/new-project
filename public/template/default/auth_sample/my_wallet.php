<?php
$page_title = "My Wallet";
include_once 'includes/header.php'; ?>


<?php include_once 'includes/sidebar.php'; ?>




<div class="content-w">
    <?php include_once 'includes/topbar.php'; ?>


    <div class="content-panel-toggler"><i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span></div>
    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="element-wrapper">
                        <div class="element-actions">

                        </div>
                        <h6 class="element-header">My Wallet</h6>
                        <div class="element-content">


                            <?php foreach (v2\Models\UserWithdrawalMethod::$method_options as $key => $option) : ?>
                                <div class="row">
                                    <div class="col-12 element-box">
                                        <div class="card">
                                            <div class="card-header" data-toggle="collapse" data-target="#make_deposit<?= $option['name']; ?>">
                                                <h5 class="form-header"><?= $option['name']; ?> Information</h5>

                                                <div class="heading-elements">
                                                    <ul class="list-inline mb-0">
                                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                                    </ul>
                                                </div>

                                            </div>
                                            <div class="card-body  collapse" id="make_deposit<?= $option['name']; ?>">

                                                <form class="col-12 ajax_form" method="POST" action="<?= domain; ?>/withrawals/submit_withdrawal_information">

                                                    <input type="hidden" name="method" value="<?= MIS::dec_enc('encrypt', $key); ?>">

                                                    <?= $this->csrf_field(); ?>

                                                    <?php $this->view($option['view'],[] , null, true ); ?>

                                                    <!-- <?= $this->use_2fa_protection(); ?> -->

                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>

                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>


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