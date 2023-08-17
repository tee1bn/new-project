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
                <table id="payment-histor" class="table table-striped table-bordered zero-configuration">
                    <tbody>

                        <?php foreach ($tickets as $key => $ticket) : ?>
                            <tr>
                                <td style="padding: 0px;">
                                    <div class="col-md-12 custom-green" style="padding: 0px;">
                                        <div class="alert custom-green   mb-2" role="alert" style="margin:0px !important; ">
                                            <a href="<?= $ticket->UserLink; ?>"> <small class="float-left">
                                                    <?= $ticket->displayStatus; ?>
                                                    <span class="label badge"><?= date('M j, Y h:iA', strtotime($ticket->created_at)); ?></span>
                                                </small></a>
                                            <strong class="float-right">

                                                <?= $ticket->closeButton; ?>
                                            </strong><br>
                                            <strong><a href="<?= $ticket->UserLink; ?>">Ticket ID: <?= $ticket->code; ?></a></strong>

                                            <br>
                                            <small>Subject:
                                                <?= $ticket->subject_of_ticket; ?>
                                            </small>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($tickets->count() == 0) : ?>
                            <tr class="text-center">
                                <td>Your tickets will appear here</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>


            </div>
        </div>
    </div>
    <ul class="pagination">
        <?= $this->pagination_links($data, $per_page); ?>
    </ul>






</div><!-- row -->
<?php include_once 'includes/footer.php'; ?>