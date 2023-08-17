<?php

namespace v2\Jobs\Jobs;

use controller;
use MIS;
use Mailer;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendEmailForNewBankAccountCreation implements ContractsJob
{
    use TraitsJob;
    public $user;

    public function __construct()
    {
    }


    public function sendToUser()
    {
        $project_name = \Config::project_name();
        $domain = \Config::domain();
        $user = $this->user;
        
        $controller = new controller;

        $body = $controller->buildView('emails/new_account', compact('user')) ;
        $mailer = new Mailer();
        
        return $mailer->sendMail(
            $user->email,
            "Welcome Email",
            $body,
            $user->fullname
        );
    }

    public function sendToAdmin()
    {

        $project_name = \Config::project_name();
        $domain = \Config::domain();

        $settings = \SiteSettings::site_settings();
        $noreply_email = $settings['noreply_email'];
        $support_email = $settings['support_email'];
        $ticket = $this->message->supportTicket;

        $support_email_address = "$support_email";

        $client_email_message = "Dear Admin, Please respond to this support ticket on the admin <br>
	                            From:<br>
	                            $ticket->customer_name,<br>
	                            $ticket->customer_email,<br>
	                            $ticket->customer_phone,<br>
	                            Ticket ID: $ticket->code<br>
	                            <br>
	                             ";

        $client_email_message .= $this->message->message;

        $client_email_message = MIS::compile_email($client_email_message);

        $mailer = new Mailer();

        return  $mailer->sendMail(
            "$support_email_address",
            "$project_name Support - Ticket ID: $ticket->code",
            $client_email_message,
            "Support"
        );
    }

    public  function execute()
    {
        return $this->sendToUser();
    }
}
