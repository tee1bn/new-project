<?php
$page_title = "Login";
include 'includes/auth_header.php'; ?>

<div class="media align-items-stretch justify-content-center ht-100p pos-relative">
  <!--   <div class="media-body align-items-center d-none d-lg-flex">
    <div class="mx-wd-600">
      <img src="<?= asset; ?>/guest/assets/img/img15.png" class="img-fluid" alt="">
    </div>

  </div>
 -->
  <div class="sign-wrapper mg-lg-l-50 mg-xl-l-60">
    <div class="wd-100p">
      <h3 class="tx-color-01 mg-b-5">Sign In</h3>
      <p class="tx-color-03 tx-16 mg-b-40">Welcome back! Please signin to continue.</p>

      <form data-toggle="validator" class="form-horizontal form-simple" id="loginform" action="<?= domain; ?>/login/authenticate" method="post">

        <?= $this->csrf_field(); ?>

        <?php if (@$this->inputError('user_login') != '') : ?>
          <center class="alert alert-danger">
            <?= $this->inputError('user_login'); ?>
          </center>
        <?php endif; ?>


        <div class="form-group">
          <label>Email address</label>
          <input type="text" class="form-control" name="user" value="<?= Input::old('user') ?? ''; ?>" required="" placeholder="yourname@yourmail.com">
        </div>
        <div class="form-group">
          <div class="d-flex justify-content-between mg-b-5">
            <label class="mg-b-0-f">Password</label>
          </div>
          <input type="password" required="" name="password" class="form-control" placeholder="Enter your password">
        </div>
        <!--  <div class="form-group">
          <div class="g-recaptcha form-group" data-sitekey="<?= '' //SiteSettings::site_settings()['google_re_captcha_site_key']; 
                                                            ?>">
          </div>
        </div> -->
        <div class="form-group">
          <div class="d-flex justify-content-between mg-b-5">

            <label class="mg-b-0-f">
              <input type="checkbox" name="remember_me" checked value="1"> Keep me signed in</label>
            <a href="<?= domain; ?>/forgot-password" class="tx-13">Forgot password?</a>
          </div>
        </div>
        <button class="btn btn-brand-02 btn-block">Sign In</button>

        <div class="tx-13 mg-t-20 tx-center">Don't have an account? <a href="<?= domain; ?>/register">Create an Account</a></div>
      </form>

    </div>
  </div><!-- sign-wrapper -->
</div><!-- media -->

<?php include 'includes/auth_footer.php';
