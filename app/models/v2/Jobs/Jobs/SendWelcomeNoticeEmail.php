<?php

namespace v2\Jobs\Jobs;

use controller;
use MIS;
use Mailer;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendWelcomeNoticeEmail implements ContractsJob
{
    use TraitsJob;
    public $user;

    public function __construct()
    {
    }


    public function setUpWith($user)
    {
        $this->user = $user;
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
        
        $subject = $this->getEmailContent()->subject;
        $body = $this->getEmailContent()->body;        
        $mailer = new Mailer();
        return $mailer->sendMail(
            $this->user->email,
            $subject,
            $body,
            $this->user->fullname
        );

        
    }
}
