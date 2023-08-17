<?php

use  v2\Models\Campaign;
use Illuminate\Support\Str;
use v2\Personalization\Personalization;
use Illuminate\Database\Capsule\Manager as DB;



/**
 * 
 */
class CampaignExecution extends controller
{

	function __construct()
	{
	}



	public function record_email_open($campaign_id, $recipient_email)
	{
		$campaign = Campaign::find($campaign_id);
		if ($campaign == null) {
			return;
		}

		$track = $campaign->opens_track;
		$track[$recipient_email] = 1;

		$campaign->update([
			"opens" => count($track),
			"opens_track" => ($track),
		]);
	}

	public function email_campaigns()
	{

		$campaigns = Campaign::Published()->where('type', 'email')->OnGoing()->latest()->get();


		if ($campaigns->isEmpty()) {
			return;
		}

		$this->exec_email_campaign($campaigns->first()->id);
	}

	public function email_campaigns2()
	{

		$campaigns = Campaign::Published()->where('type', 'email')->OnGoing()->oldest()->get();


		if ($campaigns->isEmpty()) {
			return;
		}

		$this->exec_email_campaign($campaigns->first()->id);
	}


	public function exec_email_campaign($campaign_id)
	{
		$campaign = Campaign::find($campaign_id);


		$mailer = new Mailer;
		$domain = Config::domain();
		$project_name = Config::project_name();
		$sentby = "noreply@convertbetcodes.com";

		$per_page = 100;

		if ($campaign->sql_query == '') {


			$emails = explode(",", $this->recipients);
			$total = count($emails);

			$page = $campaign->current_offset ?? 1;
			$skip = (($page - 1) * $per_page);

			$recipients = User::whereIn('email', $emails)->take($per_page)->skip($skip)->get();

			$all_users = User::whereIn('email', $emails)->get()->keyBy('username');
		} else {

			$query = $campaign->sql_query;
			$total = $campaign->rows();

			$page = $campaign->current_offset ?? 1;
			$skip = (($page - 1) * $per_page);


			if ($campaign->BindsArray == []) {

				$recipients = collect(DB::select("$query LIMIT $per_page OFFSET $skip "));
			} else {

				$recipients = collect(DB::select("$query LIMIT $per_page OFFSET $skip ", $campaign->BindsArray));
			}
			$all_users = User::whereIn('username', $recipients->pluck('username')->toArray())->get()->keyBy('username');
		}


		if ($campaign->no_of_recipient >= $total) {

			$campaign->update([
				'progress_status' => "completed"
			]);

			return;
		}



		foreach ($recipients as $key => $recipient) {
			$email = $recipient->email;

			$content = $campaign->content();
			$subject = $campaign->subject;

			$auth = $all_users[$recipient->username];

			$personalization = new Personalization;
			$content = $personalization->setUser($auth)
				->setContent($content)
				->personalise()
				->getOutput();

			$subject = $personalization->setUser($auth)
				->setContent($subject)
				->personalise()
				->getOutput();


			//add email open tracker code
			$content .= "<img src='$domain/campaign_execution/record_email_open/{$campaign->id}/$email'>";


			//client
			$mailer->sendMail(
				"{$email}",
				"$subject",
				$content,
				$recipient->firstname,
				"{$sentby}",
				"$project_name"
			);
		}

		$page++;
		$no_of_recipient = count($recipients) + $campaign->no_of_recipient;
		$campaign->update([
			'current_offset' => $page,
			'no_of_recipient' => $no_of_recipient,
		]);
	}
}
