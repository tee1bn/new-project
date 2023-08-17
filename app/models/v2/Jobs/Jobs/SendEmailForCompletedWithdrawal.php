<?php

namespace v2\Jobs\Jobs;

use MIS;
use Mailer;
use controller;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendEmailForCompletedWithdrawal implements ContractsJob
{
    use TraitsJob;
    public $withdrawal;

    public function __construct()
    {
    }

    public function sendToUser()
    {
        $project_name = \Config::project_name();
        $domain = \Config::domain();

        $user = $this->withdrawal->user;

        $controller = new controller;
        $withdrawal = $this->withdrawal;
        $receiver_content =  $controller->buildView('emails/completed_withdrawal', compact('withdrawal'), true, true);
        $mailer = new Mailer;

        return $mailer->sendMail(
            $user->email,
            "Check your recent payment ID:#{$this->withdrawal->id}",
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
