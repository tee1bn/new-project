<?php
$page_title = "Make  Payment";

$payment_details = $order->PaymentDetailsArray;

$manual = SiteSettings::find_criteria('manual_transfer')->settingsArray;
$manual_detail = $manual[$manual['mode']['mode']];


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
                            <div class="row element-box">
                                <div class="col-md-6 mb-4">

                                    <div class="invoice-headin"><h4>Deposit #<small><?=$order->TransactionID();?></small></h4>
                                        <div class="invoice-date">
                                            <?=date("d F Y h:iA", strtotime($order->created_at));?>
                                        </div>
                                    </div>

                                    <br>
                                    <h5>Instruction</h5>
                                      
                                      <p>Please pay <?=$currency;?><?=$order->amount;?> into either:</p>

                                      <?php if (isset($manual_detail['btc_address'])) :?>
                                      <p><strong onclick="copy_text('<?=$manual_detail['btc_address'];?>')">Bitcoin  Address: <?=$manual_detail['btc_address'];?><i class="fa fa-clipboard"></i></strong></p>
                                      <?php endif  ;?>

                                      <?php if (isset($manual_detail['eth_address'])) :?>
                                      <p><strong onclick="copy_text('<?=$manual_detail['eth_address'];?>')">Ethereum  Address: <?=$manual_detail['eth_address'];?> </strong></p>

                                      <?php endif  ;?>
                                      <p>
                                          Then, submit the hash rate so your payment can be confirmed and your account funded.
                                      </p>


                                    <form action="<?=domain;?>/user/submit_hash_rate/<?=$order->id;?>" method="post">
                                        <div class="form-group">
                                            <input required="" value="<?=$order->HashRate;?>" name="hash_rate" class="form-control" placeholder="Enter hash rate here">
                                            <input type="hidden" name="order_id" value="<?=$order->id;?>">
                                        </div>
                                        <button class="btn btn-primary">Submit hash rate</button>
                                    </form>
                                </div>
                                <div class="col-md-5 offset-md-1">
                                    <h3 class="order-section-heading"><?=$currency;?><?=MIS::money_format($order->amount);?></h3>
                                    <div class="order-summary-row">
                                        <div class="order-summary-label"><span>Subtotal</span></div>
                                        <div class="order-summary-value"><?=$currency;?><?=$order->amount;?></div>
                                    </div>
                                    <div class="order-summary-row">
                                        <div class="order-summary-label"><span>Processing </span><strong>Fee</strong></div>
                                        <div class="order-summary-value"><?=$currency;?>0.00</div>
                                    </div>
                                   
                                    <div class="order-summary-row as-total">
                                        <div class="order-summary-label"><span>Total</span></div>
                                        <div class="order-summary-value"><?=$currency;?><?=$order->amount;?></div>
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