<?php

namespace v2\Jobs\Jobs;


/* 


*/

use controller;
use MIS;
use Mailer;
use SubscriptionOrder;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendOrderFollowUpEmail implements ContractsJob
{
    use TraitsJob;

    public $order;



    public  function execute()
    {

        $today = date("Y-m-d");

        //checking that user has not paid then send follow up email on how to pay
        $recently_paid_order = SubscriptionOrder::where('user_id', $this->order->user_id)
            ->whereBetween('paid_at', ["$today 00:00:00", "$today 23:59:59"])
            ->whereRaw("paid_at > '{$this->order->updated_at}'")
            ->Paid()
            ->count();


        if ($this->order->is_paid() || $recently_paid_order > 0) {

            return true;
        }

        $user = $this->order->user;
        $order = $this->order;
        $subject = "$user->firstname your order is pending";
        $body = (new controller)->buildView('emails/order_follow_up', compact('order'), true, true);


        $mailer = new Mailer();
        return $mailer->sendMail(
            $user->email,
            $subject,
            $body,
            $user->fullname
        );
    }
}
