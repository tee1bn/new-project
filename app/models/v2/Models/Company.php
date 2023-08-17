<?php

namespace v2\Models;


use Config;
use v2\Models\Wallet\ChartOfAccount;
use v2\Models\Wallet\BasicAccountType;
use v2\Models\Wallet\CompanyAccountType;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Company extends Eloquent
{

    protected $fillable = [
        'organisation_id',
        'created_by',
        'name',
        'company_description',
        'approval_status',
        'documents',
        'details',
        'logo'
    ];


    protected $table = 'companies';
    protected $connection = 'default';


    public function getgetLogoAttribute()
    {
        $value = $this->logo;
        if (!file_exists($value) &&  (!is_dir($value))) {
            return Config::domain() . '/' . (Config::logo());
        }

        $pic = Config::domain() . "/$value";

        return $pic;
    }



    public function get_trial_balance2($as_of_date, $start_date = null)
    {
        $charts_of_accounts  =  ChartOfAccount::for_company($this->id);
        $basic_accounts      =    BasicAccountType::orderBy('name')->get();

        foreach ($basic_accounts as  $basic_account) {

            $trial_balance[$basic_account->id]['basic_account'] = $basic_account->toArray();
            $i = 0;
            foreach ($charts_of_accounts->get() as $chart_of_account) {
                $i++;
                if ($chart_of_account->basic_account_type_id == $basic_account->id) {


                    $posts = $chart_of_account->posts_without_pagination(null, $as_of_date);

                    if ($chart_of_account->is_credit_balance()) {

                        if ($last_post == null) {

                            $chart_of_account->credit_balance =
                                ChartOfAccount::account_format($chart_of_account->current_balance);
                            $chart_of_account->raw_credit_balance =  $chart_of_account->current_balance;
                        } else {
                            $chart_of_account->credit_balance =  $last_post->formattedPostBalance;
                            $chart_of_account->raw_credit_balance =  $last_post->post_balance;
                        }
                    } else {

                        if ($last_post == null) {


                            $chart_of_account->debit_balance =
                                ChartOfAccount::account_format($chart_of_account->current_balance);
                            $chart_of_account->raw_debit_balance =  $chart_of_account->current_balance;
                        } else {

                            $chart_of_account->debit_balance =  $last_post->formattedPostBalance;
                            $chart_of_account->raw_debit_balance =  $last_post->post_balance;
                        }
                    }

                    $trial_balance[$basic_account->id]['accounts'][$chart_of_account->id] = $chart_of_account->toArray();
                }
            }
        }


        foreach ($trial_balance as $basic_account_type_id => $value) {
            if ($value['accounts'] == null) {
                unset($trial_balance[$basic_account_type_id]);
            }
        }


        return $trial_balance;
    }

    public function get_trial_balance_with_subcategories($as_of_date)
    {

        $charts_of_accounts  =  ChartOfAccount::for_company($this->id);
        $subcategories  =  CompanyAccountType::for_company($this->id);


        // print_r($subcategories->get()->toArray());

        // return;
        $basic_accounts      =    BasicAccountType::orderBy('name')->get();

        foreach ($basic_accounts as  $basic_account) {




            $trial_balance[$basic_account->id]['basic_account'] = $basic_account->toArray();
            $i = 0;
            foreach ($charts_of_accounts->get() as $chart_of_account) {
                $i++;
                if ($chart_of_account->basic_account_type_id == $basic_account->id) {


                    $last_post = $chart_of_account->last_post($as_of_date);

                    if ($chart_of_account->is_credit_balance()) {

                        if ($last_post == null) {

                            $chart_of_account->credit_balance =
                                ChartOfAccount::account_format($chart_of_account->current_balance);
                            $chart_of_account->raw_credit_balance =  $chart_of_account->current_balance;
                        } else {
                            $chart_of_account->credit_balance =  $last_post->formattedPostBalance;
                            $chart_of_account->raw_credit_balance =  $last_post->post_balance;
                        }
                    } else {

                        if ($last_post == null) {


                            $chart_of_account->debit_balance =
                                ChartOfAccount::account_format($chart_of_account->current_balance);
                            $chart_of_account->raw_debit_balance =  $chart_of_account->current_balance;
                        } else {

                            $chart_of_account->debit_balance =  $last_post->formattedPostBalance;
                            $chart_of_account->raw_debit_balance =  $last_post->post_balance;
                        }
                    }

                    $trial_balance[$basic_account->id]['accounts'][$chart_of_account->id] = $chart_of_account->toArray();
                }
            }
        }


        foreach ($trial_balance as $basic_account_type_id => $value) {
            if ($value['accounts'] == null) {
                // unset($trial_balance[$basic_account_type_id]);
            }
        }


        return $trial_balance;
    }




    public function get_trial_balance($from = null, $as_of_date)
    {
        $charts_of_accounts  =  ChartOfAccount::for_company($this->id);
        $basic_accounts      =    BasicAccountType::orderBy('name')->get();

        foreach ($basic_accounts as  $basic_account) {

            $trial_balance[$basic_account->id]['basic_account'] = $basic_account->toArray();
            $i = 0;
            foreach ($charts_of_accounts->get() as $chart_of_account) {
                $i++;
                if ($chart_of_account->basic_account_type_id == $basic_account->id) {


                    $last_post = $chart_of_account->last_post($from, $as_of_date);

                    if ($chart_of_account->is_credit_balance()) {

                        if ($last_post == null) {

                            $chart_of_account->credit_balance =
                                ChartOfAccount::account_format($chart_of_account->current_balance);
                            $chart_of_account->raw_credit_balance =  $chart_of_account->current_balance;
                        } else {
                            $chart_of_account->credit_balance =  $last_post->formattedPostBalance;
                            $chart_of_account->raw_credit_balance =  $last_post->post_balance;
                        }
                    } else {

                        if ($last_post == null) {


                            $chart_of_account->debit_balance =
                                ChartOfAccount::account_format($chart_of_account->current_balance);
                            $chart_of_account->raw_debit_balance =  $chart_of_account->current_balance;
                        } else {

                            $chart_of_account->debit_balance =  $last_post->formattedPostBalance;
                            $chart_of_account->raw_debit_balance =  $last_post->post_balance;
                        }
                    }

                    $trial_balance[$basic_account->id]['accounts'][$chart_of_account->id] = $chart_of_account->toArray();
                }
            }
        }


        foreach ($trial_balance as $basic_account_type_id => $value) {
            if (@$value['accounts'] == null) {
                unset($trial_balance[$basic_account_type_id]);
            }
        }


        return $trial_balance;
    }
}
