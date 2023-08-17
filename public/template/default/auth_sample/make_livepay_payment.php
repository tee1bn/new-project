<?php
$page_title = "Make  Payment";


$payment_details = $order->PaymentDetailsArray;
$livepay_order_id = $payment_details['approval']['order_id'];
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
                        <h6 class="element-header">Make Payment</h6>
                        <div class="element-content">
                            <div class="row">


                                <center>
                                    <script type="text/javascript" src="https://gw17.livepay.io/gw/paywidget/?orderId=<?= $livepay_order_id; ?>">
                                    </script>
                                </center>

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