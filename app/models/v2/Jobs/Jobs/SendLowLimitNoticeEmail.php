<?php

namespace v2\Jobs\Jobs;

use controller;
use MIS;
use Mailer;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendLowLimitNoticeEmail implements ContractsJob
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

    public function getUnitBody()
    {
        $balance = $this->subscription->units;
        return (object) array(
            'subject' => "Your Conversion Unit is low",
            'body' => "Your conversion unit is <b>$balance</b>, and low."
        );
    }

    public function getPlanBody()
    {
        $expiry_date = date("D M d, Y", strtotime($this->subscription->expires_at));
        return (object) array(
            'subject' => "Your Subscription is expiring soon",
            'body' => "Your current subscription is expiring soon by <b>$expiry_date</b>."
        );
    }


    public  function execute()
    {

        $subscription = $this->subscription;
        $user = $this->subscription->user;
        $controller = new controller;
        $notice_message = "";

        if ($subscription->type == 'unit') {
            $subject = $this->getUnitBody()->subject;
            $notice_message = $this->getUnitBody()->body;
        }

        if ($subscription->type == 'plan') {
            $subject = $this->getPlanBody()->subject;
            $notice_message = $this->getPlanBody()->body;
        }

        $body = $controller->buildView('emails/low_unit_notification', compact('user', 'notice_message'));
        $mailer = new Mailer();
        return $mailer->sendMail(
            $user->email,
            $subject,
            $body,
            $user->fullname
        );
    }
}
