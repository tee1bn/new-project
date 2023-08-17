<?php
$page_title = "Free Predictions ";
$page_description = "Free Predictions ";

use Illuminate\Support\Carbon;


$flags = explode("/", $conversion['bookies_train']);

$new_date = new Carbon($conversion['updated_at']);;

$bookie_keys = $conversion->bookieKeys();
$home_array = $bookie_keys['home_array'];
$destination_array = $bookie_keys['destination_array'];

$timing =  $conversion->getStartAndStopTime(true);
include_once 'includes/header.php'; ?>




<div class="row">

    <div class="col-sm-12 col-lg-6 mg-t-10">

        <div class="card">

            <div class="card-body" style="padding:0px;">


                <div style="padding:10px;" class="card">


                    <div class="row">

                        <div class="col-6">
                            <p class="tx-11 tx-spacing-1 tx-color-03 tx-medium mg-b-5">
                                <span class=""><?= $conversion['home_entries']['summary']['no_of_entries']; ?>events @<?= $home_array['odds_value']; ?> odd</span>
                            </p>
                        </div>
                        <div class="col-6 text-right">
                            <p class="tx-11 tx-spacing-1 tx-color-03 tx-medium mg-b-5">
                                <span class="">by @guest</span>
                            </p>

                        </div>
                    </div>

                    <h4 class="tx- tx-normal tx-rubik tx-spacing--2 mg-b-5">
                        <span class="float-left">
                            <?= $conversion['home_entries']['summary']['booking_code']; ?>
                            <span class=" tx-15 flag-icon flag-icon-<?= $flags[0] ?? ''; ?>"></span><br>
                            <code class="float-left badge badge-drk"><?= $conversion['home_bookie']['name']; ?> </code>
                        </span>
                        <span class="float-right tx-12 text-right">
                            🔥 <?= $conversion['gravity']; ?>+<br>

                        </span>
                    </h4>


                    <small>
                        <span class="text-danger"><i class="fa fa-clock"></i> <?= date("M d, H:i", strtotime($conversion->starts_at)); ?> </span>
                        <span class="float-right"><a href="<?= $conversion->predictionEmbedLink; ?>"><?= $timing['matches_left']; ?> matches left</a></span>
                    </small>
                </div>

                <div>
                    <!--  <ul class="nav nav-tabs">

                        <li class="nav-item active">
                            <a class="nav-link" data-toggle="tab" href="#lines">
                                <?= $conversion['home_entries']['summary']['no_of_entries']; ?> Events</a>
                        </li>
                        <span>convertbetcodes.com</span>
                    </ul> -->

                    <ul class="list-unstyled mg-b-0 ">
                        <li class="list-label">

                            <?= $conversion['dump']['destination_bookie_entries']['summary']['no_of_entries'] ?? 0; ?>
                            <small>Matches</small>
                            <span class="float-right">
                                <a href="<?= domain; ?>" target="_blank"><span style="text-transform: lowercase;"><i class="fa fa fa-bolt"></i> convertbetcodes.com</span></a>
                            </span>
                        </li>

                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div class="tab-pane  active " id="lines">

                            <ul class="list-unstyled mg-b-0 ">
                                <div>
                                    <?php
                                    $i = 1;

                                    $merged_list = $conversion->getMergedListOfConvertedEvents();

                                    foreach ($merged_list['lists'] as $line) :
                                        $check = $line['is_converted'] ? "tx-success fa-check-circle" : "tx-danger fa-times";
                                    ?>
                                        <div>
                                            <div style="border-left: 5px double blueviolet;">
                                                <li class="list-item" style="padding: 3px !important;">
                                                    <div class="media align-items-center">
                                                        <div>
                                                            <?= $i; ?>. &nbsp;
                                                        </div>
                                                        <div class="media-body mg-sm-l-15">
                                                            <p class="tx-12 mg-b-0 tx-color-03"><i class="fa-1x <?= $line['sport']['icon'] ?? 'fa fa-futbol'; ?>"></i> <?= $line['home']['tournament_name'] ?? 'N/A'; ?> </p>
                                                            <p class="tx-medium mg-b-0"><?= $line['home']['item_name']; ?> </p>
                                                            <p class="tx-12 mg-b-0 tx-color-03"><?= $line['home']['market_name'] ?? ''; ?>
                                                                <b><span class="badg"><?= $line['home']['outcome_name'] ?? ''; ?></span></b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right tx-rubik">
                                                        <p class="mg-b-0 text-muted"><small class="badge bade-dark badge-pill"><?= $conversion['home_bookie']['name']; ?></small></p>
                                                        <p class="mg-b-0 text-muted"><small class="badge bade-dark badge-pill">
                                                                <?= date("M d, H:i", strtotime($line['home']['item_date'])); ?> </small></p>
                                                    </div>
                                                </li>
                                            </div>
                                        </div>

                                    <?php $i++;
                                    endforeach; ?>
                                </div>
                            </ul>


                        </div>

                    </div>
                </div>

            </div><!-- card-body -->
        </div><!-- card-body -->

        <div class="mg-y-20 btn-group btn-group-sm btn-block" role="group" aria-label="Basic example">

            <a href="<?= $prev->predictionEmbedLink; ?>" class="btn btn-outline-dark">&lt;&lt;Prev</a>
            <a href="<?= $next->predictionEmbedLink; ?>" class="btn btn-outline-dark">Next&gt;&gt;</a>
        </div>

    </div><!-- card -->




</div>





<style>
    .bookie-name {
        position: relative;
        top: -6px;
    }

    .events-odds {

        margin: 0px;
        line-height: 16px;
        font-size: 12px;
    }

    .conversion-arrow {

        position: absolute;
        left: 45%;
        color: #979797;
    }
</style>






<?php include_once 'includes/footer.php'; ?>