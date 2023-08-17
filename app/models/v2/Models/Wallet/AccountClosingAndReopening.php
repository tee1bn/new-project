<?php

namespace v2\Models\Wallet;


use Illuminate\Database\Eloquent\Model as Eloquent;

class AccountClosingAndReopening extends Eloquent
{

	protected $fillable = [
		'chart_of_account_id',	'financial_period_id', 'open_or_close',	'user_id',	'balance',	'reason'
	];

	protected $table = 'ac_account_closing_and_reopening';
	protected $connection = 'wallet';


	/**
		This is the startdate i.e date the attached account got opened
	 */
	public function open_start_date($chart_of_account_id)
	{
		$history =  self::where('chart_of_account_id', $chart_of_account_id)->latest();
		$last_history = $history->get()->toArray()[0];
		$second_to_last_history = $history->get()->toArray()[1];

		if ($last_history['open_or_close'] == 'open') {
			$startdate = date("Y-m-d H:i:s", strtotime($last_history['created_at']));
			$startdate =  $last_history['created_at'];
		} else {

			$startdate = date("Y-m-d H:i:s", strtotime($second_to_last_history['created_at']));
			$startdate =  $second_to_last_history['created_at'];
		}

		return $startdate;
	}



	/**
		This is the enddate i.e date the attached account got opened
	 */
	public function close_end_date($chart_of_account_id)
	{
		$history =  self::where('chart_of_account_id', $chart_of_account_id)->latest();
		$last_history = $history->get()->toArray()[0];
		$second_to_last_history = $history->get()->toArray()[1];

		if ($last_history['open_or_close'] == 'close') {
			$enddate = date("Y-m-d H:i:s", strtotime($last_history['created_at']));
		} else {

			$enddate = date("Y-m-d");
		}

		return $enddate;
	}





	public static function get_state($chart_of_account_id)
	{
		return self::where('chart_of_account_id', $chart_of_account_id)->latest()->first();
	}


	/**
	 * [get_state_boolean description]
	 * @param  [type] $chart_of_account_id []
	 * @return [boolean]    [true if open and false if closed]
	 */
	public static function get_state_boolean($chart_of_account_id)
	{
		$state = self::get_state($chart_of_account_id);
		if ($state == null) {
			return true;
		}

		return ($state->open_or_close == 'open');
	}
}
