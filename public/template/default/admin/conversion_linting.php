<?php
$page_title = "Conversion Linting";
include 'includes/header.php'; ?>


<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 ">
                <?php include 'includes/breadcrumb.php'; ?>

                <h3 class="content-header-title mb-0">Conversion Linting</h3>
            </div>

            <div class="content-header-right col-md-6">
                <?= $note; ?>
            </div>
        </div>

        <div class="content-body">



            <section id="video-gallery" class="card">
                <div class="card-header">
                    <h4 class="card-title"></h4>
                    <?php include_once 'template/default/composed/filters/conversion_linting.php'; ?>


                    <div style="display: inline; float:right;">
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                            <a href="javascript:void;" onclick="$confirm_dialog = new DialogJS(process_bulk_action, ['issue'] ,'These will be marked as issued?')" class="btn btn-secondary">Issue</a>
                            <a href="javascript:void;" onclick="$confirm_dialog = new DialogJS(process_bulk_action, ['resolved'] ,'These will be marked as resolved?')" class="btn btn-secondary">Resolve</a>
                            <a class="btn btn-secondary">
                            </a>
                        </div>
                        <input type="checkbox" name="" onclick="toggle_all_records(this)" id="all_records">
                    </div>

                </div>
                <div class="card-content">
                    <div class="card-body table-responsive">

                        <table id="" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#market id</th>
                                    <th>Dump</th>
                                </tr>
                            </thead>
                            <tbody>
                                <form action="<?= domain; ?>/admin/bulk_action_conversion_linting" method="POST" id="bulk_action_form">

                                    <?php foreach ($lintings as $lint) : ?>
                                        <tr>
                                            <td>
                                                <label>
                                                    <input type="checkbox" name="records[]" class="record_selector" value="<?= $lint->id; ?>">
                                                    <?= $lint->market_id; ?><br>
                                                    <?= $lint->dump['bookie_keys']['home_array']['booking_code']; ?> ==> <?= $lint->dump['bookie_keys']['destination_array']['booking_code']; ?>
                                                </label>
                                                <br>
                                                gravity: x<?= $lint->gravity; ?>, last warning:<small class="badge badge-sm badge-primary"><?= date("M.j.y H:i", strtotime($lint->created_at)); ?></small>
                                                <br> <?= $lint->Duration; ?>

                                                <br>
                                                <div class="btn-group btn-group-sm" role="group" aria-label="...">
                                                    <a href="<?= $lint->conversion->link; ?>" class="btn btn-outline-secondary">View Conversion</a>
                                                    <?= $lint->DisplayedStatusActions(false); ?>
                                                </div>


                                            </td>
                                            <td>
                                                <?= $lint->DisplayableStatus; ?>
                                                <b><?= $lint->dump['home_event']; ?></b> <br>
                                                <i class="text-muted"><?= $lint->dump['home']['market']; ?>:<?= $lint->dump['home']['prediction']; ?>@<?= $lint->dump['home']['odd_value']; ?></i>
                                                <br>
                                                <i class="text-muted"><?= ($lint->dump['home']['translated_market']); ?>:
                                                    <?= json_encode($lint->dump['home']['translated_prediction']); ?>
                                                </i>
                                                <br>

                                                <b><?= $lint->dump['destination_event']; ?></b> <br>
                                                <i class="text-muted"><?= $lint->dump['destination']['market']; ?>:<?= $lint->dump['destination']['prediction']; ?>@<?= $lint->dump['destination']['odd_value']; ?></i>
                                                <br>
                                                <i class="text-muted"><?= ($lint->dump['destination']['translated_market']); ?>:
                                                    <?= json_encode($lint->dump['destination']['translated_prediction']); ?>
                                                </i>

                                            </td>

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



            <ul class="pagination">
                <?= $this->pagination_links($data, $per_page); ?>
            </ul>


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



        </div>
    </div>
</div>
<!-- END: Content-->

<?php include 'includes/footer.php'; ?>