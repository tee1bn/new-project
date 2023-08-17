<?php
$page_title = "Conversion logs";
include 'includes/header.php'; ?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <?php include 'includes/breadcrumb.php'; ?>
                <h3 class="content-header-title mb-0">Conversion logs</h3>
            </div>

            <div class="content-header-right col-md-6 col-12">

            </div>
        </div>


        <div class="content-body">

            <section id="video-gallery" class="card">
                <div class="card-header">
                    <?php include_once 'template/default/composed/filters/subscription_orders_usage.php'; ?>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    <div class="heading-elements">
                        <small class="float-right"><?= $note; ?> </small>
                    </div>
                </div>
                <div class="card-content table-responsive">
                    <table id="" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#Ref #sub</th>
                                <th>User</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($convertion_logs) > 0) { ?>
                                <?php foreach ($convertion_logs as $log) : ?>
                                    <tr>
                                        <td>
                                            #<?= $log->id; ?>
                                            <a href="<?= MIS::current_url('full'); ?>?bill_id=<?= @$log->subscription->id; ?>"> #<?= @$log->subscription->id; ?></a>
                                            <a href="<?= domain; ?>/admin/membership_orders?id=<?= @$log->subscription->id; ?>"> go to sub</a>
                                            <br>
                                            <span class="badge badge-sm badge-primary">
                                                <?= date("M j, Y h:iA", strtotime($log->created_at)); ?>
                                            </span>
                                            <span class="badge badge-sm badge-dark"><?= ucwords($log->bill_type); ?></span>
                                            <br>
                                            <b><?= $log->conversion_info['from']; ?></b><code><?= $log->conversion_info['code']; ?></code>
                                            ->
                                            <b><?= $log->conversion_info['to']; ?></b><code><?= $log->conversion_info['destination_code']; ?></code>
                                            <br>
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Button group with nested dropdown">
                                                <a class="btn btn-outline-primary" href="Javascript:void(0);">
                                                    use:
                                                </a>
                                                <a class="btn btn-outline-primary" href="Javascript:void(0);">
                                                    bal:
                                                </a>
                                            </div>

                                        </td>
                                        <td>
                                            <?= $log->user->DropSelfLink; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center">No Data found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

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