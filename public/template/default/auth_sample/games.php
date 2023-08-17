<?php
$page_title = "Games";
include_once 'includes/header.php'; ?>

<div>
    <div class="row pd d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div class="col-xs-6">

            <?php include_once 'includes/breadcrumb.php'; ?>

            <h4 class="mg-b-0 tx-spacing--1">Games</h4>
            <small><?= $note; ?></small>
        </div>

        <div class="col-xs-6">
            <?php //include_once 'template/default/composed/filters/orders.php'; ;?>

        </div>
    </div>



    <div class="row">
        <div class="col-md-12" style="padding: 0px;">
            <?php foreach ($orders as $key => $order) : ?>
                <div class="card mg-b-15">
                    <div class="card-header fa-x" data-toggle="collapse" href="#collapseExample<?= $order->id; ?>">
                        <h6 class="lh-5 mg-b-0" style="cursor: pointer;">
                            <a href="javascript:void(0);">
                                <!-- <?= count($order->order_detail); ?> item(s) --> #<?= $order->OrderId; ?>
                                <span class="float-right" style="position: relative;top: 0px;"> <?= date("M j 'y H:i", strtotime($order->paid_at)); ?> <?= $order->payment; ?> </span>
                            </a>
                        </h6>
                    </div>
                    <div class="card-body row collapse show" id="collapseExample<?= $order->id; ?>">
                        <div class="col-md-12"><?= count($order->order_detail); ?> item(s),  <?= $order->ValuePaid; ?></div>
                        <?php foreach ($order->order_detail as $key => $detail) : ?>

                            <div class="col-md-4">
                                <?= $detail['view']; ?>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($orders->isEmpty()) : ?>
                <div class="text-center">
                    <span>Your Games will appear here</span>
                </div>
            <?php endif; ?>
        </div>
    </div><!-- row -->



    <br />
    <br />
    <?php if ($per_page <= $total) : ?>
        <ul class="pagination justify-content-left">
            <?= $this->pagination_links($total, $per_page); ?>
        </ul>
    <?php endif; ?>
</div>
<?php include_once 'includes/footer.php'; ?>