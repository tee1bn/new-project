<?php
$page_title = "Applets";
include_once 'includes/header.php'; ?>

<?php include_once 'includes/auth_nav.php'; ?>

<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Applets</h4>
    </div>
</div>



<div class="row row-xs">

    <div class="col-md-12 alert alert-primary alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>✅ Convert directly on your website, app, etc. <br>✅ No developer required!</strong><br>
        <!-- Retain your loyal users, boost traffic, zero technical cost. -->
    </div>



    <div class="col-md-12  mg-t-10">
        <div class="card ht-100p">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mg-b-0">Applets </h6>

                <div class="d-flex tx-18">
                    <!-- <button type="button" class="btn  btn btn-primary">Primary</button> -->
                    <a href="<?= domain; ?>/user/create_applet" class="link-03 lh-0">+ New Applet<i class="icon ion-md-refresh"></i></a>
                    <a href="#" class="link-03 lh-0 mg-l-10"><i class="icon ion-md-more"></i></a>
                </div>
            </div>


            <ul class="list-group list-group-flush tx-13">
                <?php foreach ($applets as $key => $applet) : ?>
                    <li class="list-group-item d-flex pd-sm-x-20">
                        <div class="avatar"><span class="avatar-initial rounded-circle bg-dark"><i class="fa fa-mobile"></i></span></div>
                        <div class="pd-sm-l-10 mg-l-5">
                            <p class="tx-medium mg-b-0 text-uppercase"><?= $applet['name'] ?? 'default'; ?> </p>
                            <small class="tx-12 tx-color-03 mg-b-0"><i><?= $applet['details']['domain'] ?? 'add domain'; ?></i></small>
                        </div>
                        <div class="mg-l-auto text-right">
                            <small class="tx-12 tx-success mg-b-0">
                                <?= $applet['ActiveStatus']; ?>
                            </small><br>
                            <a href="<?= domain; ?>/user/edit_applet?id=<?= $applet->id; ?>" class="">Edit</a>
                        </div>
                    </li>
                <?php endforeach; ?>

                <?php if (count($applets) == 0) : ?>

                    <li class="list-group-item d-flex pd-sm-x-20 text-center">

                        <center>
                            <a href="<?= domain; ?>/user/create_applet" class="link-03 lh-0">+ Create new Applet<i class="icon ion-md-refresh"></i></a>
                        </center>

                    </li>
                <?php endif; ?>

            </ul>


        </div>
    </div>

</div><!-- row -->
<?php include_once 'includes/footer.php'; ?>