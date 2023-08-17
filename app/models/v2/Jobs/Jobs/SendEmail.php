<?php

namespace v2\Jobs\Jobs;

use MIS;
use Mailer;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendEmail implements ContractsJob
{
    use TraitsJob;
    public $user;

    public function __construct()
    {
    }


    public function setUpWith($user)
    {
        return $this;
    }




    public  function execute()
    {
        //


        // print_r($this->user->email);
        try {
            $mailer = new Mailer;

            $controller = new \controller;

            $user = $this->user;
            $content = $controller->buildView('meeting/ourfile', compact('user'), false, true);


            return $mailer->sendMail($this->user->email, "Showing Doyin the Queue ", $content);
        } catch (\Exception $th) {
            print_r($th->getMessage());
        }

        // return false;

        // return  $mailer->sendMail($to, $subject, $body);
    }
}
