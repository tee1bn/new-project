<?php
$page_title = "2 Factor Authentication";
include 'includes/auth_header.php'; ?>
<h4 class="auth-header">2 Factor Authentication</h4>


<div class="box-content">
    <div class="card-body">
        <p class="text-center"> <small>Please enter 6 digit code on your Google <br>Authenticator mobile App.</small>

        </p>
        <form data-toggle="validator" class="" id="" action="<?= domain; ?>/login/submit_2fa" method="post">

            <?php if (@$this->inputError('user_login') != '') : ?>
                <center class="alert alert-danger">
                    <?= $this->inputError('user_login'); ?>
                </center>
            <?php endif; ?>

            <div class="form-group">
                <label>2FA Code</label>
                <input type="text" class="form-control" placeholder="Enter 2FA code" name="code">
            </div>


            <button type="submit" class="btn btn-lg btn-block btn-primary">Submit</button>
        </form>
    </div>

    <div class="form-group">
        <p class="text-center">Don't have an account? <a href="<?= domain; ?>/register" class="">Register</a> Or <a href="<?= domain; ?>/login">Login</a></p>
    </div>
</div>

<hr>
<hr>





<?php include 'includes/auth_footer.php';
