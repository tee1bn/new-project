<?php

namespace v2\Jobs\Jobs;

use MIS;
use Mailer;
use v2\Jobs\Traits\Job as TraitsJob;
use v2\Jobs\Contracts\Job as ContractsJob;

class SendEmailFor2faUpdate implements ContractsJob
{
    use TraitsJob;
    public $user;

    /**
     * The DB job model in the jobs table
     *
     * @var
     */
    public $db_job;

    public function __construct()
    {
    }

    public function sendToUser()
    {
        $project_name = \Config::project_name();
        $domain = \Config::domain();

        $settings = \SiteSettings::site_settings();
        $support_email = $settings['support_email'];

        $user = $this->user;
        $state = $user->has_2fa_enabled() ? "ON" : "OFF";

        $date = date("M j, Y h:i a", strtotime($this->db_job->created_at));


        $body = "<p>Hi <b>$user->fullname</b>, </p>
        <p>Your $project_name Account 2-Factor Authentication has been turned $state successfully.</p>
        <p>Date: $date </p>
        <p>
            If this change was not carried out by you, or you have any questions or concerns, 
            please <a href='mailto:$support_email'>contact customer</a> support.
        </p>
        ";

        $body = MIS::compile_email($body);

        $mailer = new Mailer();
        return $mailer->sendMail(
            $user->email,
            "2FA Settings Changed",
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
