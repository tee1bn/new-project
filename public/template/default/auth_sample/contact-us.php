<?php
$page_title = "Support Tickets";
include_once 'includes/header.php'; ?>
<?php include_once 'includes/auth_nav.php'; ?>


<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
    <div>
        <?php include_once 'includes/breadcrumb.php'; ?>

        <h4 class="mg-b-0 tx-spacing--1">Support Tickets</h4>
    </div>

    <div class="">
        <a class="btn btn-light btn-xs" href="<?= domain; ?>/user/contact-us">+New Ticket</a>
    </div>
</div>

<div class="row row-xs">

    <div class="card col-12">
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">

                <form class="contact-form mt-45" id="contact" method="post" action="<?= domain; ?>/ticket_crud/create_ticket">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">

                            <div class="form-field">
                                <input class="form-control" value="<?= $auth->full_name; ?>" readonly="readonly" id="name" type="hidden" required="" name="full_name" placeholder="Your Name">

                                <input class="form-control" id="email" value="<?= $auth->email; ?>" readonly="readonly" type="hidden" required="" name="email" placeholder="Email">
                            </div>
                            <div class="form-field">
                                <input class="form-control" id="sub" value="<?= $auth->phone; ?>" readonly="readonly" type="hidden" required="" name="phone" placeholder="Phone">
                            </div>

                            <input type="hidden" name="from_client" value="true">

                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="form-field">

                                <textarea class="form-control" id="message" rows="7" name="comment" required="" placeholder="Your Message"></textarea>
                            </div>
                        </div>


                        <div class="col-md-6 col-offset-md-2">
                            <br>
                            <?= MIS::use_google_recaptcha(); ?>
                        </div>


                        <div class="col-md-12 col-lg-12 mt-30">
                            <button class=" btn btn-dark" type="submit" id="submit" name="button">
                                Send Message
                            </button>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>






</div><!-- row -->
<?php include_once 'includes/footer.php'; ?>