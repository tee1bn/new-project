<?php

use Illuminate\Database\Capsule\Manager as DB;
use v2\Models\HotWallet;
use v2\Models\InvestmentPackage;
use v2\Models\Investment;

require_once "app/controllers/home.php";


/**
 *
 */
class PackageCrudController extends controller
{

    public function __construct()
    {

        if (!$this->admin()) {
            $this->middleware('current_user')
                ->mustbe_loggedin();
        }

    }


    public function resume_package($package_id)
    {
        # code...
    }


    public function pause_package($package_id)
    {

        $pack = HotWallet::find($package_id);

        if ($pack == null) {
            Session::putFlash("danger","Not Exiiting");
        }


        if ($pack->running_status == 1) {

            $pack->pause();
        }else{

            $pack->play();
        }

        echo "<pre>";
        print_r($pack->toArray());

        Redirect::back();
    }


    public function submit_simulate_packages()
    {
        echo "<pre>";
        print_r($_POST);


        $pack = InvestmentPackage::find($_POST['investment_id']);




        $this->validator()->check(Input::all(), array(

            'investment_id' => [
                'required' => true,
            ],
        ));


        if (!$this->validator->passed()) {
            Session::putFlash('danger', Input::inputErrors());
            Redirect::back();
        }



        $username = $_POST['username'];
        $auth = User::where('username', $username)->first();

        if ($auth == null) {
            Session::putFlash('danger', "User with username: <code>$username</code> not found. Please enter the correct username");
            Redirect::back();

        }



        DB::beginTransaction();

        $amount = $pack->DetailsArray['min_capital'];

        try {
           
            
            //create investment
            $investment = Investment::create([
                'user_id' => $auth->id,
                'pack_id' => $pack->id,
                'capital' => $amount,
                'worth_after_maturity' => $pack->getWorthAfterMaturity()['roi_and_capital'],
                'currency_id' => null,
                'matures_at' => $pack->getMaturityTimeFrom(),
                'status' => 1,
                'admin_id' => $this->admin()->id,
                'extra_detail' => $pack->toJson(),  
            ]);
            
            
            if ($investment == false) {
                throw new Exception("Could not create investment", 1);
            }
            

            DB::commit();
            Session::putFlash('success', "{$investment->pack->name} purchased successfully");

        } catch (Exception $e) {
            DB::rollback();
            // print_r($e->getMessage());
            Session::putFlash('danger', 'Action Failed');
        }

        Redirect::back();
    }

}


?>