<?php

namespace v2\Models\Wallet;


use Exception;
use v2\Models\Wallet\BasicAccountType;
use Illuminate\Database\Eloquent\Model as Eloquent;

class AcBooksSettings extends Eloquent
{

	protected $fillable = ['start_date',	'end_date', 'company_id', 'status', 'open_or_close'];

	protected $table = 'ac_books_settings';
	protected $connection = 'wallet';




	public function is_closed()
	{
		return ($this->open_or_close == 'close');
	}


	public function is_open()
	{
		return (!$this->is_closed());
	}





	public static function open_for_financial_period($company_id, $period_id, $account_type, $accountant_id)
	{

		$company_chartsofaccount = ChartOfAccount::where('company_id', $company_id)
			->where('basic_account_type_id', $account_type)->get();


		$period  =  self::find($period_id);
		$period->update(['open_or_close' => 'open']);

		print_r($company_chartsofaccount->count());


		foreach ($company_chartsofaccount as $chart_of_account) {
			// echo $chart_of_account['id'];


			$chart_of_account->current_balance = 0;
			$chart_of_account->save();


			AccountClosingAndReopening::create([

				'chart_of_account_id'	=> $chart_of_account['id'],
				'open_or_close'		=> 'open',
				'user_id'			=> $accountant_id,
				'balance'			=> $chart_of_account['current_balance'],
				'financial_period_id'			=> $period_id,
				'reason'			=> "Open for Financial Year {$period->start_date} to {$period->end_date} ",
			]);
		}
	}



	public static function close_for_financial_period($company_id, $period_id, $account_type, $accountant_id)
	{

		$closables_accounts_ids = BasicAccountType::closables()->get()->pluck('id')->toArray();
		$company_chartsofaccount = ChartOfAccount::where('company_id', $company_id)
			->whereIn(
				'basic_account_type_id',
				$closables_accounts_ids
			)
			->get();


		$period  =  self::find($period_id);


		$period->update(['open_or_close' => 'close']);

		print_r($company_chartsofaccount->count());


		foreach ($company_chartsofaccount as $chart_of_account) {
			echo "<br>";
			// echo $chart_of_account['id'];

			AccountClosingAndReopening::create([

				'chart_of_account_id'	=> $chart_of_account['id'],
				'open_or_close'		=> 'close',
				'user_id'			=> $accountant_id,
				'balance'			=> $chart_of_account['current_balance'],
				'financial_period_id'			=> $period_id,
				'reason'			=> "Close for Financial Year {$period->start_date} to {$period->end_date} ",
			]);
		}
	}


	public static function 	activated_period()
	{
		return self::where('status', 1)->first();
	}

	public function closed_periods()
	{
		return	self::where('open_or_close', 'close');
	}

	public function 	open_periods()
	{
		return	self::where('open_or_close', 'open');
	}


	public static function 	set_financial_period($period_id)
	{
		self::where('status', '1')->update(['status' => 0]);
		$period = self::find($period_id);
		$period->update(['status' => 1]);
	}


	public function getActivationStatusAttribute()
	{
		if ($this->status ==  1) {
			return "<span class='label label-xs label-success'>Activated</span>";
		} else {
			return "<span class='label label-xs label-danger'>Not Activated</span>";
		}
	}

	public function getClosedStatusAttribute()
	{
		if ($this->open_or_close == 'close') {
			return "<span class='label label-xs label-danger'>Closed</span>";
		} else {
			return "<span class='label label-xs label-success'>Open</span>";
		}
	}


	public static  function company($company_id)
	{
		return self::where('company_id', $company_id);
	}

	public static function create_financial_period($company_id, $start_date, $end_date)
	{


		try {

			$period = self::create([
				'start_date' => $start_date,
				'end_date' => $end_date,
				'company_id' => $company_id
			]);

			return $period;
		} catch (Exception $e) {
		}
		return false;
	}



	public function current_fiancial_year($value = '')
	{
	}
}
