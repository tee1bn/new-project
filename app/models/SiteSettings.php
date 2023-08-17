<?php


use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Capsule\Manager as DB;

class SiteSettings extends Eloquent
{

	protected $fillable = ['criteria',	'settings', 'description', 'name'];

	protected $table = 'site_settings';

	public static function SubRollOverSettings()
	{
		$settings = [
			"rollable_within_x_days" => "5 days",
			"grace_period" => "7 days", //period to reuse rolled units
		];

		return $settings;
	}

	public static function getReferralBonusSettings()
	{
		$settings = [
			'bonus_units' => 2, //2 units
			'period_of_payment' => "12 months"
		];

		return $settings;
	}



	public static function getAffiliateCommissionStructure()
	{
		$structure = [
			"ngn" => [
				"threshold" => "10000",
				"min_withdrawals" => "1000",
				"withdrawal_fee" => "100",
				"levels" => [
					0 => [
						"commission_in_percent" => 0,
					],
					1 => [
						"commission_in_percent" => 50,
					]
				]
			],
			"ghs" => [
				"threshold" => "100",
				"min_withdrawals" => "50",
				"levels" => [
					0 => [
						"commission_in_percent" => 0,
					],
					1 => [
						"commission_in_percent" => 50,
					],
					2 => [
						"commission_in_percent" => 0,
					],
				]
			],


		];

		$period_of_payment = "12 months";

		return get_defined_vars();
	}


	public static function getMinNoOfEventToBeginCharging()
	{
		return 100;
	}
	public static function getRate()
	{
		$settings =
			[
				'a' => [
					'chunk' => 1000,
					'cost_per_chunk' => 1,
					'source' => "api",
				],
				'u' => [
					'chunk' => 1000,
					'cost_per_chunk' => 1,
					'source' => "web",
				],

				'inline' => [
					'chunk' => 1000,
					'cost_per_chunk' => 1,
					'source' => "inline widget",
				],
				'embed' => [
					'chunk' => 1000,
					'cost_per_chunk' => 1,
					'source' => "embeded widget",
				],
				'link' => [
					'chunk' => 1000,
					'cost_per_chunk' => 1,
					'source' => "conversion link",
				],

			];

		return $settings;
	}
	/* 
	public static function getReferralCommission()
	{
		$settings = [
			'month' => 3,
			'percent' => 10,
		];

		return $settings;
	} */



	public static function getPlans()
	{
		/* 
		name
		price
		expires_at
		no_of_bookies
		max_events_per_booking
		no_of_units
		integrations
		no_ads,
		il=individual limited; iu=individual unlimited,
		paths,
		visibility:hide|null
		no_of_destination_bookies
		*/
		$blue_print = [
			/* Freemium */
			["Free",  0,  INF, INF, 2, INF,  false, false, 'iu', null, 'hide'],


			// ["API-KEY", 20,  INF, INF, INF, 0, true, true, 'api_key'],


			/* Individuals */
			["Al", 1,  20, INF, INF, 12, false, true, 'il'],

			["Bl", 2,  20, INF, INF, 30, false, true, 'il'],
			["Cl", 4,  30, INF, INF, 60, false, true, 'il'],
			["Dl", 10,  30, INF, INF, 200, true, true, 'il'],

			//unlimited conversions for x-days
			/* 
			["B", 2,  4, INF, INF, INF, false, true, 'iu'],
			["C", 4,  8, INF, INF, INF, false, true, 'iu'],
			*/



			["D", 10, 30, INF, INF, INF, false, true, 'iu'],

			/* Business------ */
			/* ["BA", 24,  30, 4, INF, INF, true, true, 'bu', '12'],
			["BB", 50,  30, 5, INF, INF, true, true, 'bu', '20'],
			*/
			["BC", 60,  30, 6, INF, INF, true, true, 'bu', '30'],

			["BF", 2000,  30, INF, INF, INF, true, true, 'bu', INF, 'hide'],
			// ["Bet Shop-BS", 10,  30, INF, INF, INF, false, true, 'bu', INF, null, 1],


			// /*limited by conversion  */
			["BLA", 50,  30, INF, INF, 1300,  true, true, 'bl', INF],
			["BLB", 100,  30, INF, INF, 2700,  true, true, 'bl', INF],
			["BLC", 200,  30, INF, INF, 5500,  true, true, 'bl', INF],
			["BLD", 400,  30, INF, INF, 11000,  true, true, 'bl', INF],
			// ["BLE", 1000,  30, INF, INF, 27500,  true, true, 'bl', INF],

		];

		$priced_currency = SiteSettings::pricedCurrency();
		$plans = [];
		$i = 1;
		foreach ($blue_print as $key => $plan) {
			$plan = [
				"name" => $plan[0],
				"price" => $plan[1],
				"expires_at" => $plan[2],
				"no_of_bookies" => $plan[3],
				"max_events_per_booking" => $plan[4],
				"no_of_units" => $plan[5],
				"integrations" => $plan[6],
				// "no_ads" => $plan[7],
				"priced_currency" => "$priced_currency",
				"group" => $plan[8][0] == 'b' ? "business" : "individual",
				"id" => $i,
				"paths" => $plan[9] ?? null,
				"hide" => isset($plan[10]) && ($plan[10] == 'hide'),
				"no_of_destination_bookies" => $plan[11] ?? NULL,
			];
			$plans[$i]  = $plan;
			$i++;
		}

		$benefits = [
			// "max_events_per_booking" =>  "Max events per booking",
			"no_of_bookies" =>  "Allowed bookies",
			"no_of_units" =>  "conversions",
			// "no_ads" =>  "No Ads",
			"integrations" =>  "API, Widget",
			"no_of_destination_bookies" =>  "Destination bookies ",
		];

		// print_r($blue_print);
		return compact('plans', 'benefits');
	}



	public static function pricedCurrency()
	{
		return "USD";
	}
	public static function PlanIsBetshop($plan)
	{
		return $plan['no_of_destination_bookies'] === 1;
	}


	public static function PlanHasAllowedBookie($plan)
	{
		return $plan['no_of_bookies'] != INF;
	}


	public static function AvailableCurrencies()
	{
		$currencies = [
			"AUD" => "A$",
			"CAD" => "C$",
			"GBP" => "£",
			"GHS" => "GH&#8373",
			"KES" => "KSh",
			"NGN" => "₦",
			"RWF" => "RWF",
			"TZS" => "TSh",
			"UGX" => "USh",
			"USD" => "$",
			"XAF" => "XAF",
			"XOF" => "XOF",
			"ZAR" => "R",
		];

		array_walk($currencies, function (&$item, $key) {

			return $item = [
				"code" => $key,
				"html_code" => "$item",
			];
		});

		return collect($currencies);
	}



	public function getsettingsArrayAttribute()
	{

		if ($this->settings == null) {

			return [];
		}

		return  json_decode($this->settings, true);
	}



	public static function find_criteria($criteria)
	{

		if (is_array($criteria)) {

			return self::whereIn('criteria', $criteria)->get();
		}
		return self::where('criteria', $criteria)->first();
	}


	public static function payment_gateway_settings()
	{
		$payments_settings_keys = [
			/*'paypal_keys',
		 'perfect_money_keys',
		  'manual_transfer' ,
		 'livepay_keys',
		 'coinpay_keys',
		 'bank_transfer',
		 */
			'accrue_keys',
			'paystack_keys',
			'dpopay_keys',
			'manual_chipper_cash',
			'uba_dom_usd',
			'manual_paypal',
			'manual_mobile_money_ghana',
			'flutter_wave_keys',
			'coinbase_commerce_keys',
		];

		sort($payments_settings_keys);

		return self::whereIn('criteria', $payments_settings_keys)->get();
	}



	public function delete_document($key)
	{
		$doc = json_decode($this->settings, true);
		$tobe_deleted = ($doc[$key]);
		unset($doc[$key]);

		DB::beginTransaction();

		try {


			$this->update(['settings' => json_encode($doc)]);

			DB::commit();
			Session::putFlash("success", "{$tobe_deleted['label']} Deleted Successfully");
			return true;
		} catch (Exception $e) {
			DB::rollback();
			Session::putFlash("danger", "Could not delete ");
			return false;
		}





		header("content-type:application/json");

		echo json_encode(compact('response'));
	}



	public  function upload_documents($files)
	{
		$directory = 'uploads/admin/documents';


		$documents = json_decode($this->settings, true);

		if ($documents == "") {
			$documents = [];
		}


		$i = 0;



		DB::beginTransaction();

		try {

			foreach ($files as $label => $file) {

				$handle = new Upload($file);



				$file_type = explode('/', $handle->file_src_mime)[0];

				if (($handle->file_src_mime == 'application/pdf') || ($file_type == 'image')) {

					$handle->file_new_name_body = "{$this->name} $label";

					$handle->Process($directory);
					$file_path = $directory . '/' . $handle->file_dst_name;

					$new_file[$i]['files'] = $file_path;
					$new_file[$i]['label'] = $label;
					$new_file[$i]['category'] = $file['category'];



					array_unshift($documents, $new_file[$i]);
				} else {

					Session::putFlash("danger", "only .pdf format allowed");
					throw new Exception("Only Pdf is allowed ", 1);
				}
				$i++;
			}



			$this->update([
				'settings' => json_encode($documents)
			]);

			DB::commit();
			Session::putFlash("success", "Documents Uploaded Successfully");
		} catch (Exception $e) {
			DB::rollback();
			Session::putFlash("danger", "Documents Uploaded Failed.");
		}

		return ($documents);
	}




	public static function documents_settings()
	{
		$settings = json_decode(self::where('criteria', 'documents_settings')->first()->settings, true);
		return $settings;
	}

	public static function site_settings()
	{
		$settings = json_decode(self::where('criteria', 'site_settings')->first()->settings, true);
		return $settings;
	}

	public static function commission_settings()
	{
		$settings = json_decode(self::where('criteria', 'commission_settings')->first()->settings, true);
		return $settings;
	}

	public static function low_limit_settings()
	{
		$settings = array(
			'low_limit_minimum_days' => 5,
			'low_unit_minimum_percent' => 15,
			'minimum_notice_interval' => '12 hours',
		);
		return $settings;
	}



	public static function pools_settings()
	{
		$settings = json_decode(self::where('criteria', 'pools_settings')->first()->settings, true);
		return $settings;
	}





	public static function company_account_details()
	{
		$settings = json_decode(self::where('criteria', 'admin_bank_details')->first()->settings, true);
		return $settings;
	}



	public static function coinpay_keys()
	{
		$settings = json_decode(self::where('criteria', 'coinpay_keys')->first()->settings, true);
		return $settings;
	}




	public static function paystack_keys()
	{
		$settings = json_decode(self::where('criteria', 'paystack_keys')->first()->settings, true);
		return $settings;
	}



	public static function sms_api_keys()
	{
		$settings = json_decode(self::where('criteria', 'sms_api_keys')->first()->settings, true);
		return $settings;
	}




	/*

	public function getsettingsAttribute($value)
    {

		if ($value == null) {
			
			return json_encode([]);			
		}
		// return json_decode($value , true);
	
        return json_encode( json_decode($value ,true));
    }
*/
}
