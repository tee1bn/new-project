<?php
$page_title = "Request Password Reset";
include 'includes/auth_header.php'; ?>


<div class="content content-fixed content-auth-alt">
    <div class="container d-flex justify-content-center ht-100p">
        <div class="mx-wd-300 wd-sm-450 ht-100p d-flex flex-column align-items-center justify-content-center">
            <div class="wd-80p wd-sm-300 mg-b-15"><img src="<?= asset; ?>/guest/assets/img/img18.png" class="img-fluid" alt=""></div>
            <h4 class="tx-20 tx-sm-24">Reset your password</h4>
            <p class="tx-color-03 mg-b-30 tx-center">Enter your email address and we will send you a link to reset your password.</p>
            <form data-toggle="validator" class="form-horizontal form-simple" id="loginform" action="<?= domain; ?>/forgot-password/send_link" method="post">

                <?= $this->csrf_field(); ?>


                <div class="wd-100p d-flex flex-column flex-sm-row mg-b-10">
                    <input type="text" class="form-control" required="" name="user" placeholder="Enter email address">

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
