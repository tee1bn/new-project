<?php

namespace v2\Shop\Payments;

use Exception, SiteSettings, Session;
use v2\Shop\Contracts\OrderInterface;

/**
 * 
 */
class Paystack
{
	public $name = 'paystack';
	private $mode;
	public $order;
	public $api_keys;
	public $payment_type;
	public $shop;

	function __construct()
	{

		$settings = SiteSettings::paystack_keys();

		$this->mode = $settings['mode']['mode'];


		$this->api_keys =  $settings[$this->mode];

		//initate my keys and all
	}



	public function setShop($shop)
	{
		$this->shop = $shop;
		return $this;
	}

	public function setPaymentType($payment_type)
	{
		$this->payment_type = $payment_type;
		return $this;
	}

	public function reVerifyPayment()
	{

		return $this->verifyPayment();
	}



	public function verifyPayment()
	{



		$payment_details = json_decode($this->order->payment_details, true);
		$reference = $payment_details['ref'];

		$result = array();
		//The parameter after verify/ is the transaction reference to be verified
		$url = "https://api.paystack.co/transaction/verify/$reference";

		$secret_key = $this->api_keys['secret_key'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			[
				"Authorization: Bearer $secret_key"
			]
		);
		$request = curl_exec($ch);
		curl_close($ch);

		if (!$request) {
			// var_dump($request);
			Session::putFlash("danger", "1. We could not complete your payment.");
			return false;
		}

		$result = json_decode($request, true);



		if ($result['data']['status'] != 'success') {
			Session::putFlash("danger", "2. We could not complete your payment.");
			return false;
		}




		if ($this->amountPayable()  > $result['data']['amount']) {

			Session::putFlash("danger", "3. We could not complete your payment.<br>
			{$this->amountPayable()}  -- {$result['data']['amount']}");
			return false;
		}



		if (strtolower($result['data']['currency']) != strtolower($payment_details['currency'])) {
			Session::putFlash("danger", "4. we could not complete your payment.");
			return false;
		}



		// the transaction was successful, you can deliver value
		/* 
		@ also remember that if this was a card transaction, you can store the 
		@ card authorization to enable you charge the customer subsequently. 
		@ The card authorization is in: 
		@ $result['data']['authorization']['authorization_code'];
		@ PS: Store the authorization with this email address used for this transaction. 
		@ The authorization will only work with this particular email.
		@ If the user changes his email on your system, it will be unusable
		*/


		$confirmation = ['status' => true];

		return compact('result', 'confirmation');
	}

	public function setOrder(OrderInterface $order)
	{
		$this->order = $order;
		return $this;
	}


	public function amountPayable()
	{
		$amount = 100 * $this->order->total_price();

		return $amount;
	}


	public function initializePayment()
	{

		$payment_method = $this->name;

		$order_ref = $this->order->generateOrderID();

		$amount = $this->amountPayable();


		$user = $this->order->user;

		$payment_details = [
			'gateway' => $this->name,
			'ref' => $order_ref,
			'order_unique_id' => $this->order->id,
			'name_in_shop' => $this->order->name_in_shop,
			'email' => $user->email,
			'currency' => 'NGN',
			'amount' => $amount,
			'custom_fields' => [
				[
					'display_name' => "Full Name",
					'variable_name' => 'name',
					'value' => $user->fullname,
				],
				[
					'display_name' => "Phone",
					'variable_name' => 'phone',
					'value' => $user->phonenumber,
				],
			],
			'success_url' => "",
			'failure_url' => "",
		];

		$this->order->setPayment($payment_method, $payment_details);

		return $this;
	}

	public function attemptPayment()
	{


		if ($this->order->is_paid()) {
			throw new Exception("This Order has been paid with {$this->order->payment_details}", 1);
		}


		if ($this->order->payment_method != $this->name) {
			throw new Exception("This Order is not set to use paystack payment menthod", 1);
		}


		$payment_details = json_decode($this->order->payment_details, true);


		$payment_details['api_keys'] = $this->api_keys['public_key'];


		return $payment_details;
	}
}
