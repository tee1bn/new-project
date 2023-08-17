<?php

namespace app\controllers\api;

use MIS;
use Input;
use Validator;
use v2\Models\Applet;
use v2\Classes\Bookies;
use v2\Models\AppletTrack;
use v2\Models\BetcodeConversion;
use v2\Classes\BetCodesConverter;

/**
 *
 */
class AppletController
{

    public function __construct()
    {
    }



    public function isValidApplet($host, $id)
    {

        $decoded = json_decode(MIS::dec_enc("decrypt", $id), true);
        if (!is_array($decoded)) {
            return false;
        }



        $applet = Applet::where("id", $decoded['id'])->Enabled()->first();
        if (!$applet) {
            return false;
        }


        $details = $applet->details;
        $whitelisted_domains = explode(",", $details['domain']);

        $whitelisted_domains = array_map(function ($item) {
            return trim($item);
        }, $whitelisted_domains);


        if ($details['domain'] == "*") { //allow access from all host
            return compact('applet', 'decoded');
        }


        if (!in_array($host, $whitelisted_domains)) {
            return false;
        }



        /* 
        unset($applet->id);
        unset($applet->user_id);
        */
        return compact('applet', 'decoded');
    }



    public function convert()
    {



        $json = file_get_contents('php://input');
        $input = json_decode($json, true);

        //confirm recaptcha
        $post_data =  [
            'secret' => $_ENV['google_re_captcha_applet_secret_key'],
            'response' => $input['recaptcha_token'] ?? '',
        ];
        $response = MIS::make_post("https://www.google.com/recaptcha/api/siteverify", $post_data);
        $csrf =  (json_decode($response, true));

        if (($csrf['success'] != 1) || !in_array($csrf['hostname'], ["localhost", "applet.convertbetcodes.com"])) {
            // return ["data" => $csrf,  "message" => "CRSF protected", "status" => 400];
        }


        $validator = new Validator;
        $validator->check($input, [
            'host' => [
                'required' => true,
                // 'url' => true,
            ],
            'id' => [
                'required' => true,
            ],
            'from' => [
                'required' => true,
            ],
            'to' => [
                'required' => true,
            ],
            'booking_code' => [
                'required' => true,
            ],
        ]);




        $message = "";
        if (!$validator->passed()) {
            $message = (Input::errors());
            return ["data" => [], "message" => "Invalid request", "status" => 400];
        }

        //ensure this applet is valid
        $response = $this->isValidApplet($input['host'], $input['id']);

        if (!$response) {
            return ["data" => [], "message" => "Invalid/Host not whitelisted", "status" => 400];
        }

        $applet = $response['applet'];




        $from = explode(":", $input['from']);
        $from_bookmaker = $from[0];
        $from_bookmaker_country =  $from[1] ?? null;


        $to = explode(":", $input['to']);
        $to_bookmaker =  $to[0];
        $to_bookmaker_country =  $to[1] ?? null;

        $channel = $response['decoded']['type'];

        $converter = new BetCodesConverter;
        $converter
            ->setUser($applet->user ?? null)
            ->ChargeForConversion(true)
            ->setChannel($channel)
            ->setCode($input['booking_code'])
            ->setHomeBookie($from_bookmaker, $from_bookmaker_country)
            ->setDestinationBookie($to_bookmaker, $to_bookmaker_country)
            ->convert()
            ->attemptCharge();

        $conversion = $converter->getResponse();




        $conversion_object = BetcodeConversion::find($conversion['conversion']['id']);

        if (!$conversion_object->isOk()) {
            return ["data" => [], "message" => $conversion['errors'], "status" => 500];
        }

        $conversion['conversion']['dump'] =  $conversion_object->getMergedListOfConvertedEvents();


        //record charge if link to track unit limit
        if ($channel == 'link') {

            //check for applet functionality
            if (!$applet->linkIsFunctional()) {
                $conversion['conversion']['dump'] =  null;
                return ["data" => [], "message" => ["This conversion link is no longer functional"], "status" => 500];
            }


            $details = $applet['details'];
            $details['unit_used'] = intval($details['unit_used'])  + $converter->charge->amount;
            $applet->update([
                "details" => $details
            ]);
        }





        return ["data" => compact('conversion'), "message" => "success", "status" => 200];
    }



    /**
     * 
     *
     * @return
     */
    public function authenticate()
    {
        $json = file_get_contents('php://input');
        $input = json_decode($json, true);



        $validator = new Validator;
        $validator->check($input, [
            'host' => [
                // 'required' => true,
                // 'url' => true,
            ],
            'id' => [
                'required' => true,
            ],
        ]);


        $message = "";
        if (!$validator->passed()) {
            $message = (Input::errors());
            return ["data" => [], "message" => "Invalid request", "status" => 400];
        }

        $response = $this->isValidApplet($input['host'], $input['id']);

        if (!$response) {
            return ["data" => [], "message" => "Invalid/Host not whitelisted", "status" => 400];
        }

        $applet = $response['applet'];


        //record authentication
        AppletTrack::track($applet, $input['host'], "auth");

        unset($applet->id);
        unset($applet->user_id);

        $details = $applet->details;





        $all_bookies = (new Bookies)->getAvailabilityOfAll();


        $avail_home = array_filter($all_bookies, function ($item) use ($details) {
            return isset($details['home_bookies'][$item['bookie']]);
        });

        $home_bookies = array_map(function ($item) {
            $item['id'] = $item['bookie'];
            $item['text'] = $item['name'];

            return $item;
        }, $avail_home);



        $avail_destination = array_filter($all_bookies, function ($item) use ($details) {
            return isset($details['destination_bookies'][$item['bookie']]);
        });


        $destination_bookies = array_map(function ($item) {
            $item['id'] = $item['bookie'];
            $item['text'] = $item['name'];

            return $item;
        }, $avail_destination);


        $destination_bookies = array_map(function ($item) {
            $item['id'] = $item['bookie'];
            $item['text'] = $item['name'];

            return $item;
        }, $avail_destination);


        sort($home_bookies);
        sort($destination_bookies);

        $details['home_bookies'] = array_values($home_bookies);
        $details['destination_bookies'] = array_values($destination_bookies);
        $applet->details = $details;



        $google_re_captcha_applet_public_key = $_ENV['google_re_captcha_applet_public_key'];

        return ["data" => compact('applet', 'google_re_captcha_applet_public_key'), "message" => "success", "status" => 200];
    }
}
