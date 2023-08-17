<?php

use v2\Classes\Trial;
use v2\Classes\UTrial;
use v2\Classes\QuickBetEditor;
use v2\Models\BetcodeConversion;
use v2\Classes\BetCodesConverter;

/**
 * this class is the default controller of our application,
 *
 */
class ConvertController extends controller
{


    public function __construct()
    {
    }


    public function test()
    {
        $from_code =  $_REQUEST['from_code'];

        $from = explode(":", $_REQUEST['from_bookmaker']);
        $from_bookmaker = $from[0];
        $from_bookmaker_country =  $from[1] ?? null;


        $to = explode(":", $_REQUEST['to_bookmaker']);
        $to_bookmaker =  $to[0];
        $to_bookmaker_country =  $to[1] ?? null;


        // header("content-type:application/json");
        echo "<pre>";

        $auth = User::find(1);


        if ($_ENV['APP_ENV'] == 'production') {
            if (!$this->admin()) {
                $this->middleware('current_user')
                    ->mustbe_loggedin()
                    ->must_have_verified_email()
                    ->must_be_company_tester();
            }
        }


        try {

            $converter = new BetCodesConverter;

            $response  = $converter
                ->setCode($from_code)
                ->setHomeBookie($from_bookmaker, $from_bookmaker_country)
                ->setDestinationBookie($to_bookmaker, $to_bookmaker_country)
                ->setUser($auth)
                ->setUseNearestEquivalence(true)
                // ->setChannel('a')
                // ->PrepareDestinationBookieLines()
                ->ChargeForConversion(false)
                ->convert()
                ->attemptCharge();


            $response['status'] = '200';
        } catch (\Exception $e) {
            print_r($e->getMessage());
            $response = ['status' => 'fail'];
        }


        // ob_clean();
        print_r($response['dump']);
        // echo json_encode($response);
    }




    public function convert_codes()
    {
        $from_code =  $_REQUEST['from_code'];


        $from = explode(":", $_REQUEST['from_bookmaker']);
        $from_bookmaker = $from[0];
        $from_bookmaker_country =  $from[1] ?? null;


        $to = explode(":", $_REQUEST['to_bookmaker']);
        $to_bookmaker =  $to[0];
        $to_bookmaker_country =  $to[1] ?? null;

        $charge =  $_REQUEST['charge'];


        header("content-type:application/json");


        try {

            $user = User::find($_REQUEST['user_id']);
            $converter = new BetCodesConverter;
            $response  = $converter->setCode($from_code)
                ->setHomeBookie($from_bookmaker, $from_bookmaker_country)
                ->setDestinationBookie($to_bookmaker, $to_bookmaker_country)
                ->setUser($user)
                ->setUseNearestEquivalence(true)
                ->ChargeForConversion($charge)
                ->convert()
                ->attemptCharge();


            $response['status'] = '200';
        } catch (\Exception $e) {
            // print_r($e->getMessage());
            $response = [
                'status' => 'fail',
                'errors' => $e->getMessage(),
            ];
        }

        ob_clean();
        echo json_encode($response);
    }




    public function edit_booking_code()
    {

        $code =  $_REQUEST['code'];
        $from = $_REQUEST['convert_from'];
        $to =  $_REQUEST['convert_to'];

        $qb = new QuickBetEditor;
        $r = $qb->loadBookingCode("$code", "$from", "$to");


        if ($r == null) {
            Session::putFlash("danger", "This code cannot be edited now.");
            Redirect::back();
        }

        $conversion = $r['conversion'];

        header("content-type:application/json");

        ob_clean();

        $link = $conversion->EditLink;
        echo json_encode(compact('link'));
        // Redirect::to("c/edit/$conversion->id");
    }

    public function booking_codes()
    {


        //check token
        if (!Input::exists()) {
            echo "crsf error";
            return;
        }

        $validator = new Validator;

        $validator->check($_REQUEST, [
            'code' => [
                'required' => true,
                'min' => 3,
                'max' => 32,
                'no_special_character' => true,
                // 'name'=>"Booking Code" ,
            ],
            'convert_from' => [
                'required' => true,
                'min' => 3,
                'max' => 32,
            ],
            'convert_to' => [
                'required' => true,
                'min' => 3,
                'max' => 32,
            ],

        ]);




        if (!$validator->passed()) {
            Session::putFlash("danger", Input::inputErrors());
            Redirect::back();
        }

        $from_code =  $_REQUEST['code'];
        $from_bookmaker = $_REQUEST['convert_from'];
        $to_bookmaker =  $_REQUEST['convert_to'];

        header("content-type:application/json");



        if (Input::get('edit') == 'edit') {
            $this->edit_booking_code();
            return;
        }




        try {

            $auth = $this->auth();





            $api_domain = Config::apiDomain();
            $api_domain = Config::domain();
            $user_id = $auth->id ??  false;


            /*  if (!$auth->isPaidUser()) {
                Session::putFlash("danger", "Please <a href='$api_domain/pg/pricing'>subscribe</a> to use this service.");
                Redirect::back();
            } */




            $trial = new Trial;
            $trial->setUser($auth);

            $charge = !$trial->canTry();
            $trial->countAttempt();
            // $charge = true;
            $trial_left  = $trial->trialLeft();
            $min_trials = max(0, $trial_left);



            if ($min_trials > 0) {
                Session::putFlash("warning", "You have {$min_trials} free conversion(s) left. 
                <br>          <a  onclick=$('#howToSubscribe').modal(); class='text-inf text-bold alert-link'>Please subscribe to continue </a>");
            }

            $query_string  = http_build_query(compact('from_code', 'from_bookmaker', 'to_bookmaker', 'user_id', 'charge'));



            $url = "$api_domain/convertbetcodes/convert_codes?$query_string";
            $response = MIS::make_get($url);


            if ($response == null) {
                // throw new Exception("Error Processing Request", 1);
            }

            $response = json_decode($response, true);
            if (!isset($response['home_entries']) || $response['home_entries'] == null || $response['home_entries'] == []) {

                $view = $this->buildView('guest/notice_to_buy_unit', compact('response'), true, true);
            } else {
                $view = $this->buildView('guest/convertbetcodes_response_view', compact('response'), true, true);
            }
            ob_clean();

            $data = compact('view');
            header("content-type:application/json");
            echo json_encode($data);
        } catch (\Exception $e) {
            Session::putFlash("danger", "Please try again");
            // Redirect::back();
        }
    }
}
