<?php

use v2\Models\Unit;
use v2\Classes\CurrencyArray;
use v2\Classes\Bookies;

$bookies = (new Bookies)->getAvailabilityForAPI();


$page_title = "Complete Payment";
include_once 'includes/header.php';

$currencies = (new CurrencyArray)->currencies;


$plan = $packages[$plan_id];

?>

<script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
<script src="<?= asset; ?>/angulars/rave-checkout.js"></script>
<script src="https://js.paystack.co/v2/inline.js"></script>
<script src="<?= asset; ?>/angulars/paystack-checkout.js"></script>


<div>
    <div class="d-sm-flex align-items-center justify-content-between mg-b-10">
        <div>
            <?php include_once 'includes/breadcrumb.php'; ?>
            <h4 class="mg-b-0 tx-spacing--1">Complete Payment</h4>
            <a href="<?= domain; ?>/user/account_plan" style="text-docoration:underline;">See all Plans</a>
        </div>
    </div>

    <?php $this->view('composed/notice_for_rollable_units', [], true, true); ?>

    <div style="display: one;">
        <div class="mg-t-50">
        </div>
        <div class="row ">

            <div class="col-12 mg-t-3">


                <div class="card card-body">
                    <div class="media align-items- ">

                        <div class="media-body">
                            <h4 class="tx-18 tx-sm-20 mg-b-2"> <i class="fa fa-briefcase "></i> <?= $plan['name']; ?> - <small>
                                    <?= $currencies[$plan['end_currency']]; ?><?= ($plan['converted_price']); ?>
                                </small></h4>

                            <p class="tx-13 tx-color-03 mg-b-0"> <?= $plan['expires_at'] == INF ? "unlimited" : $plan['expires_at'] ?> days validity</p>


                            <ul class="list-group list-group-flush">


                                <?php foreach ($plans['benefits'] as $key => $benefit) :
                                    $benefit_value = $plan[$key];
                                ?>
                                    <li class="list-group-item small-padding tx-12 text-capitalize">
                                        <?php if (is_bool($benefit_value)) : ?>

                                            <?php if ($benefit_value) : ?>
                                                <span class="badge badge-success float-right"><i class="fa fa-check"></i></span>
                                            <?php else : ?>
                                                <span class="badge badge-danger float-right"><i class="fa fa-times"></i></span>
                                            <?php endif; ?>
                                        <?php else :
                                            $benefit_value = $benefit_value == INF ? "unlimited" : "$benefit_value";
                                        ?>

                                            <i class=" float-right"><?= $benefit_value; ?></i>
                                        <?php endif; ?>


                                        <?= $benefit; ?>
                                    </li>

                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <!-- onsubmit="submit_form(event)" -->
                    <form id="pack_subscription" data-function="on_complete_order" class="ajax_form" action="<?= domain; ?>/user/create_upgrade_request">
                        <div class="form-group">


                            <select required name="id" class="form-control" hidden>
                                <option value="">Select a pack</option>
                                <?php

                                foreach ($packages as $key => $package) :

                                    if ($package['converted_price'] == 0) {
                                        // continue;
                                    };
                                ?>
                                    <option value="<?= $package['id']; ?>" <?= $plan['id'] == $package['id'] ? 'selected' : ''; ?>>
                                        <?= $package['name']; ?> -
                                        <?= $currencies[$package['end_currency']]; ?><?= $package['converted_price']; ?>,
                                        <?= $package['max_events_per_booking'] == INF ? "unlimited" : $package['max_events_per_booking'] ?>-E/b,
                                        <?= $package['no_of_bookies'] == INF ? "unlimited" : $package['no_of_bookies'] ?>-B,
                                        <?= $package['expires_at'] == INF ? "unlimited" : $package['expires_at'] ?>days,
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>


                        <?php if (SiteSettings::PlanIsBetshop($plan)) : ?>
                            <div class="form-group">
                                <label>Select Betshop (destination) Bookie</label>
                                <select multiple required name="destination_bookies[]" data-maximum-selection-length="1" data-placeholder="Select bet shop bookie" class="form-control select2_single">
                                    <?php
                                    ksort($bookies);
                                    foreach ($bookies as $key => $bookie) : ?>
                                        <option value="<?= $key; ?>"><?= ucwords($bookie['name']); ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>


                        <?php if (SiteSettings::PlanHasAllowedBookie($plan)) : ?>

                            <div class="form-group">
                                <label>Select Allowed Bookies</label>
                                <select multiple required name="attempted_bookies[]" data-maximum-selection-length="<?= $plan['no_of_bookies']; ?>" data-placeholder="Select Allowed Bookies" class="form-control select2_single">
                                    <?php
                                    ksort($bookies);
                                    foreach ($bookies as $key => $bookie) : ?>
                                        <option value="<?= $key; ?>"><?= ucwords($bookie['name']); ?></option>

                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>



                        <div class="form-group">
                            <label>Select Payment method</label>
                            <select required name="payment_method" class="form-control">
                                <!-- <option value="rave">Flutterwave</option> -->

                                <?php foreach ($shop->get_available_payment_methods() as $key => $option) : ?>
                                    <option value="<?= $key; ?>"><?= $option['name']; ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" id="pay_button" class="btn btn-outline-primary">Pay now</button>
                        </div>

                    </form>


                </div>
            </div>



            <div class="col-12 mt-5 mb-10">
                <h4 style="text-transform:capitalize;text-align:center;">Happy Customers</h4>

                <script type="text/javascript" src="https://testimonial.to/js/iframeResizer.min.js"></script>
                <iframe id="testimonialto-carousel-all-convert-bet-codes-light" src="https://embed.testimonial.to/carousel/all/convert-bet-codes?theme=light&autoplay=on&showmore=on&one-row=off&hideDate=on&same-height=off" frameborder="0" scrolling="no" width="100%"></iframe>
                <script type="text/javascript">
                    iFrameResize({
                        log: false,
                        checkOrigin: false
                    }, "#testimonialto-carousel-all-convert-bet-codes-light");
                </script>
            </div>
        </div>
    </div>
</div>
<!-- The payemnt Modal -->
<div class="modal" id="payment_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Payment</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body" id="payment_instruction"></div>


        </div>
    </div>
</div>

<script>
    function submit_form(e) {
        e.preventDefault();

        $("#pay_button").attr("disabled", true);
        $("#pay_button").append(" <i class='fa fa-spinner fa-spin'></i>");


        var param = ($('#pack_subscription').serialize());
        const params = new URLSearchParams(param);
        const obj = Object.fromEntries([...params])
        buy_now(obj)
    }

    function buy_now(package) {

        $form = new FormData();
        $.ajax({
            type: "POST",
            // url: `${$base_url}/shop/complete_unit_order/make_payment`,
            url: `${$base_url}/user/create_upgrade_request`,
            data: JSON.stringify(package),
            contentType: 'application/json', // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
            processData: false, // NEEDED, DON'T OMIT THIS
            cache: false,
            success: function(data) {
                on_complete_order(data);
            },
            error: function(data) {},
            complete: function() {
                $("#pay_button").attr("disabled", false);
                $("#pay_button").html("Pay now");

            }
        });
    }



    on_complete_order = function($data) {


        try {
            switch ($data.gateway) {

                case 'dpopay':
                    window.location.href = $data.checkout_url;
                    break;
                case 'paystack':
                    payWithPaystack($data);
                    break;
                case 'rave':
                    payWithRave($data);
                    break;
                case 'coinbase_commerce':
                    window.location.href = $data.checkout_url;
                    break;

                default:

                    if ($data.gateway.includes("accrue")) {
                        window.location.href = $data.checkout_url;
                        // window.open($data.checkout_url, "_blank");
                        return;
                    }

                    // code block
                    $('#payment_instruction').html($data.instruction);
                    $("#payment_modal").modal({
                        backdrop: 'static',
                        keyboard: false
                    })

                    break;

            }


        } catch (e) {}
    }
</script>

<style>
    .small-padding {
        padding: 4px;
    }
</style>

<?php include_once 'includes/footer.php'; ?>