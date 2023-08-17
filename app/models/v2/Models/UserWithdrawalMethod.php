<?php

namespace v2\Models;

use SiteSettings;
use Illuminate\Database\Eloquent\Model as Eloquent;

class UserWithdrawalMethod extends Eloquent
{

	protected $fillable = [

		'user_id',	'method',	'details'
	];

	protected $table = 'users_withdrawals_methods';
	protected $connection = 'default';
	protected $appends = ['Display'];



	public  static $method_options = [
		/* 'bitcoin' => [
			'name' => 'Bitcoin',
			'class' => 'Bitcoin',
			'view' => 'withdrawal_methods/bitcoin',
			'display' => [
				'bitcoin_address' => 'Bitcoin Address'
			],
		],

		'ethereum' => [
			'name' => 'Ethereum',
			'class' => 'Ethereum',
			'view' => 'withdrawal_methods/ethereum',
			'display' => [
				'ethereum_address' => 'Ethereum Address'
			],
		], */

		'ngn_bank' => [
			'name' => 'NGN -Bank details',
			'class' => 'LocalBank',
			'view' => 'withdrawal_methods/ngn_bank',
			'display' => [
				'bank_id' => 'Bank ID',
				'bank' => 'Bank',
				'account_name' => 'Account Name',
				'account_number' => 'Account Number',
			],
		],


		'airtel_tigo' => [
			'name' => 'Airtel Tigo',
			'class' => '',
			'view' => 'withdrawal_methods/airtel_tigo',
			'display' => [
				'account_name' => 'Account Name',
				'mobile_number' => 'Mobile Number',
			],
		],


		'vodafone' => [
			'name' => 'Vodafone Cash',
			'class' => '',
			'view' => 'withdrawal_methods/vodafone',
			'display' => [
				'account_name' => 'Account Name',
				'mobile_number' => 'Mobile Number',
			],
		],


		'mtn_momo' => [
			'name' => 'MTN Momo',
			'class' => '',
			'view' => 'withdrawal_methods/mtn_momo',
			'display' => [
				'account_name' => 'Account Name',
				'mobile_number' => 'Mobile Number',
			],
		],



		/* 
		'paypal' => [
			'name' => 'Paypal',
			'class' => 'Paypal',
			'view' => 'withdrawal_methods/paypal',
			'display' => [

				'email_address' => 'Email Address'
			],
		],

		'skrill'=> [
			'name' => 'Skrill',
			'class' => 'Skrill',
			'view' => 'withdrawal_methods/skrill',
			'display' => [

							'email_address'=> 'Email Address'
					],
		],

		'payeer'=> [
			'name' => 'Payeer',
			'class' => 'Payeer',
			'view' => 'withdrawal_methods/payeer',
			'display' => [

							'payeer_id'=> 'Payeer ID'
			],
		], */
	];



	public function scopeApproved($query)
	{
		return $query;
	}


	public function getAccountHolderAttribute()
	{

		$settings = SiteSettings::find_criteria('paystack_keys')->settingsArray;

		if ($this->method != 'ngn_bank') {
			return "";
		}

		$details = $this->DetailsArray;

		if ($details['account_name'] != null) {
			return $details['account_name'];
		}


		$financial_bank = FinancialBank::find($details['bank_id']);

		$params = http_build_query([
			'account_number' =>  $details['account_number'] ?? null,
			'bank_code' =>   $financial_bank->code
		]);


		$secret_key = $settings['live']['secret_key'];

		$url = "https://api.paystack.co/bank/resolve?$params";
		$header = [
			"Authorization: Bearer $secret_key"
		];
		$response = \MIS::make_get($url, $header);

		$response = json_decode($response, true);


		$account_name = $response['data']['account_name'];
		return $account_name;
	}


	public function getDisplayAttribute()
	{

		$show = self::$method_options[$this->method];
		$compact = $this->details;
		$to_show  = $this->MethodDetails;

		$line = '';
		foreach ($to_show as $key => $label) {
			$value = $to_show[$key] ?? '';
			$key = str_replace("_", " ", $key);
			$line .= "<li>
			    				$key:<span>$value</span>
			    	 </li>";
		}

		$to_show['display'] = $line;
		return $to_show;
	}
	public function getMethodDetailsAttribute()
	{
		$details = $this->details;


		switch ($this->method) {

			case 'ngn_bank':

				$financial_bank = FinancialBank::find($details['bank_id']);

				$compact = [
					'bank' => $financial_bank->bank_name,
					// 'bank_code' => $financial_bank->code,
					'bank_id' => $financial_bank->id,
					'account_name' => $details['account_name'] ?? null,
					'account_number' => $details['account_number'],
				];

				$show = [
					'bank' => 'Bank',
					'account_name' => 'Account Holder',
					'account_number' => 'NUBAN',
				];

				// $showable = array_intersect_key($compact, $show);
				$compact['name'] = self::$method_options[$this->method]['name'];

				return ($compact);

				break;

			default:

				$details['name'] = self::$method_options[$this->method]['name'];
				return $details;


				break;
		}
	}


	public function adminComments()
	{
		$comments =   AdminComment::where('model_id', $this->id)->where('model', 'bank')->get();
		return $comments;
	}


	public function scopeForUser($query, $user_id)
	{
		return  $query->where('user_id', $user_id);
	}


	public static function for($user_id, $method)
	{
		$return  = self::where('user_id', $user_id)->where('method', $method)->first();

		return $return;
	}


	public function getDetailsAttribute($value)
	{
		if ($value == null) {
			return [];
		}

		return json_decode($value, true);
	}


	public function getDetailsArrayAttribute()
	{
		return $this->details;
	}


	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
}
