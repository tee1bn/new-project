<?php
$page_title = "Change Password";
include_once 'includes/header.php'; ?>
<?php include_once 'includes/auth_nav.php'; ?>

<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Change Password</h4>
    </div>
    <!--   <div class="d-none d-md-block">
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="upload" class="wd-10 mg-r-5"></i> Export</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="share-2" class="wd-10 mg-r-5"></i> Share</button>
        <button class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="sliders" class="wd-10 mg-r-5"></i> Settings</button>
    </div> -->
</div>

<div class="row row-xs">


    <div class="card col-12">
        <div class="">

            <form method="post" action="<?= domain; ?>/user-profile/change_password" class="ajax_form" style="padding: 10px;">
                <?= @$this->csrf_field('change_password'); ?>

                <!-- 
                <div class="form-group">
                    <input type="password" name="current_password" class="form-control" placeholder="Current Password">
                    <span class="text-danger"><?= @$this->inputError('current_password'); ?></span>
                </div> -->


                <div class="form-group">
                    <label>New password</label>
                    <input type="password" required name="new_password" class="form-control" placeholder="New Password">
                    <span class="text-danger"><?= @$this->inputError('new_password'); ?></span>
                </div>

                <div class="form-group">
                    <label>Confirm new password</label>
                    <input type="password" required name="confirm_password" class="form-control" placeholder="Confirm password">
                    <span class="text-danger"><?= @$this->inputError('confirm_password'); ?></span>
                </div>

                <?= $this->use_2fa_protection(); ?>


                <button type="submit" class="btn btn-outline-primary col-4">Submit</button>
            </form>




        </div>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>