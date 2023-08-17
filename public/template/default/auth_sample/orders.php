<?php
$page_title = "Orders";
include_once 'includes/header.php'; ?>

<?php include_once 'includes/auth_nav.php'; ?>

<div class="">
    <div>
        <?php include_once 'includes/breadcrumb.php'; ?>
        <h4 class="mg-b-0 tx-spacing--1">Recent Orders</h4>
        <p><?= $note; ?></p>
    </div>
    <div class="float-rigt">
        <?php include_once 'template/default/composed/filters/users-orders.php'; ?> |
        <a href="<?= domain; ?>/user/account_plan"> Subscribe</a>

    </div>
</div>
<?php include_once 'includes/auth_nav.php'; ?>

<div class="row row-xs">

    <!--     <div class="col-md-12 alert alert-primary alert-dismissible mg-t-10" >
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Note!</strong> To resolve any unpaid order, click the resume button, and follow through to complete the payment.</a>
    </div>
 -->

    <div class="card-body table-responsive">
        <?php if (count($subscription_orders) == 0) : ?>
            <center class="card card-body">No records found</center>
        <?php endif; ?>

        <table class="table table-striped">

            <tbody>
                <?php
                $i = 1;
                foreach ($subscription_orders as $order) :
                    $subscriber = $order->user;
                    $payment_details = $order->PaymentDetailsArray;
                ?>
                    <tr>
                        <td style="border: 1px solid #e8e8e8;padding:0px;">
                            <li class="list-group-item d-flex ">
                                <!-- <small class="badge badge-white"><?= $i; ?></small> -->
                                <div>
                                    <p class="tx-medium mg-b-0">
                                        #<?= $payment_details['ref']; ?>
                                        <br><small class="tx-12 tx-color-03 mg-b-0"><?= date("M j, y", strtotime($order->created_at)); ?></small>
                                        <small><?= $order->ExpiryStatus; ?></small>
                                        <br><small><?= $payment_details['gateway']; ?></small>
                                    </p>
                                </div>
                                <div class="mg-l-auto text-right">
                                    <p class="tx-medium mg-b-0">
                                        <b><?= $order->details['name']; ?>
                                        </b>
                                        <small class="tx-12 tx-success mg-b-0"><?= $order->paymentstatus; ?></small>
                                    </p>
                                    <small class="currency"><?= $payment_details['currency']; ?><?= $order['price'] ?? ''; ?></small>
                                    <br>
                                    <div class="float-right" role="group" aria-label="">
                                        <!-- <a type="a" class="badge text-white badge-secondary" href=" <?= domain; ?>/user/package_invoice/<?= $order->id; ?>">Invoice</a> -->
                                        <?php if (!$order->is_paid()) : ?>

                                            <?php if ($order->resumeLink != '') : ?>

                                                <a type="a" class="badge text-white badge-secondary" href="<?= $order->resumeLink; ?>">Resume</a>
                                            <?php endif; ?>

                                            <form id="fo<?= $order->id; ?>" class="ajax_form" action="<?= $order->reverifyLink; ?>" method="post" style="display: inline;">
                                                <button type="submit" class="badge text-white badge-secondary">Query</button>
                                            </form>
                                        <?php endif; ?>

                                    </div>
                                </div>

                            </li>
                        </td>

                    </tr>
                <?php $i++;
                endforeach; ?>

            </tbody>
        </table>

    </div>
    <ul class="pagination pagination-sm">
        <?= $this->pagination_links($data, $per_page); ?>
    </ul>



</div>
</div><!-- row -->
<?php include_once 'includes/footer.php'; ?>