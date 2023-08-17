<?php

namespace v2\Shop\Payments\Accrue;

use Config;
use GuzzleHttp\Client;
use Exception, SiteSettings, Session;
use v2\Shop\Contracts\OrderInterface;

/**
 * 
 */
class Accrue
{
    public $name = 'accrue';
    private $mode;
    public $order;
    public $api_keys;
    public $shop;
    public $payment_type;

    function __construct()
    {

        $settings =  [
            "test" => [
                "public_key" => $_ENV["ACCRUE_TEST_PUBLIC_KEY"],
                "secret_key" => $_ENV["ACCRUE_TEST_SECRET_KEY"],
                "endpoint" => "https://staging.api.useaccrue.com/cashramp/api/graphql",
            ],

            "live" => [
                "public_key" => $_ENV["ACCRUE_LIVE_PUBLIC_KEY"],
                "secret_key" => $_ENV["ACCRUE_LIVE_SECRET_KEY"],
                "endpoint" => "https://api.useaccrue.com/cashramp/api/graphql",
            ],

            "mode" => $_ENV["ACCRUE_MODE"],
            "available" => $_ENV["ACCRUE_AVAILABLE"],
        ];


        $this->mode = $settings['mode'];
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


        $graphQLquery = <<<GQL
query {
    merchantPaymentRequest(reference: "$reference") {
        id
        paymentType
        reference
        redirectUrl
                    status

        merchantCustomer {
            id
        }
        p2pPayment {
            id
            status
            exchangeRate
            payment {
                amount
                fee
            }
        }
        p2pPaymentMethod {
            id
            value
        }   
    }
}
GQL;
        $client = new Client(['timeout'  => 0]);
        $response = $client->request(
            'POST',
            $this->api_keys['endpoint'],
            [
                'headers' => [
                    'Authorization' => "Bearer {$this->api_keys['secret_key']}"
                ],

                'json' => [
                    'query' => $graphQLquery
                ]
            ],
        );

        $content = ($response->getBody()->getContents());
        $response_array  = json_decode($content, true);


        if ($response_array['data']['merchantPaymentRequest']['status'] != 'completed') {
            return false;
        }

        if ($response_array['data']['merchantPaymentRequest']['p2pPayment']['status'] != 'completed') {
            return false;
        }


        //payment is confirmed payment
        //give value        
        $result = $response_array;
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
        $amount = $this->order->total_price();
        $amount = $this->order->AmountInUSD();
        return $amount;
    }


    public function initializePayment()
    {

        $payment_method = $this->name;

        $order_ref = $this->order->generateOrderID();

        $amount = $this->amountPayable();

        $user = $this->order->Buyer;

        // $code = \Config::currency('code');

        $code = explode("_", $this->order->payment_method)[1];
        $country = collect($this->getCountries())->keyBy('currency')->toArray()[strtoupper($code)];


        $user_email = strtolower(trim($user->email));

        $payment_details = [
            'gateway' => $this->order->payment_method,
            'ref' => $order_ref,
            'order_unique_id' => $this->order->id,
            'name_in_shop' => $this->order->name_in_shop,
            'email' => $user_email,
            'phone' => $user->phone ?? '09134567891',
            'currency' => 'USD',
            'amount' => $amount,
        ];

        $redirect_url = $this->order->reverifyLink;

        $graphQLquery = <<<GQL

mutation {
    initiateHostedPayment(
        paymentType: deposit,
        amount: $amount,
        currency: usd,
        countryCode: "{$country['id']}",
        reference: "$order_ref",
        redirectUrl: "{$redirect_url}",
        firstName: "$user->firstname",
        lastName: "{$user->lastname}",
        email: "$user_email"
    ) {
        id
        hostedLink
    }
}        
GQL;
        $client = new Client(['timeout'  => 0]);
        $response = $client->request(
            'POST',
            $this->api_keys['endpoint'],
            [
                'headers' => [
                    'Authorization' => "Bearer {$this->api_keys['secret_key']}"
                ],

                'json' => [
                    'query' => $graphQLquery
                ]
            ],
        );

        $content = ($response->getBody()->getContents());
        $response_array  = json_decode($content, true);
        $payment_details['approval'] = $response_array['data']['initiateHostedPayment'];
        /* 
        echo "<pre>";
        echo $this->api_keys['endpoint'];
        print_r($response_array); */

        $this->order->setPayment($payment_method, $payment_details);

        return $this;
    }

    public function goToGateway()
    {
        \Redirect::to($this->getPaymentLink());
    }

    public function getPaymentLink()
    {
        $payment_details = json_decode($this->order->payment_details, true);
        return $payment_details['approval']['hostedLink'];
    }



    public function getCountries()
    {
        $json = '
[{
    "id": "CM",
    "name": "Cameroon",
    "currency": "XAF"
  },
  {
    "id": "GH",
    "name": "Ghana",
    "currency": "GHS"
  },
  {
    "id": "KE",
    "name": "Kenya",
    "currency": "KES"
  },
  {
    "id": "NG",
    "name": "Nigeria",
    "currency": "NGN"
  },
  {
    "id": "ZA",
    "name": "South Africa",
    "currency": "ZAR"
  },
  {
    "id": "TZ",
    "name": "Tanzania",
    "currency": "TZS"
  },
  {
    "id": "UG",
    "name": "Uganda",
    "currency": "UGX"
  }
]';
        return json_decode($json, true);
    }

    public function attemptPayment()
    {


        if ($this->order->is_paid()) {

            \Session::putFlash("info", "This Order has been paid with");
            throw new Exception("This Order has been paid with {$this->order->payment_details}", 1);
        }


        if ($this->order->payment_method != $this->name) {
            throw new Exception("This Order is not set to use {$this->name} payment method", 1);
        }
        $payment_details = json_decode($this->order->payment_details, true);

        $payment_details['checkout_url'] = $payment_details['approval']['hostedLink'];
        return $payment_details;
    }
}
