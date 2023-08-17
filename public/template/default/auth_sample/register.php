<?php
$page_title = "Register";
include 'includes/auth_header.php'; ?>

<div class="media align-items-stretch justify-content ht-100p row">
	<div class="sign-wrapper mg-lg-r-50 mg-xl-r-60 col-md-6">
		<div class="pd-t-20 wd-100p">
			<h4 class="tx-color-01 mg-b-5">Sign up
				<?php if (isset($_COOKIE['referral'])) : ?>
					<i><small class="float-right text-muted" onclick="show_notification('You have been invited by @<?= $_COOKIE['referral'] ?? ''; ?>','dark')"><i class="fa fa-user"></i>@<?= $_COOKIE['referral'] ?? ''; ?></small></i>
				<?php endif; ?>
			</h4>
			<p class="tx-color-03 tx-16 mg-b-20">
				It only takes a minute.
				<span class="d-md-block  d-lg-none">
					<br><a href="#advantages">See benefits of registration</a>
				</span>

			</p>

			<form data-toggle="validator" class="form-horizontal form-simple" id="loginform" action="<?= domain; ?>/register/register" method="post">

				<?= $this->csrf_field('user_registration'); ?>


				<div class="form-group">
					<label>Username</label>
					<input type="" required="" class="form-control " value="<?= Input::old('username') ?? ''; ?>" name="username" placeholder="User Name">
					<span class="text-danger"><?= Input::inputError('username') ?? ''; ?></span>
				</div>

				<div class="form-group">
					<label>Email address <span class="text-danger"><small>*will be verified</small></span></label>
					<input type="" required="" class="form-control " value="<?= Input::old('email') ?? ''; ?>" name="email" placeholder="mclury@gmail.com">
					<span class="text-danger"><?= Input::inputError('email') ?? ''; ?></span>
				</div>

				<div class="form-group">
					<label>Phone</label>
					<input type="" required="" class="form-control " value="<?= Input::old('phone') ?? ''; ?>" name="phone" placeholder="e.g +1 812345678 (add country code)">
					<span class="text-danger"><?= Input::inputError('phone') ?? ''; ?></span>
				</div>

				<div class="row">
					<div class="form-group col-md-6">
						<label>Firstname</label>
						<input type="text" class="form-control" placeholder="Enter your firstname" name="firstname" value="<?= Input::old('firstname') ?? ''; ?>">
						<span class="text-danger"><?= Input::inputError('firstname') ?? ''; ?></span>
					</div>
					<div class="form-group col-md-6">
						<label>Lastname</label>
						<input type="text" class="form-control" placeholder="Enter your lastname" name="lastname" value="<?= Input::old('lastname') ?? ''; ?>">
						<span class="text-danger"><?= Input::inputError('lastname') ?? ''; ?></span>
					</div>
				</div>




				<?php

				if (isset($_COOKIE['referral'])) {
					$introduced_by = $_COOKIE['referral'];
					$readonly   = "readonly='readonly'";
				} else {

					$introduced_by = Input::old('introduced_by');
					$readonly   = "";
				}; ?>



				<div class="form-group" style="display:none;">
					<label>Sponsor</label>
					<input type="text" class="form-control " value="<?= $introduced_by; ?>" name="introduced_by" placeholder="Sponsor">
					<div class="pre-icon os-icon os-icon-hierarchy-structure-2"></div>
					<span class="text-danger"><?= Input::inputError('introduced_by') ?? ''; ?></span>
				</div>



				<div class="form-group">
					<div class="d-flex justify-content-between mg-b-5">
						<label class="mg-b-0-f">Password</label>
					</div>
					<input type="password" class="form-control" name="password" placeholder="Enter your password">
				</div>
				<div class="form-group tx-12">
					By clicking <strong>Create an account</strong> below, you agree to our
					<a href="<?= domain; ?>/pg/terms-of-service" target="_blank">terms of service</a> and
					<a href="<?= domain; ?>/pg/privacy-policy" target="_blank">privacy statement.</a>
				</div><!-- form-group -->


				<div class="form-group ">
					<div class="g-recaptcha form-group" data-sitekey="<?= SiteSettings::site_settings()['google_re_captcha_site_key']; ?>"></div>
				</div>

				<button class="btn btn-brand-02 btn-block">Create Account</button>
			</form>
			<!-- <div class="divider-text">or</div>
					<button class="btn btn-outline-facebook btn-block">Sign Up With Facebook</button>
					<button class="btn btn-outline-twitter btn-block">Sign Up With Twitter</button> -->


			<div class="tx-13 mg-t-20 tx-center">Already have an account? <a href="<?= domain; ?>/login">Sign In</a></div>
		</div>
	</div><!-- sign-wrapper -->

	<div class="media-body pd-y-30 pd-lg-x-50 pd-xl-x-60 col-md-6 align-items pos-relative" id="advantages">

		<h4 id="section1" class="mg-b-10">Join other bettors!</h4>
		<p class="mg-b-30 tx-16">Your reliable, fast, and robust online bet converter
			with the most coverage. As a Bettor doing your best, we believe nothing should hold you back.</p>

		<ul class="pd-l-20 mg-0 mg-t-16 tx-16 list-disc">
			<li>Cover more bookies.</li>
			<li>Cover more sports asides from football (soccer).</li>
			<li>Cover more prediction markets.</li>
			<li>Convert long-list booking of (30 - 100)events and more.</li>
			<li><b>See exempted lines from conversion result, so you can track your conversion.</b></li>
			<li>API Access & Embed Access.</li>
			<li>Access to conversion statistics.</li>
			<li>Access to affiliate & bonuses.</li>
			<li>Access to winning communities.</li>
			<li>Be limitless with winning opportunities.</li>
			<br>

		</ul>
		<!-- <div class="mx-lg-wd-500 mx-xl-wd-550">
			<img src="<?= asset; ?>/guest/assets/img/img16.png" class="img-fluid" alt="">
		</div>
		<div class="pos-absolute b-0 r-0 tx-12">
		</div> -->
	</div>
</div><!-- media -->
<?php include 'includes/auth_footer.php'; ?>