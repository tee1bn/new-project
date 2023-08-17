<?php

namespace v2\Jobs\Jobs;

use MIS;
use Mailer;
use controller;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendEmailForCommissionPaid implements ContractsJob
{
    use TraitsJob;
    public $commission;

    public function __construct()
    {
    }

    public function sendToUser()
    {
        $project_name = \Config::project_name();
        $domain = \Config::domain();


        $involved_accounts = $this->commission->involved_accounts;
        $line = $involved_accounts->where('credit', '>', 0)->first();
        $account = $line->chart_of_account;
        $user = $account->owner;




        $controller = new controller;
        $commission = $this->commission;
        $receiver_content =  $controller->buildView('emails/commission_paid', compact('commission'), true, true);
        $mailer = new Mailer;

        return $mailer->sendMail(
            $user->email,
            "You just earned! ",
            $receiver_content,
            $user->firstname
        );
    }

    public function sendToAdmin()
    {
    }

    public  function execute()
    {
        return $this->sendToUser();
    }
}
