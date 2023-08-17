<?php

namespace v2\Shop\Payments\DpoPay;

use Config;
use Redirect;
use SimpleXMLElement;
use Exception, SiteSettings, Session;
use v2\Shop\Contracts\OrderInterface;

/**
 * 
 */
class DpoPay
{
    public $name = 'dpopay';
    private $mode;
    public $api_url = 'https://secure.3gdirectpay.com/API/v6/';
    // public $api_url = 'https://secure1.sandbox.directpay.online/API/v6/';
    public $checkout_url = 'https://secure.3gdirectpay.com/dpopayment.php';

    function __construct()
    {

        $settings = SiteSettings::find_criteria('dpopay_keys')->settingsArray;

        $this->mode = $settings['mode']['mode'];

        $this->api_keys =  $settings[$this->mode];

        $this->CompanyToken = "8D3DA73D-9D7F-4E09-96D4-3D44E7A83EA3";
        //initate my keys and all
    }
    protected $createResponses = [
        '000' => 'Transaction created',
        '801' => 'Request missing company token',
        '802' => 'Company token does not exist',
        '803' => 'No request or error in Request type name',
        '804' => 'Error in XML',
        '902' => 'Request missing transaction level mandatory fields - name of field',
        '904' => 'Currency not supported',
        '905' => 'The transaction amount has exceeded your allowed transaction limit, please contact: support@directpay.online',
        '906' => 'You exceeded your monthly transactions limit, please contact: support@directpay.online',
        '922' => 'Provider does not exist',
        '923' => 'Allocated money exceeds payment amount',
        '930' => 'Block payment code incorrect',
        '940' => 'CompanyREF already exists and paid',
        '950' => 'Request missing mandatory fields - name of field',
        '960' => 'Tag has been sent multiple times',
    ];
    protected $createResponseCodes;
    protected $verifyResponses = [
        '000' => 'Transaction Paid',
        '001' => 'Authorized',
        '002' => 'Transaction overpaid/underpaid',
        '801' => 'Request missing company token',
        '802' => 'Company token does not exist',
        '803' => 'No request or error in Request type name',
        '804' => 'Error in XML',
        '900' => 'Transaction not paid yet',
        '901' => 'Transaction declined',
        '902' => 'Data mismatch in one of the fields - field (explanation)',
        '903' => 'The transaction passed the Payment Time Limit',
        '904' => 'Transaction cancelled',
        '950' => 'Request missing transaction level mandatory fields – field (explanation)',
    ];


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


        //give value        
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

        return $amount;
    }

    public function createToken(array $payment_details)
    {

        $now = date('Y/m/d H:i');
        $customerPhone = preg_replace('/[^0-9]/', '', $payment_details['customerPhone'] ?? "");

        $success_url = htmlentities($payment_details['success_url']);
        $failure_url = htmlentities($payment_details['failure_url']);

        $user = $this->order->buyer;

        $success_url = htmlspecialchars($success_url, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $failure_url = htmlspecialchars($failure_url, ENT_XML1 | ENT_QUOTES, 'UTF-8');


        $postXml = <<<POSTXML
        <?xml version="1.0" encoding="utf-8"?>
        <API3G>
            <CompanyToken>{$this->CompanyToken}</CompanyToken>
            <Request>createToken</Request>
            <Transaction>
                <PaymentAmount>{$payment_details['amount']}</PaymentAmount>
                <PaymentCurrency>{$payment_details['currency']}</PaymentCurrency>
                <CompanyRef>{$payment_details['ref']}</CompanyRef>
                <customerDialCode></customerDialCode>
                <customerZip></customerZip>
                <customerCountry></customerCountry>
                <customerFirstName>{$user->firstname}</customerFirstName>
                <customerLastName>{$user->lastname}</customerLastName>
                <customerAddress></customerAddress>
                <customerCity></customerCity>
                <customerPhone></customerPhone>
                <RedirectURL></RedirectURL>
                <BackURL></BackURL>
                <customerEmail>{$payment_details['email']}</customerEmail>
            </Transaction>
            <Services>
                <Service>
                    <ServiceType>3854</ServiceType>
                    <ServiceDescription>order#{$payment_details['ref']} units for conversion </ServiceDescription>
                    <ServiceDate>{$now}</ServiceDate>
                </Service>
            </Services>
        </API3G>
        
POSTXML;



        $created = false;
        $cnt     = 0;

        while (!$created && $cnt < 10) {
            try {
                $curl = curl_init();
                curl_setopt_array(
                    $curl,
                    array(
                        CURLOPT_URL            => $this->api_url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING       => "",
                        CURLOPT_MAXREDIRS      => 10,
                        CURLOPT_TIMEOUT        => 30,
                        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST  => "POST",
                        CURLOPT_POSTFIELDS     => $postXml,
                        CURLOPT_HTTPHEADER     => array(
                            "cache-control: no-cache",
                        ),
                    )
                );
                $response = curl_exec($curl);
                curl_close($curl);
            } catch (Exception $exception) {
                echo "Ss";
                return "Curl error in createToken: " . $exception->getMessage();
                $cnt++;
            }


            $xml = new SimpleXMLElement($response);

            // Check if token creation response has been received
            if (!in_array($xml->xpath('Result')[0]->__toString(), array_keys($this->createResponses))) {
                return "Error in getting Transaction Token: Invalid response: " . $response;
                $cnt++;
            } elseif ($xml->xpath('Result')[0]->__toString() === '000') {
                $transToken        = $xml->xpath('TransToken')[0]->__toString();
                $result            = $xml->xpath('Result')[0]->__toString();
                $resultExplanation = $xml->xpath('ResultExplanation')[0]->__toString();
                $transRef          = $xml->xpath('TransRef')[0]->__toString();

                $created = true;

                return [
                    'success'           => true,
                    'result'            => $result,
                    'transToken'        => $transToken,
                    'resultExplanation' => $resultExplanation,
                    'transRef'          => $transRef,
                ];
            } else {
                $created = true;

                return [
                    'success'   => false,
                    'errorcode' => $xml->xpath('Result')[0]->__toString(),
                    'error'     => $xml->xpath('ResultExplanation')[0]->__toString(),
                ];
            }
        }
    }


    public function initializePayment()
    {

        $payment_method = $this->name;

        $order_ref = $this->order->generateOrderID();

        $amount = $this->amountPayable();


        $user = $this->order->Buyer;
        $code = \Config::currency('code');
        $domain = Config::domain();

        $payment_details = [
            'gateway' => $this->name,
            'ref' => $order_ref,
            'order_unique_id' => $this->order->id,
            'name_in_shop' => $this->order->name_in_shop,
            'email' => strtolower(trim($user->email)),
            'phone' => $user->phone ?? '09134567891',
            'currency' => $code,
            'amount' => $amount,
            'custom_fields' => [
                [
                    'metaname' => "Full Name",
                    'metavalue' => $user->fullname,
                ],
                [
                    'metaname' => "Phone",
                    'metavalue' => $user->phone,
                ],
            ],
            'success_url' => "$domain/user/dashboard",
            'failure_url' => "",
        ];


        $param = http_build_query([
            'item_purchased' => $payment_details['name_in_shop'],
            'order_unique_id' => $payment_details['order_unique_id'],
            'payment_method' =>  $payment_details['gateway'],
        ]);


        $checkout_url =  "$domain/shop/checkout/?$param";
        $success_url =  "$domain/shop/callback/?$param";
        $payment_details['success_url'] = $success_url;


        $response = $this->createToken($payment_details);

        $payment_details['checkout_url'] = "{$this->checkout_url}?ID={$response['transToken']}";
        $payment_details['response'] = $response;

        $this->order->setPayment($payment_method, $payment_details);

        return $this;
    }


    public function goToGateway()
    {

        $payment_details = json_decode($this->order->payment_details, true);

        $checkout_url = $payment_details['checkout_url'];
        Redirect::to($checkout_url);
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

        $payment_details['api_keys'] = $this->api_keys['public_key'];


        return $payment_details;
    }
}
