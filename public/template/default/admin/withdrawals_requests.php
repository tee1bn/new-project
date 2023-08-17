<?php
$page_title = 'Withdrawal Requests';
include 'includes/header.php';

use v2\Models\Wallet\ChartOfAccount;

?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-6  mb-2">
                <?php include 'includes/breadcrumb.php'; ?>

                <h3 class="content-header-title mb-0" style="display:inline;">Withdrawal Requests</h3>
            </div>

            <div class="content-header-right col-6  mb-2">
                <?= $note; ?>
            </div>
        </div>
        <div class="content-body">


            <section class="card">
                <div class="card-header">

                    <?php $this->view('composed/filters/journals', compact('sieve')); ?>
                    <h4 class="card-title" style="display: inline;"></h4>

                    <div class="dropdown" style="display: inline; float:right;">
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
                            Bulk Action
                        </button>
                        <div class="dropdown-menu">

                            <a class="dropdown-item" href="javascript:void;" onclick="$confirm_dialog = new DialogJS(process_bulk_action, ['pend'] ,'These will be marked as pending?')">
                                <span type='span' class='label label-xs label-primary'>Pend</span>
                            </a>

                            <a class="dropdown-item" href="javascript:void;" onclick="$confirm_dialog = new DialogJS(process_bulk_action, ['complete'] ,'These will be marked as completed?')">
                                <span type='span' class='label label-xs label-primary'>Complete</span>
                            </a>

                            <a class="dropdown-item" href="javascript:void;" onclick="$confirm_dialog = new DialogJS(process_bulk_action, ['decline'] ,'These will be marked as Declined?')">
                                <span type='span' class='label label-xs label-primary'>Decline</span>
                            </a>


                            <a class="dropdown-item" href="javascript:void;" onclick="$confirm_dialog = new DialogJS(process_bulk_action, ['export_csv'] ,'Export to CSV ?')">
                                <span type='span' class='label label-xs label-primary'>Export to CSV</span>
                            </a>

                        </div>
                        <input type="checkbox" name="" onclick="toggle_all_records(this)" id="all_records">
                    </div>

                </div>
                <div class="card-content">
                    <div class="card-body table-responsive">


                        <table id="charts_of_accounts_table" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>DATE</th>
                                    <th>User</th>
                                    <th>Method</th>
                                    <th style="text-align:right;">AMOUNT($)<br>Fee<br>Payable</th>
                                    <th>NOTES</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <form action="<?= domain; ?>/withrawals/process_bulk_action" method="POST" id="bulk_action_form">

                                <tbody>

                                    <?php foreach ($journals as $journal) : ?>
                                        <tr>
                                            <td><span class="badge badge-dark"><?= date("M d, Y", strtotime($journal->created_at)); ?></span><br>
                                                <?= $journal->id; ?>#<?= $journal->publishedState; ?>
                                            </td>
                                            <td><?= @$journal->user->DropSelfLink ?? "N/A"; ?></td>
                                            <td><?= @$journal->withdrawalDetails ?? "N/A";  ?></td>
                                            <td style="text-align:right;">
                                                <?= @$journal->payablesDetails['amount'] ?? "Nil"; ?><br>
                                                <?= @$journal->payablesDetails['fee'] ?? "0"; ?>
                                                <br><?= @$journal->payablesDetails['payable'] ?? ""; ?>
                                            </td>
                                            <td><?= $journal->notes; ?></td>
                                            <td>

                                                <div class="btn-group btn-group-sm">
                                                    <?php if ($journal->is_editable()) : ?>
                                                        <!-- <a class="btn btn-outline-dark" href="<?= domain ?>/admin/edit_journal/<?= $journal->id; ?>">Edit</a> -->
                                                    <?php endif; ?>
                                                    <!-- <a class="btn btn-outline-dark" href="<?= domain ?>/admin/view_journal/<?= $journal->id; ?>">View</a> -->

                                                    <?php if ($journal->is_pending()) : ?>
                                                        <a class="btn btn-outline-dark" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain ?>/admin/complete_journal/<?= $journal->id; ?>', 
                                                    'Complete withdrawal#<?= $journal->id; ?> ?')">Complete</a>

                                                        <a class="btn btn-outline-dark" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain ?>/admin/decline_journal/<?= $journal->id; ?>', 'Decline journal?')">Decline</a>
                                                    <?php endif; ?>

                                                    <?php if ($journal->is_reversible()) : ?>
                                                        <a class="btn btn-outline-dark" onclick="$confirm_dialog = new ConfirmationDialog('<?= domain ?>/admin/reverse_journal/<?= $journal->id; ?>',
                                                    'Reverse withdrawal#<?= $journal->id; ?> ?')">Reverse</a>
                                                    <?php endif; ?>

                                                </div>



                                            </td>

                                            <td><input type="checkbox" name="records[]" class="record_selector" value="<?= $journal->id; ?>"></td>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>
                                <input type="hidden" name="model" value="withdrawal">
                                <input type="hidden" name="action" value="" id="bulk_action">
                            </form>
                        </table>



                    </div>
                </div>
            </section>

            <script type="text/javascript">
                process_bulk_action = function($action) {
                    $('#bulk_action').val($action);
                    $('#bulk_action_form').submit();


                }


                toggle_all_records = function($all_records) {
                    $selectors = $('.record_selector');
                    for (var i = 0; i < $selectors.length; i++) {
                        $selector = $selectors[i];
                        $selector.checked = $('#all_records')[0].checked;
                    }
                }
            </script>

            <ul class="pagination">
                <?= $this->pagination_links($data, $per_page); ?>
            </ul>
        </div>
    </div>
</div>
<!-- END: Content-->


<div id="new_category_app"></div>

<?php include 'includes/footer.php'; ?>