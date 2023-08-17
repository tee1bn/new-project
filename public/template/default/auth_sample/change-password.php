<?php
$page_title = "Request Password Reset";
include 'includes/auth_header.php'; ?>


<div class="content content-fixed content-auth-alt">
    <div class="container d-flex justify-content-center ht-100p">
        <div class="mx-wd-300 wd-sm-450 ht-100p d-flex flex-column align-items-center justify-content-center">
            <!-- <div class="wd-80p wd-sm-300 mg-b-15"><img src="<?= asset; ?>/guest/assets/img/img18.png" class="img-fluid" alt=""></div> -->
            <h4 class="tx-20 tx-sm-24">Change your password</h4>
            <p class="tx-color-03 mg-b-30 tx-center">Please set a new password</p>



            <form data-toggle="validator" class="form-horizontal form-simple" id="loginform" action="<?= domain; ?>/forgot-password/reset_password" method="post">

                <?= $this->csrf_field(); ?>

                <div class="form-group">
                    <input type="hidden" class="form-control" id="email" placeholder="Email Address" name="user" value="<?= $_SESSION['change_password_email']; ?>" readonly required>
                </div>



                <div class="form-group">
                    <input type="Password" class="form-control" id="email" placeholder="New Password" name="new_password" value="" required>
                    <small class="pull-left" style="color: red;"> <?= @$this->inputError('new_password'); ?></small>
                </div>



                <div class="form-group">
                    <input type="Password" class="form-control" id="email" placeholder="Confirm New Password" name="confirm_new_password" value="" required>
                    <small class="pull-left" style="color: red;"> <?= @$this->inputError('confirm_new_password'); ?></small>
                </div>

                <div class="form-group">
                    <div class="g-recaptcha form-group" data-sitekey="<?= SiteSettings::site_settings()['google_re_captcha_site_key']; ?>">
                    </div>
                </div>

                <div class="wd-100p d-flex flex-column flex-sm-row mg-b-40">
                    <button class="btn btn-brand-02">Reset Password</button>

                </div>
            </form>
            <span class="tx-12 tx-color-03">Don't have an account? <a href="<?= domain; ?>/register">Sign up</a></span>
            <span class="tx-12 tx-color-03">Or <a href="<?= domain; ?>/login">Log in</a></span>



        </div>

    </div><!-- container -->
</div>























<?php include 'includes/auth_footer.php';
