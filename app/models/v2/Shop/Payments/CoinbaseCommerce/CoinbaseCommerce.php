<?php

namespace v2\Shop\Payments\CoinbaseCommerce;

use MIS;
use Config;
use Session;
use Exception;
use SiteSettings;
use CoinbaseCommerce\Webhook;
use CoinbaseCommerce\ApiClient;
use CoinbaseCommerce\Resources\Charge;
use CoinbaseCommerce\Resources\Checkout;
use Redirect;
use v2\Shop\Contracts\PaymentMethodInterface;

/**
 * 
 */
class CoinbaseCommerce implements PaymentMethodInterface
{
    public $name = 'coinbase_commerce';
    private $payment_type;
    private $mode;

    function __construct()
    {

        $settings = SiteSettings::find_criteria('coinbase_commerce_keys')->settingsArray;

        $this->mode = $settings['mode']['mode'];

        $this->api_keys =  $settings[$this->mode];

        $apiClientObj = ApiClient::init($this->api_keys['secret_key']);
        $apiClientObj->setTimeout(6);
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


    public function goToGateway()
    {

        $payment_details = json_decode($this->order->payment_details, true);

        $checkout_url = $payment_details['checkout_url'];
        Redirect::to($checkout_url);
    }



    public function getPaymentLink()
    {
        $payment_details = json_decode($this->order->payment_details, true);
        return $payment_details['checkout_url'];
    }



    public function paymentStatus()
    {
    }


    public function reVerifyPayment()
    {

        $payment_details = json_decode($this->order->payment_details, true);

        $exploded_callback_url = explode("/", $payment_details['checkout_url']);
        $charge_id = end($exploded_callback_url);
        $charge = Charge::retrieve($charge_id);

        $payment_received = [];
        $local_currencies = [];
        foreach ($charge['payments'] as $key => $payment) {

            if (strtolower($payment['status']) != "confirmed") {
                continue;
            }

            $payment_received[] = $payment['value']['local']['amount'];
            $local_currencies[$payment['value']['local']['currency']] = $payment['value']['local']['currency'];
        }





        $expected_amount = $payment_details['custom']['local_price']['amount'];
        $expected_currency = $payment_details['custom']['local_price']['currency'];

        //currency mismatch
        if ((count($local_currencies) > 1)  || $expected_currency != end($local_currencies)) {
            return false;
        }





        if (round(array_sum($payment_received), 2) < round($expected_amount, 2)) {
            return false;
        }


        $result = $charge;

        $confirmation = ['status' => true];
        return compact('result', 'confirmation');
    }





    public function verifyPayment()
    {
        /**
         * To run this example please read README.md file
         * Past your Webhook Secret Key from Settings/Webhook section
         * Make sure you don't store your Secret Key in your source code!
         */
        $secret = $this->api_keys['webhook_secret'];
        $headerName = 'X-Cc-Webhook-Signature';
        $headers = getallheaders();
        $signraturHeader = isset($headers[$headerName]) ? $headers[$headerName] : null;
        $payload = trim(file_get_contents('php://input'));

        try {
            $event = Webhook::buildEvent($payload, $signraturHeader, $secret);

            ///
            //check that it is charge:confirmed
            if ($event->type != 'charge:confirmed') {
                return false;
            }



            $payment_details = $this->order->PaymentDetailsArray;
            $result = $event;
            $confirmation = ['status' => true];
            return compact('result', 'confirmation');

            $myfile = fopen("coinbase.txt", "w") or die("Unable to open file!");
            fwrite($myfile, ($event->addresses));
            fwrite($myfile, ($event->id));
            fwrite($myfile, ($event->type));

            http_response_code(200);
            echo sprintf('Successully verified event with id %s and type %s.', $event->id, $event->type);
        } catch (\Exception $exception) {
            http_response_code(400);
            echo 'Error occured. ' . $exception->getMessage();
        }
    }


    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }



    public function amountPayable()
    {
        $amount = $this->order->total_price();
        $amount = $this->order->AmountInUSD();
        return $amount;
    }

    private function makeOneTimePayment()
    {




        $payment_method = $this->name;
        $order_ref = $this->order->generateOrderID();
        $amount = $this->amountPayable();


        $callback_param = http_build_query([
            'item_purchased' => $this->order->name_in_shop,
            'order_unique_id' => $this->order->id,
        ]);


        $domain = Config::domain();
        if ($_ENV['APP_ENV'] == 'local') {
            $callback_url = "https://domain.com/shop/callback?$callback_param";
        } else {
            $callback_url = "{$domain}/shop/callback?$callback_param";
        }


        $user = $this->order->Buyer;
        $code = \Config::currency('code');

        $payment_details = [
            'gateway' => $this->name,
            'ref' => $order_ref,
            'order_unique_id' => $this->order->id,
            'item_purchased' => $this->order->name_in_shop,
            'email' => $user->email,
            'phone' => $user->phone ?? '09134567891',
            // 'currency' => $code,
            'amount' => $amount,
            'currency' => "USD",
            'fullname' => $user->fullname,
            "invoice_id" => $this->order->InvoiceID
        ];

        $project_name = \Config::project_name();

        $order_detail = $this->order->order_detail();


        $checkoutData = [
            'name' => $order_ref,
            'description' => " Order from $project_name",
            'pricing_type' => 'fixed_price',
            'local_price' => [
                'amount' =>  $this->amountPayable(),
                'currency' => strtoupper($code),
                'currency' => "USD",
            ],
            "metadata" => $payment_details,
            "requested_info" => [],
        ];


        // $chargeObj = Checkout::create($checkoutData);
        $chargeObj = Charge::create($checkoutData);


        $payment_details['checkout_url'] = "$chargeObj->hosted_url";
        $payment_details['custom'] = $checkoutData;

        $this->order->setPayment($payment_method, $payment_details);

        return $this;
    }

    public function makeSubscriptionPayment()
    {
        Session::putFlash("danger", "$this->name is unable to process subscription(Automatic) based payment.");

        $this->order->setPayment($payment_method, $payment_details);
        return $this;
    }

    public function initializePayment()
    {
        $actions = [
            'one_time' => 'makeOneTimePayment',
            'subscription' => 'makeSubscriptionPayment',
        ];

        $method = $actions[$this->payment_type];
        return $this->$method();
    }

    public function attemptPayment()
    {


        if ($this->order->is_paid()) {
            throw new Exception("This Order has been paid with {$this->order->payment_method}", 1);
        }


        if ($this->order->payment_method != $this->name) {
            throw new Exception("This Order is not set to use {$this->name} payment method", 1);
        }

        $payment_details = json_decode($this->order->payment_details, true);

        return $payment_details;
    }
}
