<?php

$clients = [
    [
        "name" => "betcode",
        "logo" => "https://www.betcode.live/images/betcode-logo.svg",
        "show" => 1,
    ],
    [
        "name" => "convertedcode",
        "logo" => "https://www.convertedcode.com/assets/images/logo-color.png",
        "logo" => "$logo/../../bookmakers/convertedcode.PNG",
        "show" => 1,
    ],

    [
        "name" => "getbetize",
        "logo" => "https://pbs.twimg.com/profile_images/1564724651833675778/D4FtDVqf_400x400.jpg",
        "show" => 0,
    ],

    [
        "name" => "betconverter.com",
        "logo" => "https://betconverter.com/assets/images/logo_main.png",
        "show" => 1,
    ],

    [
        "name" => "betcodetrader.com",
        "logo" => "https://betcodetrader.com/wp-content/uploads/2023/01/cropped-base_logo_transparent_background.png",
        "show" => 0,
    ],

    [
        "name" => "getcodes.ng",
        "logo" => "https://www.getcodes.ng/static/media/getlogo.a5456cbc6693fb76c4d9.png",
        "show" => 0,
    ],

    [
        "name" => "betslipswitch.com",
        "logo" => "https://www.betslipswitch.com/images/bet_logo.png",
        "logo" => "$logo/../../bookmakers/betslipswitch.png",
        "show" => 0,
    ],

    [
        "name" => "thepunterbot.com",
        "logo" => "https://thepunterbot.com/favicon.ico",
        "show" => 0,
    ],
    [
        "name" => "betrelate.com",
        "logo" => "$logo/../../bookmakers/betrelate.png",
        "show" => 0,
    ],

    [
        "name" => "bookallbet",
        "logo" => "http://bookallbets.com/images/Logo%20with%20white%20Square-circle%20-orange.png",
        "show" => 0,
    ],

];

?>

<div class=" mt-5" style="display:;">
    <h5 style="text-transform:capitalize;">Trusted by</h5>
    <div class="row tx-20 mg-t-10 ">
        <?php foreach ($clients as $key => $client) :
            if ($client['show'] != 1) {
                continue;
            }
        ?>
            <div class="tx-purple col-md-3 mb-2">
                <img title="<?= $client['name']; ?>" class="" src="<?= $client['logo']; ?>" style="padding:10px;background:#3a3a3a;border-radius:10px;object-fit:contain;height:60px;width: 200px;filter: grayscale(1);">
            </div>
        <?php endforeach; ?>
    </div>

</div>