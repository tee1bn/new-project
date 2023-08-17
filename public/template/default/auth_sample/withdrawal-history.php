<?php
$page_title = "Withdrawals History";
include_once 'includes/header.php'; ?>


<?php include_once 'includes/sidebar.php'; ?>




<div class="content-w">

    <!-- <div class="content-panel-toggler"><i class="os-icon os-icon-grid-squares-22"></i><span>Sidebar</span></div> -->
    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="element-wrapper">
                        <div class="element-actions">

                        </div>
                        <h4 class="element-header">Withdrawal history</h4>
                        <div class="element-content">

                            <?php if ($withdrawals->isEmpty()) : ?>
                                <div class="text-center">Your withdrawals will show here</div>
                            <?php endif; ?>



                            <table id="myTable" class="table table-stripe" style="display:<?= $withdrawals->isEmpty() ? 'none' : ''; ?>">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Amount(<?= $currency; ?>)</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <!-- <th>*</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($withdrawals as $withdrawal) :
                                        $detail = $withdrawal->ExtraDetailArray;
                                    ?>
                                        <tr>
                                            <td><?= $withdrawal->id; ?></td>
                                            <td><?= $this->money_format($withdrawal['amount']); ?></td>
                                            <td><?= $withdrawal->withdrawal_method->method; ?></td>
                                            <td><?= $withdrawal->DisplayStatus; ?></td>
                                            <td><small class=""><?= date("M j, Y h:ia", strtotime($withdrawal->created_at)); ?></small></td>
                                            <td>

                                                <?php if ($withdrawal->is_pending() && false) : ?>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">Action
                                                        </button>
                                                        <div class="dropdown-menu">


                                                            <a class="dropdown-item" href="javascript:void;" onclick="$confirm_dialog = 
                                  new ConfirmationDialog('<?= domain; ?>/withrawals/user_push/<?= $withdrawal->id; ?>/declined','This will be marked as declined?')">
                                                                <span type='span' class='label label-xs label-primary'>Decline</span>
                                                            </a>

                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </div>

            <ul class="pagination">
                <?= $this->pagination_links($data, $per_page); ?>
            </ul>


            <?php include_once 'includes/customiser.php'; ?>

        </div>

        <?php include_once 'includes/quick_links.php'; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>