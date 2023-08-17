<?php
$page_title = "Affiliate Application ";
include_once 'includes/header.php'; ?>
<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Affiliate Application</h4>
        <span>Become an affiliate, recommend and earn commission.</span>
    </div>
</div>

<div class="mb-5">
    <div class="row row-xs">
        <div class="card col-12">
            <div class="card-body">
                <form action="<?= domain; ?>/user/submit_affiliate_application" method="post">

                    <div class="form-group">
                        <label>Select region & payout currency*</label>
                        <select name="currency" class="form-control" required>
                            <option value="">Select a currency</option>
                            <option value="ngn">NGN</option>
                            <?php if (in_array($auth->id, [4, 2,  19049, 12756])) : ?>
                                <option value="ghs">GHS</option>
                            <?php endif; ?>
                        </select>
                        <small class="text-danger">what your referrals will be mostly paying, from which you get commission.</small>

                    </div>
                    <div class="form-group">
                        <label>
                            <input name="agreed_to_terms" value="1" type="checkbox" required> I have read and agree to the
                            <a href="javacript:void(0);" data-toggle="modal" data-target="#affiliate_agreement">Affiliate agreement</a>
                        </label>
                    </div>

                    <div class=" alert alert-danger alert-dismissible">
                        <strong>Notice!</strong>
                        <span>You will not be able to change filled information. kindly double check to be sure.</span>
                    </div>

                    <div class="form-group">
                        <button type="submit" id="submit_btn" class="btn btn-outline-primary" style="display:none;">Submit Application</button>
                        <button type="button" class="btn btn-outline-primary" onclick="$confirm_dialog = new DialogJS(submit_form, [this])">Submit Application</button>
                    </div>


                </form>
            </div>


        </div>

    </div>
</div>

<div class="modal " id="affiliate_agreement">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Affiliate Agreement</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div style="overflow-y:scroll;max-height:500px;" class="card-body">
                    <?= CMS::fetch('cbc_affiliate_agreement'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    submit_form = function(btn, event) {
        var $form = btn.form;
        if (!$form[0].checkValidity()) {
            $($form).find(':submit').click();
            return;
        }
        $($form).find(':submit').click();
    }
</script>


<?php include_once 'includes/footer.php'; ?>