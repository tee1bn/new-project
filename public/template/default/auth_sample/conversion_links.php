<?php
$page_title = "Conversion Links 🔗";
include_once 'includes/header.php'; ?>
<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>

        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Conversion Links</h4>
        <small>The perfect giveaway for punters and their audience.</small>
    </div>
</div>

<div class="row row-xs">



    <div class="col-md-12 alert alert-dark alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>🔗 Sponsor conversions for your goons.
            <br>🔗 Celebrate grand winnings with the community.
            <br>🔗 Advertise brand while at it.
            <br>🔗 No developer/website required!
        </strong>
    </div>



    <div class="col-md-12  mg-t-10">
        <div class="card ht-100p">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mg-b-0">Links </h6>

                <div class="d-flex tx-18">
                    <!-- <button type="button" class="btn  btn btn-primary">Primary</button> -->
                    <a href="<?= domain; ?>/user/create_link" class="link-03 lh-0">+ New Link<i class="icon ion-md-refresh"></i></a>
                    <a href="#" class="link-03 lh-0 mg-l-10"><i class="icon ion-md-more"></i></a>
                </div>
            </div>


            <ul class="list-group list-group-flush tx-13">
                <?php foreach ($links as $key => $link) :

                ?>
                    <li class="list-group-item d-flex pd-sm-x-20">
                        <div class="pd-sm-l-10 mg-l-5">
                            <p class="tx-medium mg-b-0 text-uppercase"><?= $link['FunctionalStatus']; ?> <?= $link['name'] ?? 'default'; ?> </p>
                            <small class="tx-12 tx-color-03 mg-b-0" onclick="copy_text(`<?= $link->PublicLink; ?>`)"><i> <?= $link->PublicLink ?? 'Functional Link will show here'; ?></i></small>
                        </div>
                        <div class="mg-l-auto text-right">
                            <?php if (($link['details']['expires_at']) == null) : ?>
                                <small class="badge text-muted">N/A</small>
                            <?php else : ?>
                                <small class="badge text-muted"><?= date("M.j.y H:i", strtotime($link['details']['expires_at'] ?? date("Y-m-d"))) ?? "N/A"; ?></small>
                            <?php endif; ?>
                            <br>

                            <small class="tx-12 tx-success mg-b-0"><span class="badge badge-dark"><?= $link['details']['unit_used'] ?? 0; ?>/<?= $link['details']['units'] ?? "<i class='fa fa-infinity'></i>"; ?></span></small>
                            <br>
                            <a href="<?= domain; ?>/user/edit_link?id=<?= $link->id; ?>" class="">Edit Link</a>
                        </div>
                    </li>
                <?php endforeach; ?>

                <?php if (count($links) == 0) : ?>

                    <li class="list-group-item d-flex pd-sm-x-20 text-center">

                        <center>
                            <a href="<?= domain; ?>/user/create_link" class="link-03 lh-0">+ Create new link<i class="icon ion-md-refresh"></i></a>
                        </center>

                    </li>
                <?php endif; ?>

            </ul>


        </div>
    </div>

</div><!-- row -->
<?php include_once 'includes/footer.php'; ?>