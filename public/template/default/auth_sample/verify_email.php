<?php
$page_title = "Email Verification";
include 'includes/auth_header.php'; ?>
<!-- <h4 class="auth-header">Email Verification</h4> -->

<script>
    function send_verification_email() {
        if (window.$) {
            // do your action that depends on jQuery.  
            $.ajax({
                type: "POST",
                url: "<?= domain; ?>/register/verify_email",
                cache: false,
                success: function(response) {
                    window.notify();
                },
                error: function(response) {}
            });

            $("#spiner").html('<i class="fa fa-spinner fa-spin"></i>');
        } else {
            // wait 50 milliseconds and try again.
            window.setTimeout(send_verification_email, 1000);
        }
    }
    send_verification_email();
</script>

<div class="ht-100p d-flex flex-column align-items-center justify-content-center">
    <div class="wd-150 wd-sm-250 mg-b-30"><img src="<?= asset; ?>/guest/assets/img/img17.png" class="img-fluid" alt=""></div>
    <h4 class="tx-20 tx-sm-24">Verify your email address</h4>
    <p class="tx-color-03 mg-b-40">
        Hi <b><?= $auth->fullname; ?></b>, <br>
        Please check your inbox, spam, promotion etc,
        we have sent you a verification email to: <?= "xxx" . substr($auth->email, 3); ?><br>
        Open it and click the confirm button or link, to verify your account.
    </p>
    <div class="tx-13 tx-lg-14 mg-b-40">

        <center>Dint receive the email? click resend verification below.</center>

        <form action="<?= domain; ?>/register/verify_email" method="POST" class="ajax_form">
            <button type="submit" class="btn btn-sm btn-brand-02 d-inline-flex align-items-center">Re/Send Verification</button>
        </form>
        <!--         <a href="#" class="btn btn-brand-02 d-inline-flex align-items-center">Resend Verification</a>
        <a href="#" class="btn btn-white d-inline-flex align-items-center mg-l-5">Contact Support</a>
 -->
    </div>


    <div class="form-group">
        <p class="text-center">Don't have an account? <a href="<?= domain; ?>/register" class="">Register</a> | <a href="<?= domain; ?>/login">Login</a> |
            <a href="<?= domain; ?>/login/logout">Logout</a>
        </p>
    </div>

</div>


<?php include 'includes/auth_footer.php';
