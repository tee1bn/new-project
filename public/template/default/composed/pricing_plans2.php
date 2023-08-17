<div class="col-md-12">

    <?php

    use v2\Classes\ExchangeRate;
    use v2\Classes\CurrencyArray;
    use v2\Shop\Shop;

    $settings = SiteSettings::find_criteria('rules_settings')->settingsArray;
    $shop = new Shop;
    $plans = SiteSettings::getPlans();
    $packages = $plans['plans'];
    $currencies = (new CurrencyArray)->currencies;


    $exchange = new ExchangeRate;
    $priced_currency = SiteSettings::pricedCurrency();


    $code = Config::currency('code');

    foreach ($packages as $key => &$package) {
        //conversion
        $conversion = $exchange->setFrom($priced_currency)
            ->setTo($code)
            ->setAmount($package['price'])
            ->getConversion();

        $package['priced_currency'] =  $priced_currency;
        $package['converted_price'] = round((float) $conversion['destination_value'], 2);
        $package['end_currency'] = $code;
    }

    $subscriptions = collect($packages)->groupBy('group'); ?>

    <?php $this->view('composed/notice_for_rollable_units', [], true, true); ?>


    <p>**<b class="text-primary blink_me">Change the currency at the top-right to your preference.</b></p>

    <?php foreach ($subscriptions as $group_name => $group) : ?>

        <h5 style="text-transform:capitalize;"><?= $group_name; ?></h5>
        <div class="row match-height">
            <?php
            foreach ($group as  $subscription) :

                if (!$admin  && $subscription['hide']) {
                    continue;
                }
            ?>
                <div class="col-md-4 mt-2">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <h4 class="card-title"><?= $subscription['name']; ?></h4>

                                <h5 class="card-subtitle text-mute tx-16">
                                    <b class="float-right">
                                        <?= $currencies[$subscription['end_currency']]; ?><?= ($subscription['converted_price']); ?>
                                    </b>
                                </h5>
                            </div>

                            <div class="card-body">
                                <h6 class="card-subtitle text-mute">
                                    <?= $subscription['expires_at'] == INF ? "unlimited" : $subscription['expires_at'] ?> days validity
                                </h6>
                                <?php if ($group_name == 'business') : ?>
                                    <p class="card-text"> <?= $subscription['paths'] == INF ? "unlimited" : $subscription['paths'] ?> paths</p>
                                <?php endif; ?>

                                <ul class="list-group list-group-flush">

                                    <?php foreach ($plans['benefits'] as $key => $benefit) :
                                        $benefit_value = $subscription[$key];
                                    ?>
                                        <li class="list-group-item small-padding tx-12 text-capitalize">
                                            <?php if (is_bool($benefit_value)) : ?>

                                                <?php if ($benefit_value) : ?>
                                                    <span class="badge badge-success float-right"><i class="fa fa-check"></i></span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger float-right"><i class="fa fa-times"></i></span>
                                                <?php endif; ?>
                                            <?php else :
                                                if ($benefit_value === null) {
                                                    continue;
                                                }
                                                $benefit_value = $benefit_value == INF ? "unlimited" : "$benefit_value";

                                            ?>

                                                <i class=" float-right"><?= $benefit_value; ?></i>
                                            <?php endif; ?>


                                            <?= $benefit; ?>
                                        </li>

                                    <?php endforeach; ?>
                                </ul>
                                <br>

                                <?php if ($subscription['price'] != 0) : ?>

                                    <div class="form-group">
                                        <a href="<?= domain; ?>/user/pay_selected_plan/<?= $subscription['id']; ?>#unit_packs" class="btn btn-block btn-sm btn-outline-primary">Order now</a>
                                    </div>


                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
        <br>

    <?php endforeach; ?>
    <span class="text-danger">* All Packages are fair use only(rate limited).</span>
    <br>
    <br>
    <!-- <h5 style="text-transform:capitalize;">Coporates</h5> -->
    <div class="row match-height">
        <!--  <div class="col-12">
            For Bookies, Bookmakers, Sports providers, etc., kindly <a href="<?= domain; ?>/pg/contact_us">contact us</a>
        </div> -->


        <div class="col-12 mt-5">
            <h5 style="text-transform:capitalize;">Happy Customers</h5>

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


    <style>
        .betshop {
            color: #0168fa;
            text-shadow: 1px 1px 1px #4a5661;
        }

        .small-padding {
            padding: 4px;
        }
    </style>

</div>