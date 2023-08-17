<?php
$page_title = "Orders";
include 'includes/header.php'; ?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 ">
                <?php include 'includes/breadcrumb.php'; ?>

                <h3 class="content-header-title mb-0">Orders</h3>
            </div>

            <div class="content-header-right col-md-6">
                <?= $note; ?>

                <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                    <a class="btn btn-outline-primary" href="Javascript:void(0);">
                        Total:<?= $total_amount; ?>
                    </a>
                    <a class="btn btn-outline-primary" href="Javascript:void(0);">
                        Sub Total :<?= $shown_total_amount; ?>
                    </a>
                </div>

            </div>
        </div>

        <div class="content-body">



            <section id="video-gallery" class="card">
                <div class="card-header">
                    <h4 class="card-title"></h4>
                    <?php include_once 'template/default/composed/filters/products_orders.php'; ?>

                </div>
                <div class="card-content">
                    <div class="card-body table-responsive">

                        <table id="" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#Ref</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order) : ?>
                                    <tr>
                                        <td><?= $order->TransactionID; ?></td>
                                        <td><?= $order->Buyer->DropSelfLink; ?></td>
                                        <td>
                                            <pre>
                                                    <?php echo json_encode(json_decode($order->buyer_order, true), JSON_PRETTY_PRINT); ?>
                                            </pre>
                                        </td>
                                        <td><span class="badge badge-primary"><?= date("M j, Y h:iA", strtotime($order->created_at)); ?></span></td>
                                        <td><?= $order->payment; ?></td>
                                        <td>

                                            <div class="dropdown">
                                                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                                                </button>
                                                <div class="dropdown-menu">

                                                    <?php if (!$order->is_paid()) : ?>
                                                        <a href="javascript:void(0);" class="dropdown-item" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain; ?>/admin-products/mark_as_complete/<?= $order->id; ?>')"> Mark Paid</a>
                                                    <?php endif; ?>

                                                    <a href="javascript:void(0);" class="dropdown-item" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain; ?>/admin-products/mark_as_uncomplete/<?= $order->id; ?>')"> Mark Pending</a>


                                                    <?php if ($order->payment_proof != null) : ?>
                                                        <a class="dropdown-item" target="_blank" href="<?= domain; ?>/<?= $order->payment_proof; ?>">See Proof</a>
                                                    <?php endif; ?>

                                                    <?php if (!$order->is_paid()) : ?>
                                                        <form id="fo<?= $order->id; ?>" class="ajax_form" action="<?= $order->reverifyLink; ?>" method="post">
                                                            <button type="submit" class="dropdown-item" class="">
                                                                Query
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <a href="<?= domain; ?>/admin/order/<?= $order->id; ?>" class="dropdown-item">
                                                        Open
                                                    </a>
                                                    <a href="<?= domain; ?>/admin/order_invoice/<?= $order->id; ?>" class="dropdown-item">
                                                        Invoice
                                                    </a>


                                                    <!--   
                                                    <a href="<?= $order->after_payment_url(true); ?>" target="_blank" class="dropdown-item">
                                                        Delivery page
                                                    </a> -->

                                                    <form id="payment_proof_form<?= $order->id; ?>" action="<?= domain; ?>/user/upload_payment_proof/<?= $order->id; ?>" method="post" enctype="multipart/form-data">
                                                        <input style="display: none" type="file" onchange="document.getElementById('payment_proof_form<?= $order->id; ?>').submit();" id="payment_proof_input<?= $order->id; ?>" name="payment_proof">

                                                        <input type="hidden" name="order_id" value="<?= $order->id; ?>">
                                                    </form>
                                                </div>
                                            </div>


                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>
                        </table>



                    </div>
                </div>
            </section>



            <ul class="pagination">
                <?= $this->pagination_links($data, $per_page); ?>
            </ul>


        </div>
    </div>
</div>
<!-- END: Content-->

<?php include 'includes/footer.php'; ?>