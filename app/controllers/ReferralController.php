<?php


/**
 */
class ReferralController extends controller
{


    public function __construct()
    {
    }


    public function registerTracker($referral_username = null)
    {


        if ($referral_username == null) {
            return false;
        }


        $referral = User::where('username', $referral_username)->first();

        if ($referral == null) {
            return false;
        }

        if (isset($_COOKIE['referral'])) {
            // return false;
        }

        setcookie('referral', $referral_username, time() + (86400 * 30 * 365), "/"); // 86400 = 1 year

    }



    public function index($referral_username = null)
    {

        $explode = explode("/", $_GET['url']);
        $referral_username = $explode[1] ?? null;
        $referral_username = str_replace("_", " ", $referral_username);



        $this->registerTracker($referral_username);


        $path = implode("/", array_slice($explode, 2));

        Redirect::to($path);
    }
}
