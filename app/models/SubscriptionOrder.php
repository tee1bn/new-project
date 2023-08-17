<?php



use v2\Tax\Tax;
use  v2\Shop\Shop;
use v2\Classes\ExchangeRate;
use v2\Models\ConversionLog;

use  Filters\Traits\Filterable;
use  v2\Models\InvestmentPackage;
use v2\Shop\Contracts\OrderInterface;
use v2\Utilities\LowLimitNotification;
use v2\Jobs\Jobs\SendEmailForCommissionPaid;
use v2\Models\Wallet\Classes\AccountManager;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

class SubscriptionOrder extends Eloquent implements OrderInterface
{
	use Filterable;

	protected $fillable = [
		'plan_id',
		'type',  //unit|plan
		'payment_method',
		'payment_details',
		'expires_at',
		'payment_state',
		'payment_schedule',
		'user_id',
		'payment_proof',
		'price',
		'units',
		'plan_usage',
		'sent_email',
		'paid_at',
		'details',
		'rolled_over',
		'created_at'
	];

	protected $table = 'subscription_payment_orders';

	public $name_in_shop = 'packages';

	public  static $payment_types = [
		'paypal' => 'subscription',
		'coinpay' => 'one_time',
	];

	public	function supportsAPI()
	{
		return $this->details['integrations'] == 1;
	}

	public function getPlanUsagesAttribute()
	{
		$units = $this->type == 'plan' ? abs($this->units) : 0;
		return $this->plan_usage + $units + 0;
	}

	public function updateDetails(array $key_value_array)
	{
		$details = $this->details;

		$settings = array_merge($details, $key_value_array);
		$this->update([
			'details' => json_encode($settings)
		]);
	}


	public function hasUnit()
	{
		$details = $this->details;
		return $this->units > 0;
	}

	public function getPaymentScheduleArrayAttribute()
	{
		if ($this->payment_schedule == null) {
			return [];
		}

		$payment_schedule = json_decode($this->payment_schedule, true);

		return $payment_schedule;
	}

	public function getExpiryDateAttribute()
	{
		if ($this->expires_at != null) {
			return $this->expires_at;
		}

		$date_string = $this->paid_at;

		$validity = $this->details['expires_at'];
		$date = date("Y-m-d H:i:s", strtotime("$date_string +$validity days"));
		return $date;
	}

	public function scopeExpired($query, $date = null)
	{
		$date = $date ?? date("Y-m-d H:i:s");
		return $query->where('expires_at', '<', $date);
	}

	public function scopeNotExpired($query, $date = null)
	{
		$date = $date ?? date("Y-m-d H:i:s");
		return $query->where('expires_at', '>', $date);
	}

	public function scopeWithUnits($query)
	{
		return $query->where('units', '>', 0);
	}

	public function scopeActive($query, $date = null)
	{
		return $query->where('units', '>', 0);
	}

	public function is_expired()
	{
		if (strtotime($this->ExpiryDate) < time()) {
			return true;
		}
		return false;
	}


	public function fetchAgreement()
	{
		$shop = new Shop();
		$agreement = $shop->setOrder($this)->fetchAgreement();
		return $agreement;
	}

	public function getNotificationTextAttribute()
	{

		if ($this->expires_at == null) {
			return "";
		}

		$date = $this->ExpiryDate;
		$expiry_date = date("M j, Y", strtotime($date));

		$domain = Config::domain();
		$cancel_link = "$domain/shop/cancel_agreement";

		switch ($this->payment_state) {
			case 'manual':
				$note = "Till: $expiry_date";
				break;
			case 'automatic':

				$agreement_details = $this->fetchAgreement();
				$next_billing_date = date("M j, Y", strtotime($agreement_details['next_billing_date']));

				$today = strtotime(date("Y-m-d"));
				$next_billing = strtotime(date("Y-m-d", strtotime($agreement_details['next_billing_date'])));


				$note = "";

				if ($next_billing > $today) {
					$note .= MIS::generate_form([
						'order_unique_id' => $this->id,
						'item_purchased' => 'packages',
					], $cancel_link, 'Cancel Subscription', '', true);
				}

				$note .= "<br>Next Billing: $next_billing_date <br>";
				break;
			case 'cancelled':
				$note = "Till: $expiry_date";
				break;

			default:
				$note = "Till: $expiry_date";
				break;
		}

		return $note;
	}


	public function chargeConvesion($user, $conversion, $channel, $cost, $comment = "conversion events")
	{
		//record this line propery:later
		if ($this->type != 'unit') {
			return false;
		}

		if ($this->units <= 0) {
			return false;
		}


		DB::beginTransaction();
		try {


			$log = ConversionLog::logConversion(
				$user,
				$conversion,
				$this->id,
				"unit",
				$channel
			);

			$this->decrement("units", 1);

			//check if low on unit, send email
			DB::commit();

			//check if low on unit, send email
			$notification = new LowLimitNotification;
			$notification->setSubscription($this)
				->sendNoticeIfBalanceIsLow();

			return true;
		} catch (\Throwable $th) {
			DB::rollback();
			return false;
		}
	}


	public function scopePlanPricing($query)
	{
		return $query->where('type', 'plan');
	}

	public function scopeUnitPricing($query)
	{
		return $query->where('type', 'unit');
	}



	public function chargeConvesionByPlan($user, $conversion, $channel, $cost, $comment = "conversion events")
	{


		$no_of_home_events = $conversion->home_entries['summary']['no_of_entries'];
		$no_of_dest_entries = $conversion->dest_entries['summary']['no_of_entries'];

		$details = ($this->details);

		//check max event per booking
		$max_events_per_booking = $details['max_events_per_booking'] == "INF" ? INF : $details['max_events_per_booking'];
		if ($no_of_home_events > $max_events_per_booking) {
			throw new Exception("Max number of events:{$max_events_per_booking} exceeded.", 1);
			return false;
		}




		//record attempted bookies
		$attempted_bookies = $details['attempted_bookies'] ?? [];
		$destination_bookies = $details['destination_bookies'] ?? [];
		$c = $conversion->bookieKeys();

		/* $home_bookie = $c['home']['bookie'];
		$destination_bookie = $c['destination']['bookie']; 
		*/


		$details = $this->details;

		$no_of_bookies = $details['no_of_bookies'] == "INF" ? INF : $details['no_of_bookies'];

		$home_bookie = Input::get('from') == '' ? $c['home_array']['bookie'] : Input::get('from');
		$destination_bookie = Input::get('to') == '' ? $c['destination_array']['bookie'] : Input::get('to');

		//check allowed bookies
		if (
			$details['no_of_bookies'] != 'INF' &&
			(!isset($details['attempted_bookies'][$destination_bookie]) ||  !isset($details['attempted_bookies'][$home_bookie]))

		) {
			$already_attempted_bookies = implode(",", array_keys($attempted_bookies));
			throw new Exception("Only $already_attempted_bookies is allowed.", 1);
			return false;
		}


		//check allowed destination bookies
		if ($details['no_of_destination_bookies'] !== null  &&  !isset($details['destination_bookies'][$destination_bookie])) {
			$already_destination_bookies = implode(",", array_keys($destination_bookies));
			throw new Exception("Conversion to only:$already_destination_bookies is allowed.", 1);
			// throw new Exception("Conversion $home_bookie $destination_bookie.", 1);
			return false;
		}



		//check integrations
		$integrations =
			[
				'a' => [
					'source' => "api",
				],

				'inline' => [
					'source' => "inline widget",
				],
				'embed' => [
					'source' => "embeded widget",
				],
				'link' => [
					'source' => "conversion link",
				],
			];

		if (array_key_exists($channel, $integrations)  && ($details['integrations'] != true)) {
			throw new Exception("Please subscribe to a plan that supports access to integration.", 1);
			return false;
		}


		if ($this->type != 'plan') {
			throw new Exception("Please contact support.", 1);
			return false;
		}


		//check no of conversions		
		$no_of_units = $details['no_of_units'] == "INF" ? INF : $details['no_of_units'];
		/* 		if ($this->hasUnit() && $no_of_units < INF) {
			return false;
		}
 		*/


		DB::beginTransaction();
		try {

			$this->increment("plan_usage", 1);
			$log = ConversionLog::logConversion($user, $conversion, $this->id, "plan", $channel);

			//check if close to expiry, send email

			DB::commit();


			//check if close to expiry, send email
			$notification = new LowLimitNotification;
			$notification->setSubscription($this)
				->sendNoticeIfBalanceIsLow();

			return true;
		} catch (\Throwable $th) {
			DB::rollback();
			return false;
		}

		return true;
	}


	public function scopePaid($query)
	{
		return $query->where('paid_at', '!=', null);
	}

	public function scopeUnPaid($query)
	{
		return $query->where('paid_at', '=', null);
	}




	public function tax_breakdown()
	{
		$tax = new Tax;
		$tax_payable  =	$tax->setTaxSystem('general_tax');
		return $tax->setProduct($this->payment_plan)->setTaxStyleOnPrice('tax_exclusive')
			->calculateApplicableTax()->amount_taxable;
	}



	public  function invoice()
	{
		$detail = $this->details;
		$payment_detail = $this->PaymentDetailsArray;

		$tax = [];
		// $this->tax_breakdown();

		$rate = $payment_detail['amount'];
		$qty = 1;
		$amount = $qty *  $rate;

		/* $unit_tax = $tax['breakdown']['tax_payable'];
		$line_tax = $unit_tax * $qty;
		$print_tax = "$line_tax 
		<br><small> {$tax['breakdown']['total_percent_tax']}%  {$tax['pricing']} </small>";

		$before_tax = $tax['breakdown']['before_tax'] * $qty;
		$after_tax = $tax['breakdown']['total_payable'] * $qty; */


		$summary = [
			[
				'item' => "$this->TransactionID",
				'description' => "{$detail['name']} Package ",
				'rate' => $rate,
				'qty' => $qty,
				'amount' => $amount,
				/* 				'print_tax' => $print_tax,
				'line_tax' => $line_tax,
				'after_tax' => $after_tax,
				
				*/
				'before_tax' => $amount,
				'tax' => $tax,
			]
		];


		$total_before_tax = collect($summary)->sum('before_tax');
		/* 	$total_tax = collect($summary)->sum('line_tax');
		$total_after_tax = collect($summary)->sum('after_tax'); */

		$lines =  [
			'subtotal' => [
				'name' => 'Sub Total ',
				'value' => $total_before_tax,
			],
			/* 	'tax' => [
				'name' => 'Tax',
				'value' => $total_tax,
			],
			'grand_total' => [
				'name' => 'Grand Total',
				'value' => $total_after_tax,
			],

			'total_payable' => [
				'name' => 'Total Payable',
				'value' => $total_after_tax,
			], 
			
			*/
		];
		/* 
		$extra_lines = [

			'total_before_tax' => [
				'name' => 'Sub Total Before Tax',
				'value' => $total_before_tax,
			],

			'total_after_tax' => [
				'name' => 'Sub Total Before Tax',
				'value' => $total_after_tax,
			],
		];

		$full_lines = array_merge($lines, $extra_lines);
 */

		$subtotal = [
			'subtotal' => null,
			'lines' => $lines,
			'total' => null,
			// 'full_lines' => $full_lines,
		];



		$invoice = [
			'order_id' => $this->TransactionID,
			'invoice_id' => $this->TransactionID,
			'order_date' => $this->created_at,
			'payment_status' => $this->paymentstatus,
			'summary' => $summary,
			'subtotal' => $subtotal,
		];

		return $invoice;
	}


	public  function getInvoice()
	{
		$controller = new \controller;
		$order = $this;
		$remove_mle_detail = false;
		$view  =	$controller->buildView('composed/invoice', compact('order', 'remove_mle_detail'));


		return $view;
		// $view = "I am here"	;

		$mpdf = new Mpdf([
			'margin_left' => 5,
			'margin_right' => 5,
			'margin_top' => 10,
			'margin_bottom' => 20,
			'margin_header' => 10,
			'margin_footer' => 10
		]);


		$src = Config::logo();
		$company_name = \Config::project_name();
		$mpdf->AddPage('P');
		$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("{$company_name}");
		$mpdf->SetAuthor($company_name);
		// $mpdf->SetWatermarkText("{$company_name}");
		$mpdf->watermarkImg($src, 0.1);
		$mpdf->showWatermarkText = true;
		$mpdf->watermark_font = 'DejaVuSansCondensed';
		$mpdf->watermarkTextAlpha = 0.2;
		$mpdf->SetDisplayMode('fullpage');

		$date_now = (date('Y-m-d H:i:s'));

		$mpdf->SetFooter("Date Generated: " . $date_now . " - {PAGENO} of {nbpg}");



		// return  "$view";
		// return;		
		$mpdf->WriteHTML($view);
		$mpdf->Output("invoice#$order->id.pdf", \Mpdf\Output\Destination::INLINE);
	}




	public function getTransactionIDAttribute()
	{
		$payment_details = json_decode($this->payment_details, true);
		$ref = $payment_details['ref'] ?? '';
		$gateway = $payment_details['gateway'] ?? '';
		$currency = $payment_details['currency'] ?? '';
		$amount = $payment_details['amount'] ?? '';
		$method = "{$ref}-{$amount}
					<br>
					<span class='badge badge-primary'>{$gateway}-{$currency}</span>";

		return $method;
	}



	public function is_first_upgrade_for_user($plan_id = 2)
	{
		$first_order = self::where('user_id', $this->user_id)->where('plan_id', $plan_id)->Paid()->oldest('paid_at')->first();


		return $first_order->id == $this->id;
	}


	public static function extractIdFromRef($ref)
	{

		$order_id = strtolower(str_ireplace(["cbc"], "", $ref));
		$order_id = explode("s", $order_id)[0];


		return $order_id;
	}

	public function getresumeLinkAttribute()
	{
		$shop = new Shop();
		$link = $shop->setOrder($this)
			->setPaymentMethod($this->PaymentDetailsArray['gateway'], false)
			->getPaymentLink();

		return $link;
	}
	public function getreverifyLinkAttribute()
	{
		$domain = Config::domain();
		$param = http_build_query([
			'item_purchased' => $this->name_in_shop,
			'order_unique_id' => $this->id,
			'payment_method' =>  $this->payment_method,
		]);


		return "$domain/shop/re_confirm_order/?$param";
	}



	public function give_affiliate_commission()
	{
		$start_date = "2022-12-01";

		if (time() < strtotime($start_date)) {
			return;
		}


		$payment_details = ($this->paymentDetailArray);
		$payment_currency = strtolower($payment_details['currency']);
		$affiliate_structure = SiteSettings::getAffiliateCommissionStructure();

		$commission_structure = $affiliate_structure['structure'][$payment_currency]["levels"] ?? null;

		$user = $this->user;

		$period_of_payment = $affiliate_structure['period_of_payment'];
		$period_ago = date("Y-m-d 00:00:00", strtotime("-$period_of_payment"));

		//if user is beyond payment period
		if (strtotime($user->created_at) < strtotime($period_ago)) {
			return;
		}


		//if commission is available for payment currency
		if ($commission_structure == null) {
			$this->give_referral_bonus($this->user->Sponsor);
			return;
		}



		//get uplines
		$uplines = $user->referred_members_uplines(2);
		// $uplines = $user->getUplines(3);

		foreach ($commission_structure as $level => $bonus) {
			if (!isset($uplines[$level])) {
				continue;
			}


			//check amount
			$commission = $bonus['commission_in_percent'] * 0.01 * $this->price;

			if ($commission == 0) {
				continue;
			}


			$receiver = $uplines[$level];
			//ensure receiver is qualified for compensational bonuses
			//only admin 
			if ($receiver->id == 1) {
				// continue;
			}


			if (!$receiver->can_received_compensation($this)) {
				$this->give_referral_bonus($receiver);
				continue;
			}

			$identifier = "U#{$receiver->id}#L{$level}O#{$this->id}";
			$comment = "L{$level} {$bonus['commission_in_percent']}% of {$payment_details['amount']}";


			$line =  AccountManager::payAffiliateCommission([
				'receiver' => $receiver,
				'amount' => $commission,
				'identifier' => $identifier,
				'narration' => $comment,
				'currency' => $payment_currency,
			]);


			if (!$line) {
				continue;
			}

			$commission = $line;
			SendEmailForCommissionPaid::dispatch(compact('commission'));
		}
	}



	public function getTotalUnitsAttribute()
	{

		$details = $this->details;
		$no_of_units = $details['no_of_units'] ?? 0;
		$no_of_units = in_array($no_of_units, [null, 0, '', 'INF']) ? 0 : $no_of_units;

		$bonus_units = $details['bonus_units'] ?? 0;
		$bonus_units = in_array($bonus_units, [null, 0, '', 'INF']) ? 0 : $bonus_units;



		$given_units = $no_of_units + $bonus_units;

		return $given_units;
	}

	public function mark_unpaid()
	{

		if (!$this->is_paid()) {
			return;
		}
		$this->update([
			'paid_at' => null,
			'expires_at' => null,
		]);
	}


	public function mark_paid()
	{

		if ($this->is_paid()) {
			Session::putFlash('info', 'Order Already Marked as completed');
			return false;
		}


		DB::beginTransaction();
		try {

			// $validity = $this->details['expires_at']=="INF"? $this->details['expires_at']:  ;
			$details = $this->details;

			$validity = in_array($details['expires_at'], [null, 0, '', 'INF']) ? null : $details['expires_at'];
			$no_of_units = in_array($details['no_of_units'], [null, 0, '', 'INF']) ? null : $details['no_of_units'];

			$expires_at = $validity == null ?  null : date("Y-m-d H:i:s", strtotime("+$validity days"));

			if ($no_of_units == null) {
				$type = "plan";
			} else {
				$type = "unit";
			}

			$rollable_order = $this->user->hasRollableUnits();
			if ($rollable_order) {

				$rollover_settings = SiteSettings::SubRollOverSettings();
				$grace_period = $rollover_settings['grace_period'];

				// $rollover_expires_at =  date("Y-m-d H:i:s", strtotime("+$grace_period days"));
				$rollover_expires_at =  date("Y-m-d H:i:s", strtotime("$expires_at -1 day"));
				$rollable_order->update([
					'rolled_over' => 1,
					'expires_at' => $rollover_expires_at,
				]);
			}




			$this->update([
				'paid_at' => date("Y-m-d H:i:s"),
				'expires_at' => $expires_at,
				'units' => $this->TotalUnits,
				'type' => $type,
			]);

			// $this->give_value();

			$this->give_affiliate_commission();
			DB::commit();
			Session::putFlash('success', "Order #{$this->id} marked as completed");
			return true;
		} catch (Exception $e) {
			DB::rollback();
			print_r($e->getMessage());
			Session::putFlash('danger', 'Order could not mark as completed');
		}

		return false;
	}

	public function getRollableByAttribute()
	{
		$rollable_by = date("D d M Y", strtotime("$this->expires_at"));
		return $rollable_by;
	}


	private function give_value()
	{
		$user = $this->user;
		// $this->give_referral_bonus();
		// $this->send_subscription_confirmation_mail();
	}



	public function give_referral_bonus(\User $receiver)
	{
		$referral_settings = SiteSettings::getReferralBonusSettings();
		$period_of_payment = $referral_settings['period_of_payment'];
		$period_ago = date("Y-m-d 00:00:00", strtotime("-$period_of_payment"));



		//if user is beyond payment period
		if (strtotime($receiver->created_at) < strtotime($period_ago)) {
			return;
		}

		$unit = $referral_settings['bonus_units'];
		//give this user x unit

		$identifier = "RB#{$receiver->id}#{$this->user_id}";

		DB::beginTransaction();
		try {

			self::create([
				'plan_id' => 1,  //unit|plan
				'type' => 'unit',  //unit|plan
				'payment_method' => 'referral',
				'payment_details' => json_encode([]),
				'expires_at' => $this->expires_at,
				'payment_state' =>  null,
				'user_id' => $receiver->id,
				'price' => 0,
				'units' => $unit,
				'paid_at' => $this->paid_at,
				'identifier' => "$identifier",
				'details' => [],
				'rolled_over' => 1,
			]);
			DB::commit();
		} catch (\Throwable $th) {
			DB::rollback();
			//throw $th;
		}


		return;
	}

	public function is_paid()
	{

		return (bool) ($this->paid_at != null);
	}


	public function upload_payment_proof($file)
	{

		$directory 	= 'uploads/images/payment_proof';
		$handle  	= new Upload($file);

		if (explode('/', $handle->file_src_mime)[0] == 'image') {

			$handle->Process($directory);
			$original_file  = $directory . '/' . $handle->file_dst_name;

			(new Upload($this->payment_proof))->clean();
			$this->update(['payment_proof' => $original_file]);
		}
	}


	public function getpaymentDetailArrayAttribute()
	{
		return  json_decode($this->payment_details, true);
	}

	public function after_payment_url()
	{
		$domain = Config::domain();

		$id = MIS::dec_enc('encrypt', $this->id);

		$guest_url = "$domain/shop/delivery/$id";
		$auth_url = "$domain/user/dashboard";

		$controller = new controller;

		if ($controller->auth()) {

			return $auth_url;
		} else {

			return $guest_url;
		}
	}

	public function getdetailsAttribute($value)
	{
		if ($value == null) {
			return [];
		}

		return json_decode($value, true);
	}


	public function payment_plan()
	{
		return $this->belongsTo('SubscriptionPlan', 'plan_id');
	}


	public static function user_has_pending_order($user_id, $plan_id)
	{
		return (bool) self::where('user_id', $user_id)
			->where('plan_id', $plan_id)
			->where('paid_at', '=', null)->count();
	}



	public function total_qty()
	{
		return 1;
	}

	public function total_tax_inclusive()
	{

		$breakdown = $this->payment_plan->PriceBreakdown;

		$tax = [
			'price_inclusive_of_tax' => $breakdown['total_payable'],
			'price_exclusive_of_tax' => $breakdown['set_price'],
			'total_sum_tax' => $breakdown['tax'],
		];

		return $tax;
	}
	public function total_price()
	{
		return $this->price;
	}

	public function order_detail()
	{
		return $this->details;
	}



	public function AmountInUSD()
	{
		$detail =  $this->details;


		$exchange = new ExchangeRate;
		$priced_currency = $detail['priced_currency'];
		$code = "USD";

		$conversion = $exchange->setFrom($priced_currency)
			->setTo($code)
			->setAmount($detail['price'])
			->getConversion();

		$converted_price = $conversion['destination_value'];


		$this->update([
			'price' => $converted_price
		]);

		return (float) $converted_price;
	}


	public function generateOrderID()
	{

		$substr = substr(strval(time()), 9);
		$order_id = "CBC{$this->id}S{$substr}";

		return $order_id;
	}

	public function cancelAgreement()
	{
		$order = self::where('id', $this->id)->Paid()->where('payment_state', 'automatic')->first();

		if ($order == null) {
			return;
		}

		$shop = new Shop();
		$agreement_details = $this->fetchAgreement();
		$expires_at = date("Y-m-d", strtotime($agreement_details['next_billing_date']));

		DB::beginTransaction();
		try {

			$shop->setOrder($this)->cancelAgreement();

			$this->update([
				'payment_state' => 'cancelled',
				'expires_at' => $expires_at,
			]);

			DB::commit();
			Session::putFlash("success", "{$this->package_type} Billing cancelled successfully");
		} catch (Exception $e) {
			DB::rollback();
		}
	}

	public function getPaymentDetailsArrayAttribute()
	{
		if ($this->payment_details == null) {
			return [];
		}

		$payment_details = json_decode($this->payment_details, true);

		return $payment_details;
	}

	public function update_agreement_id($agreement_id)
	{
		$array = $this->PaymentDetailsArray;
		$array['agreement_id'] = $agreement_id;

		$this->update([
			'payment_details' => json_encode($array)
		]);
	}

	public function setPayment($payment_method, array $payment_details)
	{

		$this->update([
			'payment_method' => $payment_method,
			'payment_state' => @$payment_details['payment_state'],
			'payment_details' => json_encode($payment_details),
		]);

		return $this;
	}


	public function setPaymentBreakdown(array $payment_breakdown, $order_id = null)
	{
		$this->update([
			'order_id' => $order_id,
			'payment_breakdown' => json_encode($payment_breakdown),
			'amount_payable' => $payment_breakdown['total_payable']['value'],
		]);

		return $this;
	}

	public function getPaymentBreakdownArrayAttribute()
	{
		return json_decode($this->payment_breakdown, true);
	}

	public function getBuyerAttribute()
	{
		if ($this->user_id != null) {

			return $this->user;
		}

		return $this->customer;
	}

	public function calculate_vat()
	{
		$result = [
			'value' => 0,
			'percent' => 0,
		];


		return $result;
	}



	public  function create_order($cart)
	{
		extract($cart);

		$payment_plan = SubscriptionPlan::find($plan_id);
		$new_payment_order = self::create([
			'plan_id'  	=> $plan_id,
			'user_id' 		=> $user_id,
			'price'   		=> $price,
			'details'		=> json_encode($payment_plan),
		]);

		return $new_payment_order;
	}


	public function getpaymentstatusAttribute()
	{
		if ($this->paid_at != null) {

			$label = '<span class="badge badge-success">Paid</span>';
		} else {
			$label = '<span class="badge badge-danger">Unpaid</span>';
		}

		return $label;
	}

	public function getExpiryStatusAttribute()
	{
		if (!$this->is_paid()) {
			return "";
		}


		if ($this->is_expired()) {
			$label = '<span class="badge badge-danger">expired</span>';
		} else {
			$label = '<span class="badge badge-xs badge-success">active</span>';
		}

		return $label;
	}

	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
}
