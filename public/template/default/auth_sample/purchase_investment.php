<?php
$page_title = "Purchase Investment";

use v2\Models\InvestmentPackage;

include_once 'includes/header.php'; ?>


<?php include_once 'includes/sidebar.php'; ?>




<div class="content-w">
    <?php include_once 'includes/topbar.php'; ?>


    <!-- <div class="content-panel-toggler"><i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span></div> -->
    <div class="content-i">

        <div class="content-box" style="">

                    <div class="element-wrapper">
                      
                        <h6 class="element-header">Purchase Investment</h6>
                        <div class="row">

                            <?php foreach (InvestmentPackage::available()->get() as  $investment) : ?>



                                <div class="pricing-plan col-md-6 with-hover-effect element-box">
                                    <div class="plan-head element-box">
                                        <!-- <div class="plan-image"><img alt="" src="img/plan1.png"></div> -->
                                        <div class="plan-name"> <?= $investment->name; ?></div>
                                    </div>
                                    <div class="plan-body  element-box">
                                        <div class="plan-price-w">
                                            <div class="price-value"><?= $currency; ?><?= MIS::money_format($investment->DetailsArray['min_capital']); ?></div>
                                            <div class="price-label" style="color: gray;"> <?= ($investment->DetailsArray['roi_percent']); ?>% ROI in
                                                <?= ($investment->DetailsArray['maturity_in_days']); ?>days</div>
                                        </div>

                                        <?= MIS::generate_form(['pack_id' => $investment->id], domain . "/user/submit_investment", 'Invest', '', true); ?>
                                        <div class="plan-btn-w">
                                            <!-- <a class="btn btn-primary btn-rounded" href="#">Invest</a> -->
                                        </div>
                                    </div>

                                </div>


                            <?php endforeach; ?>


            </div>
            </div>


            <?php include_once 'includes/customiser.php'; ?>

        </div>

        <?php include_once 'includes/quick_links.php'; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>