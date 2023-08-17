<?php

namespace v2\Jobs\Jobs;

use controller;
use MIS;
use Mailer;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendOrderPaymentNoticeEmail implements ContractsJob
{
    use TraitsJob;
    
    public $subscription;

    public function __construct()
    {
    }


    public function setUpWith($subscription)
    {
        $this->subscription = $subscription;
        return $this;
    }

    public function getEmailContent()
    {
        
        return (object) array(
            'subject' => "Email Subject",
            'body' => "Email Body"
        );
    }


    public  function execute()
    {
        $user = $this->subscription->user;
        $subject = $this->getEmailContent()->subject;
        $body = $this->getEmailContent()->body;        
        $mailer = new Mailer();
        return $mailer->sendMail(
            $user->email,
            $subject,
            $body,
            $user->fullname
        );

        
    }
}
