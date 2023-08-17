<?php

namespace v2\Utilities\Affiliates;

use SiteSettings, Session, Exception;
use v2\Models\Wallet\Journals;
use v2\Models\UserWithdrawalMethod;
use v2\Models\Wallet\ChartOfAccount;
use v2\Models\Wallet\Classes\AccountManager;
use Illuminate\Database\Capsule\Manager as DB;

class Payout
{


    public $month;

    public $month_range;

    public $settings;

    public $currency = 'NGN';

    public $accounts_query;

    public $users_query;





    public function setSettings()
    {
        $settings = SiteSettings::getAffiliateCommissionStructure();
        $this->settings = $settings['structure'][strtolower($this->currency)];

        return $this;
    }


    public function getWallets()
    {

        $min_amount = $this->settings['min_withdrawals'];

        //check all wallet with atleast 1000
        $tag = strtolower("{$this->currency}_wallet");

        $per_page = 25;

        $wallets = ChartOfAccount::where('ac_charts_of_accounts.tag', "$tag")
            ->where('ac_charts_of_accounts.currency', $this->currency)
            ->where('a_available_balance', '>=', $min_amount);

        $identifier = "#ap#y-m-d#u44";

        $payout_month = $this->month_range['start'];
        $withdrawal_list = Journals::where('ac_account_journals.tag', 'withdrawal')
            ->where('ac_account_journals.currency', $this->currency)
            // ->where('status',  $this->currency)
            ->where('ac_account_journals.identifier', 'like', "%ap#$payout_month%");




        //join with (withdrawal request for same month) 
        //and get rows not in withdrawal request list
        //



        $query =
            $wallets->select('*', 'ac_charts_of_accounts.id as id')
            ->leftJoinSub($withdrawal_list, 'withdrawal_list', function ($join) {
                $join->on('ac_charts_of_accounts.owner_id', '=', 'withdrawal_list.user_id');
            })->where('withdrawal_list.id', null);

        echo "{$query->count()} remaning. ";

        $this->accounts_query = $query->take($per_page);


        return $this;
    }



    public function createWithdrawalRequests()
    {

        foreach ($this->accounts_query->get() as $key => $account) {
            //get balance for the month and deduct 100, then make withdrawal request
            $withdrawal_account = ChartOfAccount::find($account->id);

            $bal = $withdrawal_account->get_balance($this->month_range['end']);
            $amount_requested  = $bal['account_currency']['available_balance'];
            $payable  = $bal['account_currency']['available_balance'] - $this->settings['withdrawal_fee'];



            //create withdrawal
            $method_key = strtolower($this->currency) . "_bank";
            $method_details = UserWithdrawalMethod::for($account->owner_id, $method_key);

            if ($method_details == null) {
                continue;
            }

            $payout_month = $this->month_range['start'];

            try {

                $withdrawal_request = [
                    'withdrawal_account' => $withdrawal_account->id,
                    'withdrawal_method' => $withdrawal_account->id,
                    'amount' => $amount_requested,
                    'status' => 2, //pending
                    'collect_withdrawal_fee' => false,
                    'narration' => "withdrawal",
                    'journal_date' => null,
                    'user_id' => $account->owner_id,
                    'currency' => "NGN",
                    'identifier' => "#ap#$payout_month#{$account->owner_id}",
                    "method" =>  json_encode($method_details->toArray())
                ];



                $request = AccountManager::withdrawal($withdrawal_request);
                $request->updateDetailsByKey('withdrawal_method', ($method_details->toArray()));

                $payables = [
                    "amount" => $amount_requested,
                    "payable" => $payable,
                ];

                $request->updateDetailsByKey(
                    'payables',
                    $payables
                );


                if (!$request) {
                    throw new \Exception("Error Processing Request", 1);
                }

                DB::commit();
                Session::putFlash('success', "Withdrawal initiated successfully");
            } catch (Exception $e) {
                DB::rollback();
                echo "{$account->owner_id} {$account->owner->fullname}";
                print_r($e->getMessage());
                Session::putFlash('danger', "Something went wrong. Please try again.");
            }
        }
    }


    /**
     * Set the value of month
     *
     * @return  self
     */

    public function setMonth($month = null)
    {

        $month = $month ?? date("Y-m", strtotime("-1 month"));

        $this->month = $month;
        $this->month_range = [
            "start" => date("$month-01"),
            "end" =>  date("Y-m-t", strtotime("$month-01"))
        ];

        //ensure month is a previous month
        if (time() < strtotime("{$this->month_range['end']} 03:00:00")) {

            throw new \Exception("This month:$month is not a previous month", 1);
            return;
        }

        return $this;
    }

    /**
     * Set the value of currency
     *
     * @return  self
     */
    public function setCurrency($currency = null)
    {
        if ($currency == null) {
            return $this;
        }

        $this->currency = $currency;

        return $this;
    }
}
