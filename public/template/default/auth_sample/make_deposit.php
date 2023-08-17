<?php
$page_title = "Make Deposit";



$rules_settings =  SiteSettings::find_criteria('rules_settings');
$min_deposit = $rules_settings->settingsArray['min_deposit_usd'];

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
                        <div class="element-actions float-right">
                            <a class="btn btn-primary btn-md" href="#">
                                Current Bal:<span> <?= $currency; ?><?= MIS::money_format($deposit_balance); ?></span>
                            </a>
                            <a class="btn btn-success btn-md" href="#" data-toggle="modal" data-target="#myModal">
                                <i class="os-icon os-icon-ui-22"></i><span>Make Deposit</span>
                            </a>
                        </div>

                        <div id="myModal" class="modal fade" role="dialog" data-backdrop="static">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Deposit</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">

                                        <form class="col-12" method="POST" action="<?= domain; ?>/user/submit_make_deposit" data-function="initiate_payment">
                                            <?= $this->csrf_field(); ?>
                                            <small>Minimum Deposit: <?= $currency; ?><?= MIS::money_format($min_deposit); ?></small><br>
                                            <hr />

                                            <div class="form-group">
                                                <label>Amount (<?= $currency; ?>)</label>
                                                <input type="number" step="1" min="<?= $min_deposit; ?>" name="amount" required="" class="form-control">
                                            </div>


                                            <div class="form-group">
                                                <label>Select Processor</label>
                                                <select class="form-control" required="" name="payment_method">
                                                    <option value="">Select Payment method</option>
                                                    <?php foreach ($shop->get_available_payment_methods() as $key => $option) : ?>
                                                        <option value="<?= $key; ?>"><?= $option['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-outline-primary">Deposit</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>

                            </div>
                        </div>



                        <h6 class="element-header">Deposit</h6>
                        <div class="element-content">
                            <div class="row">

                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="card">

                                        <div class="card-header" data-toggle="collapse" data-target="#deposit_history">
                                            <div class="heading-elements">
                                                <ul class="list-inline mb-0">
                                                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                                </ul>
                                            </div>

                                        </div>
                                        <div class="card-body collapse show" id="deposit_history">

                                        <?php if ($deposits->isEmpty()) :?>
                                            <center>Your Deposits will show here</center>
                                        <?php endif ;?>

                                            <table id="myTable" class="table table-stripe" style="display:<?= $deposits->isEmpty() ?'none' :'';?>">
                                                <thead>
                                                    <tr>
                                                        <th>#ID</th>
                                                        <th>Amount(<?= $currency; ?>)</th>
                                                        <th>Order ID</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($deposits as $deposit_order) :
                                                        $detail = $deposit_order->ExtraDetailArray;
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?= $deposit_order->checkoutUrl; ?>"><?= $deposit_order->TransactionID()  ?></a></td>
                                                            <td><?= $this->money_format($deposit_order['amount']); ?></td>
                                                            <td><?= $deposit_order->paymentMethod();?></td>
                                                            <td><?= $deposit_order->DepositPaymentStatus; ?></td>
                                                            <td><small ><?= date("M j, Y h:ia", strtotime($deposit_order->created_at)); ?></small></td>

                                                        </tr>
                                                    <?php endforeach; ?>

                                                </tbody>

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