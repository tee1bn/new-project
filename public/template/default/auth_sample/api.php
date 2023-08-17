<?php
$page_title = "Api integration";
include_once 'includes/header.php'; ?>

<?php include_once 'includes/auth_nav.php'; ?>

<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Api integration</h4>
    </div>
    <!--   <div class="d-none d-md-block">
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="upload" class="wd-10 mg-r-5"></i> Export</button>
        <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="share-2" class="wd-10 mg-r-5"></i> Share</button>
        <button class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="sliders" class="wd-10 mg-r-5"></i> Settings</button>
    </div> -->
</div>


<div class="row row-xs">



    <div class="col-md-12 alert alert-primary alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Note!</strong> API Key issuance & setup fee is $20. To request for API key, <a href="https://flutterwave.com/pay/convertbetcodes">kindly make payment here </a>
    </div>





    <div class="col-md-12  mg-t-10">
        <div class="card ht-100p">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mg-b-0">API Keys
                </h6>
                <div class="d-flex">
                    <small class="float-right"><a href="<?= $api_doc; ?>"> API documentation </a></small>
                </div>
            </div>


            <ul class="list-group list-group-flush tx-13">
                <?php foreach ($apis as $key => $api) : ?>
                    <li class="list-group-item d-flex pd-sm-x-20">
                        <!-- <div class="avatar"><span class="avatar-initial rounded-circle bg-dark"><i class="fa fa-code"></i></span></div> -->
                        <div class="pd-sm-l-10 mg-l-5">
                            <p class="tx-medium mg-b-0 text"><?= $api['DisplayName'] ?? 'default'; ?>
                                <small class="tx-12 tx-success mg-b-0"><?= $api['ActiveStatus']; ?></small>
                            </p>
                            <small class="tx-12 tx-color-03 mg-b-0">API KEY: <b style="overflow-wrap: anywhere;"><?= $api->shownApiKey; ?></b></small>
                        </div>
                        <div class="mg-l-auto text-right">
                            <?= MIS::generate_form([], "$domain/user/switch_api/$api->id", '<span><i class="fa fa-power-off"></i></span>', true); ?>
                        </div>
                    </li>
                <?php endforeach; ?>

                <?php if (count($apis) == 0) : ?>

                    <li class="list-group-item d-flex pd-sm-x-20 text-center">

                        <span>No records found</span>

                    </li>
                <?php endif; ?>

            </ul>

        </div>

    </div>

</div><!-- row -->



<div class="mt-5 alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Warning!</strong>
    <p class="mg-b-0 tx-10">

        You are responsible for keeping your API keys secure.
        If you ever feel it has been compromised or in the hands of unauthorized persons, you can request another key.<br>
        You should not use your KEYS on the client codes as codes on the client's side can be spoofed.
    </p>
</div>
<?php include_once 'includes/footer.php'; ?>