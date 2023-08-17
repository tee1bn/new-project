<?php
$page_title = "Affiliate ";
include_once 'includes/header.php'; ?>
<?php include_once 'includes/auth_nav.php'; ?>

<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Affiliate</h4>
        <!-- <small>The perfect giveaway for punters and their audience.</small> -->
    </div>

    <div class="">
        <?php include_once 'includes/affiliate_nav.php'; ?>
    </div>

</div>


<!-- <div class="alert alert-info">
    <strong>Info!</strong> Commissions will begin to accrue from 1st of Dec. 2022
    <a href="https://t.me/convertbetcodesAffiliates" style="text-decoration:underline;" target="_blank" class="alert-link">Join affiliate community</a>.
</div>
 -->
<div class="mb-5">
    <!-- <h5>Estimated earnings</h5> -->
    <div class="row row-xs">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-body">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Referrals</h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $referred_all_time; ?></h3>
                    <!-- <p class="tx-11 tx-color-03 mg-b-0"> L1:<span class="tx-medium tx-success"> <?= $referred_all_time_l1; ?></span></p> -->
                </div>
            </div>
        </div>


        <!--  <div class="col-sm-6 col-lg-3">
            <div class="card card-body">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Today so far</h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $earnings_today; ?></h3>
                    <p class="tx-11 tx-color-03 mg-b-0"><span class="tx-medium tx-uppercase"> <?= $user_currency; ?></span></p>
                </div>
            </div>
        </div> -->

        <div class="col-sm-6 col-lg-3">
            <div class="card card-body">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">This month</h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $earnings_this_month; ?></h3>
                    <p class="tx-11 tx-color-03 mg-b-0"><span class="tx-medium tx-uppercase"> <?= $user_currency; ?></span></p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-body">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Total Earnings</h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $total_earnings ?? 0; ?></h3>
                    <p class="tx-11 tx-color-03 mg-b-0"><span class="tx-medium tx-uppercase"> <?= $user_currency; ?></span></p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card card-body">
                <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Balance</h6>
                <div class="d-flex d-lg-block d-xl-flex align-items-end">
                    <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $available_balance['account_currency']['available_balance']; ?></h3>
                    <!-- <p class="tx-11 tx-color-03 mg-b-0"><span class="tx-medium tx-">Last pay: <?= $last_payment; ?> <?= strtoupper($user_currency); ?></span></p> -->
                </div>
            </div>
        </div>

    </div>
</div>




<div class="mb-5">
    <h5>Your referral links
        <br><small class="tx-12">These are your referral links, use them to attract clients to our platform.</small>
    </h5>
    <div class="row row-xs">

        <div class="col-12">
            <div class="media align-items-stretch">
                <ul class="nav nav-tabs flex-column wd-150" id="myTab4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab4" data-toggle="tab" href="#new_users" role="tab" aria-controls="home" aria-selected="true">New Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab4" data-toggle="tab" href="#developer_api" role="tab" aria-controls="profile" aria-selected="false">Developer API</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="contact-tab4" data-toggle="tab" href="#widget" role="tab" aria-controls="contact" aria-selected="false">Widget</a>
                    </li>
                    <!--    
                    <li class="nav-item">
                        <a class="nav-link" id="contact-tab4" data-toggle="tab" href="#bet_shop" role="tab" aria-controls="contact" aria-selected="false">Bet shop</a>
                    </li>
                        <li class="nav-item">
                        <a class="nav-link" id="contact-tab4" data-toggle="tab" href="#conversion_link" role="tab" aria-controls="contact" aria-selected="false">Conversion Link</a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" id="contact-tab4" data-toggle="tab" href="#affiliate" role="tab" aria-controls="contact" aria-selected="false">Affiliate</a>
                    </li>
                -->
                    <!--                     <li class="nav-item">
                        <a class="nav-link" id="contact-tab4" data-toggle="tab" href="#html_banners" role="tab" aria-controls="contact" aria-selected="false">HTML banners</a>
                    </li>
 -->
                </ul>
                <div class="media-body">
                    <?php

                    $referral_links = [
                        "new_users" => "{$auth->referral_link()}/register",
                        "developer_api" => "{$auth->referral_link()}/pg/api",
                        "widget" => "{$auth->referral_link()}/pg/applet",
                        "betshop" => "{$auth->referral_link()}/pg/betshop",
                        "conversion_link" => "{$auth->referral_link()}/pg/conversion_link",
                        "affiliate" => "{$auth->referral_link()}/pg/affiliate",
                    ]; ?>
                    <div class="tab-content bd bd-gray-300 bd-l-0 pd-20" id="myTabContent4">
                        <div class="tab-pane fade active show" id="new_users" role="tabpanel" aria-labelledby="home-tab4">
                            <h6>New Users</h6>
                            <p class="mg-b-0">Send it to sports lovers who are not registered on our platform, and they will become your referrals. </p>


                            <div onclick="copy_text('<?= $referral_links['new_users']; ?>')">
                                <div class="form-group mt-2">
                                    <input class="form-control" value="<?= $referral_links['new_users']; ?>">
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-outline-dark btn-xs">Copy link</button>
                                    <!-- <button class="btn btn-outline-dark btn-xs"><i class="fa-2 fa fa-qrcode"></i> Download QrCode</button> -->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="developer_api" role="tabpanel" aria-labelledby="profile-tab4">
                            <h6>Developer API</h6>
                            <p class="mg-b-0">
                                If your clients are building a bet conversion or similar service, you can
                                invite them to use our API. It takes less than 5 minutes to integrate.
                            </p>


                            <div onclick="copy_text('<?= $referral_links['developer_api']; ?>')">
                                <div class="form-group mt-2">
                                    <input class="form-control" value="<?= $referral_links['developer_api']; ?>">
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-outline-dark btn-xs">Copy link</button>
                                    <!-- <button class="btn btn-outline-dark btn-xs"><i class="fa-2 fa fa-qrcode"></i> Download QrCode</button> -->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="widget" role="tabpanel" aria-labelledby="contact-tab4">
                            <h6>Widget</h6>
                            <p class="mg-b-0">If your clients already have a website/app where they want to convert bet codes, you can invite them to use our widget.</p>
                            <div>
                                <li>No developer/technical cost required.</li>
                                <li>One integration, access to all bookies.</li>
                                <li>Just plug and play!</li>

                                <p> For Tipsters, Betting companies, Prediction apps, Bettors, and Sports lovers.</p>

                                <div onclick="copy_text('<?= $referral_links['widget']; ?>')">
                                    <div class="form-group mt-2">
                                        <input class="form-control" value="<?= $referral_links['widget']; ?>">
                                    </div>

                                    <div class="form-group">
                                        <button class="btn btn-outline-dark btn-xs">Copy link</button>
                                        <!-- <button class="btn btn-outline-dark btn-xs"><i class="fa-2 fa fa-qrcode"></i> Download QrCode</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="bet_shop" role="tabpanel" aria-labelledby="contact-tab4">
                            <h6>Bet shop</h6>
                            <p class="mg-b-0">
                                If your clients are runs an offline betting shop for e.g bet9ja, betking, accessbet, etc you can
                                invite them to get our betshop installation code/link. With this, they make more affiliate commissions by converting from other bookies to the one they are affiliated with.
                            </p>
                            <div onclick="copy_text('<?= $referral_links['betshop']; ?>')">
                                <div class="form-group mt-2">
                                    <input class="form-control" value="<?= $referral_links['betshop']; ?>">
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-outline-dark btn-xs">Copy link</button>
                                    <!-- <button class="btn btn-outline-dark btn-xs"><i class="fa-2 fa fa-qrcode"></i> Download QrCode</button> -->
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="conversion_link" role="tabpanel" aria-labelledby="contact-tab4">
                            <h6>Conversion link</h6>
                            <p class="mg-b-0">
                                If your clients would love to sponsor others, do giveaway, celebrate grand winning, you can
                                invite them to use our conversion link.
                            </p>
                            <div onclick="copy_text('<?= $referral_links['conversion_link']; ?>')">
                                <div class="form-group mt-2">
                                    <input class="form-control" value="<?= $referral_links['conversion_link']; ?>">
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-outline-dark btn-xs">Copy link</button>
                                    <!-- <button class="btn btn-outline-dark btn-xs"><i class="fa-2 fa fa-qrcode"></i> Download QrCode</button> -->
                                </div>
                            </div>
                        </div>
                        <?php
                        $commission = SiteSettings::getAffiliateCommissionStructure(); ?>
                        <div class="tab-pane fade" id="affiliate" role="tabpanel" aria-labelledby="contact-tab4">
                            <h6>Affiliate</h6>
                            <p class="mg-b-0">If you know any good affiliate, refer them and earn <?= $commission['structure']['ngn']['levels'][2]['commission_in_percent']; ?>% on their direct lines' purchases.
                            </p>


                            <div onclick="copy_text('<?= $referral_links['affiliate']; ?>')">
                                <div class="form-group mt-2">
                                    <input class="form-control" value="<?= $referral_links['affiliate']; ?>">
                                </div>

                                <div class="form-group">
                                    <button class="btn btn-outline-dark btn-xs">Copy link</button>
                                    <!-- <button class="btn btn-outline-dark btn-xs"><i class="fa-2 fa fa-qrcode"></i> Download QrCode</button> -->
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="html_banners" role="tabpanel" aria-labelledby="contact-tab4">
                            <h6>HTML Banners</h6>
                            <p class="mg-b-0"></p>

                            <div>

                                <div class="form-group">
                                    <label>This code is to be placed on your site. It contains a dynamic banner which will display relevant offers to your visitors.</label>
                                    <select class="form-control">
                                        <option>300x250</option>
                                        <option>728x90</option>
                                        <option>468x60</option>
                                        <option>120x600</option>
                                        <option>250x250</option>
                                        <option>336x280</option>
                                        <option>300x600</option>
                                        <option>130x600</option>
                                        <option>160x600</option>
                                        <option>970x90</option>
                                        <option>970x250</option>
                                    </select>

                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" rows="5"><iframe src="https://convertbetcodes.com/embed/300x250/ib2547685" 
                                        allowtransparency="true"
                                         framespacing="0" frameborder="no" scrolling="no" width="300"height="250"></iframe></textarea>

                                </div>
                            </div>



                        </div>
                    </div>
                </div><!-- media-body -->
            </div><!-- media -->
        </div>
    </div>
</div>





<div class="mb-5">
    <h5>Promotional materials
        <br><small class="tx-12">Use this materials (Ad copies, Testimonials, Ad creatives) to boost your promotions.</small>
    </h5>
    <div class="row row-xs">

        <div class="col-12">
            <div class="media">
                <i class="fa fa-circle"></i>
                <div class="media-body mg-l-10">
                    <div class="tx-12 mg-b-4">
                        <a href="https://t.me/convertbetcodesAffiliates">Join Affiliate Group</a>
                    </div>
                    <div class="progress ht-3 mg-b-20">
                        <div class="progress-bar wd-15p" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="media">
                <i class="fa fa-circle"></i>
                <div class="media-body mg-l-10">
                    <div class="tx-12 mg-b-4">
                        <a href="https://drive.google.com/drive/folders/1V1nGmAzjCRo_CAm5ZbXrwon53q23CkPU?usp=sharing">Graphics materials</a>
                    </div>
                    <div class="progress ht-3 mg-b-20">
                        <div class="progress-bar wd-15p" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="media">
                <i class="fa fa-circle"></i>
                <div class="media-body mg-l-10">
                    <div class="tx-12 mg-b-4">
                        <a href="https://testimonial.to/convert-bet-codes/all">See Testimonials</a>
                    </div>
                    <div class="progress ht-3 mg-b-20">
                        <div class="progress-bar wd-15p" role="progressbar" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>





<?php include_once 'includes/footer.php'; ?>