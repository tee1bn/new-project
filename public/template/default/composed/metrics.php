<?php

use v2\Models\BookMaker;
use v2\Models\BetcodeConversion;
use Illuminate\Database\Capsule\Manager as DB;


$today = date("Y-m-d");
$yesterday = date("Y-m-d", strtotime("$today -1 day"));
$users = User::count();
// $total_unique_conversions = BetcodeConversion::count() + 800000;

$no_of_bookies = BookMaker::count() - 3;

$unique_request_today = BetcodeConversion::selectRaw(DB::raw("count(*) as count"))->whereRaw("
                                                            (`created_at` BETWEEN '$today 00:00:00' and '$today 23:59:59'
                                                                and home_entries is not null
                                                            )
                                                            ")->first()->count;

$unique_request_yesteday = BetcodeConversion::selectRaw(DB::raw("count(*) as count"))->whereRaw("
                                                            (`created_at` BETWEEN '$yesterday 00:00:00' and '$yesterday 23:59:59'
                                                                and home_entries is not null
                                                            )
                                                            ")->first()->count;



$unique_conversion_today = BetcodeConversion::selectRaw(DB::raw("count(*) as count"))->whereRaw("
                                                            (`created_at` BETWEEN '$today 00:00:00' and '$today 23:59:59'
                                                              and `destination_code` is not null
                                                            and `status` = 4
                                                            )")->first()->count;



$unique_conversion_yesterday = BetcodeConversion::selectRaw(DB::raw("count(*) as count"))->whereRaw("
                                                            (
                                                              `created_at` BETWEEN '$yesterday 00:00:00' and '$yesterday 23:59:59'
                                                              and `status` = 4
                                                              and `destination_code` is not null
                                                              
                                                            )")->first()->count;


$success_rate = $unique_request_today == 0 ?
    0
    : round(($unique_conversion_today / $unique_request_today  * 100), 2);

$metrics = compact(
    'users',
    'no_of_bookies'
    // 'unique_request_today',
    // 'unique_request_yesteday',
    // 'unique_conversion_today',
    // 'unique_conversion_yesterday'
);

$metrics = array_map(function ($item) {
    return MIS::restyle_text($item);
}, $metrics);

extract($metrics);


?>

<div class="row row-xs mg-y-20">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-body">
            <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Join top punters </h6>
            <div class="d-flex d-lg-block d-xl-flex align-items-end">
                <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $users; ?>+</h3>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-body">
            <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Requests</h6>
            <div class="d-flex d-lg-block d-xl-flex align-items-end">
                <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $unique_request_today; ?>+</h3>
                <p class="tx-11 tx-color-03 mg-b-0"><span class="tx-medium "><?= $unique_request_yesteday; ?>+<i class="icon ion-md-arrow-up"></i></span> Yesterday</p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-body">
            <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">CONVERSIONS @success:<?= $success_rate; ?>%</h6>
            <div class="d-flex d-lg-block d-xl-flex align-items-end">
                <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $unique_conversion_today; ?>+</h3>
                <p class="tx-11 tx-color-03 mg-b-0"><span class="tx-medium "><?= $unique_conversion_yesterday; ?>+<i class="icon ion-md-arrow-up"></i></span> Yesterday</p>
            </div>
        </div>
    </div>


    <div class="col-sm-6 col-lg-3">
        <div class="card card-body">
            <h6 class="tx-uppercase tx-11 tx-spacing-1 tx-color-02 tx-semibold mg-b-8">Bookies & Coverage</h6>
            <div class="d-flex d-lg-block d-xl-flex align-items-end">
                <h3 class="tx-normal tx-rubik mg-b-0 mg-r-5 lh-1"><?= $no_of_bookies ?>+</h3>
                <p class="tx-11 tx-color-03 mg-b-0"><span class="tx-medium tx-success">Soccer<i class="icon ion-md-arrow-up"></i></span> 147+ markets</p>
            </div>
        </div>
    </div>

</div>