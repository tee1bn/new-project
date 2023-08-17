<?php


use Illuminate\Database\Eloquent\Model as Eloquent;

class AccountClosingAndReopeningRevamped extends Eloquent 
{
	
	protected $fillable = [
		'chart_of_account_id',	'open_or_close',	'user_id',	'balance',	'reason'	
	];
	
	protected $table = 'ac_account_closing_and_reopening';



	
	public static function get_state($chart_of_account_id)
	{
		return self::where('chart_of_account_id' , $chart_of_account_id)->latest()->first();

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

		return  ($state->open_or_close == 'open');
		

	}

}


















?>