<?php
$page_title = "My Profile";
include_once 'includes/header.php'; ?>
<?php include_once 'includes/auth_nav.php'; ?>

<div class="content-w">

    <div class="content-i">
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
                    <div class="element-wrapper">
                        <div class="element-actions">

                        </div>
                        <!-- <h6 class="element-header">My Profile</h6> -->
                        <div class="element-content">
                            <div class="row">
                                <div class="element-box col-md-12">
                                    <div class="element-info">
                                        <div class="element-info-with-icon">
                                            <div class="element-info-icon">
                                                <div class="os-icon os-icon-wallet-loaded"></div>
                                            </div>
                                            <div class="element-info-text">
                                                <h5 class="element-inner-header">Profile Settings</h5>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="profile_form" class="ajax_form" action="<?= domain; ?>/user-profile/update_profile" method="post">
                                        <div class="form-group">
                                            <label for="username" class="pull-left">Username *</label>
                                            <input type="text" name="username" disabled="" value="<?= $auth->username; ?>" id="username" class="form-control" value="">
                                        </div>
                                        <?= $this->csrf_field(); ?>
                                        <!-- 
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select <?= User::is_disabled('gender', $auth); ?> class="form-control form-control" name="gender" required="">
                                                <option value="">Select</option>
                                                <?php foreach (User::$genders as $key => $value) : ?>
                                                    <option value="<?= $key; ?>" <?= ($auth->gender == $key) ? 'selected' : ''; ?>><?= $value; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div> -->


                                        <div class="row">
                                            <div class="form-group col-6">
                                                <label for="firstName" class="pull-left">First Name *</label>
                                                <input type="text" name="firstname" <?= User::is_disabled('firstname', $auth); ?> value="<?= $auth->firstname; ?>" id="firstName" class="form-control">
                                            </div>

                                            <div class="form-group col-6">
                                                <label for="lastName" class="pull-left">Last Name <sup>*</sup></label>
                                                <input type="text" name="lastname" id="lastName" <?= User::is_disabled('lastname', $auth); ?> class="form-control" value="<?= $auth->lastname; ?>">
                                            </div>
                                        </div>


                                        <!-- 
                                        <div class="form-group">
                                            <label for="birthdate" class="pull-left">Birth Date <sup>*</sup></label><?= $auth->birthdate; ?>
                                            <input type="date" name="birthdate" id="birthdate" <?= User::is_disabled('birthdate', $auth); ?> class="form-control" value="<?= $auth->birthdate; ?>">
                                        </div> -->

                                        <div class="form-group">
                                            <label for="email" class="pull-left">Email Address<sup>*</sup></label>
                                            <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                                <span class="input-group-btn input-group-prepend"></span>
                                                <input id="tch3" name="email" value="<?= $auth->email; ?>" <?= User::is_disabled('email', $auth); ?> data-bts-button-down-class="btn btn-secondary btn-outline" data-bts-button-up-class="btn btn-secondary btn-outline" class="form-control">
                                                <span class="input-group-btn input-group-append">
                                                    <!-- <button class="btn btn-sm btn-outline-primary bootstrap-touchspin-up" type="button">Require Verification</button> -->
                                                </span>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="phone" class="pull-left">Phone<sup>*</sup></label>
                                            <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                                <span class="input-group-btn input-group-prepend"></span>
                                                <input id="tch3" name="phone" value="<?= $auth->phone; ?>" <?= User::is_disabled('phone', $auth); ?> data-bts-button-down-class="btn btn-secondary btn-outline" data-bts-button-up-class="btn btn-secondary btn-outline" class="form-control">
                                                <span class="input-group-btn input-group-append">
                                                    <!-- <button class="btn btn-sm btn-outline-primary bootstrap-touchspin-up" type="button">Require Verification</button> -->
                                                </span>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <button type="submit" class="btn btn-outline-primary btn-block btn-flat">Update Profile</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
</div>

<?php include_once 'includes/footer.php'; ?>