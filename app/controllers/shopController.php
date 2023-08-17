<?php

use  v2\Shop\Shop;
use v2\Models\Tip;

use v2\Models\Customer;
use app\models\UniOrder;
use v2\Classes\ExchangeRate;
use CoinbaseCommerce\Webhook;
use Filters\Filters\TipFilter;
use CoinbaseCommerce\ApiClient;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * this class is the default controller of our application,
 * 
 */
class shopController extends controller
{


    public function __construct()
    {

        /*		
		if (! $this->admin()) {

			$this->middleware('current_user')
				 ->mustbe_loggedin();
				 // ->must_have_verified_email();
		}		
*/
    }


    public function complete_unit_order($action = 'breakdown')
    {

        $json = file_get_contents('php://input');
        $input = json_decode($json, TRUE);


        DB::beginTransaction();

        // $packages = collect(SiteSettings::find_criteria('unit_packs')->settingsArray)->keyBy('id');
        $packages = collect(SiteSettings::getPackages())->keyBy('id');

        $cart = $packages[$input['id']];
        $exchange = new ExchangeRate;
        $priced_currency = $packages['priced_currency'];
        $code = Config::currency('code');

        $conversion = $exchange->setFrom($priced_currency)
            ->setTo($code)
            ->setAmount($cart['price'])
            ->getConversion();

        $cart['priced_currency'] = $priced_currency;
        $cart['converted_price'] = $conversion['destination_value'];
        $cart['end_currency'] = $code;


        try {

            $auth = $this->auth();

            //create new customer
            $extra_detail = $cart['$extra_detail'] ?? [];

            $new_order = UniOrder::updateOrcreate(
                ['id' => $_SESSION['shop_checkout_id'] ?? null],
                [
                    'user_id'        => $auth->id ?? null,
                    // 'customer_id'        => $customer->id ?? null,
                    'buyer_order'    => json_encode($cart),
                    'extra_detail'    => json_encode($cart['$extra_detail'] ?? []),
                ]
            );

            $shop = new Shop();
            $shop
                // ->setOrderType('order') //what is being bought
                ->setOrder($new_order)
                ->setPaymentMethod($input['payment_method'] ?? 'rave')
                ->setPaymentType();

            DB::commit();
            $_SESSION['shop_checkout_id'] = $new_order->id;

            header("content-type:application/json");
            switch ($action) {
                case 'get_breakdown':
                    $breakdown = $shop->fetchPaymentBreakdown();
                    echo json_encode(compact('breakdown'));
                    break;

                case 'make_payment':

                    $payment_details = $shop->initializePayment()
                        ->attemptPayment();

                    // Session::putFlash('success', "Order Created Successfully. ");
                    echo json_encode($payment_details);
                    break;

                default:
                    # code...
                    break;
            }
        } catch (Exception $e) {
            print_r($e->getMessage());

            DB::rollback();
            Session::putFlash('danger', "We could not create your order.");
            // Redirect::back();
        }
    }

    public function complete_order($action = 'breakdown')
    {

        $cart = json_decode($_POST['cart'],  true);

        DB::beginTransaction();

        try {

            $auth = $this->auth();

            //create new customer
            $extra_detail = $cart['$extra_detail'];
            if (!$auth) {
                $customer = Customer::updateOrcreate(
                    [
                        'email' => $extra_detail['email'],
                    ],
                    [
                        'firstname' => $extra_detail['firstname'] ?? null,
                        'lastname' => $extra_detail['lastname'] ?? null,
                        'phone' => $extra_detail['phone'] ?? null
                    ]
                );
            }


            $product_references = explode("-", $extra_detail['product_ref']);
            $affiliate_id = $product_references[1] ?? null;
            $game_date = date("Y-m-d");

            $new_order = Orders::updateOrcreate(
                ['id' => $_SESSION['shop_checkout_id'] ?? null],
                [
                    'user_id'        => $auth->id ?? null,
                    'customer_id'        => $customer->id ?? null,
                    'game_date'        => $game_date,
                    'buyer_order'    => json_encode($cart['$items']),
                    'extra_detail'    => json_encode($cart['$extra_detail']),
                    'percent_off'    => $percent_off ?? 0,
                ]
            );

            $shop = new Shop();
            $shop
                // ->setOrderType('order') //what is being bought
                ->setOrder($new_order)
                ->setPaymentMethod($_POST['payment_method'])
                ->setPaymentType();
            DB::commit();
            $_SESSION['shop_checkout_id'] = $new_order->id;

            header("content-type:application/json");

            //complete payment if it is free
            if ($new_order->is_free()) {
                $url = $new_order->after_payment_url();
                $payment_details = $shop->initializePayment()->attemptPayment();
                $new_order->mark_paid();
                echo json_encode(compact('url', 'new_order', 'payment_details'));
                return;
            }

            switch ($action) {
                case 'get_breakdown':


                    $breakdown = $shop->fetchPaymentBreakdown();
                    echo json_encode(compact('breakdown'));
                    break;

                case 'make_payment':

                    $payment_details = $shop->initializePayment()
                        ->attemptPayment();

                    Session::putFlash('success', "Order Created Successfully. ");
                    echo json_encode($payment_details);
                    break;

                default:
                    # code...
                    break;
            }
        } catch (Exception $e) {
            print_r($e->getMessage());

            DB::rollback();
            Session::putFlash('danger', "We could not create your order.");
            // Redirect::back();
        }
    }








    function verify_payment()
    {
        $order_id = $_REQUEST['order_id'];

        $order = Orders::where('id', $order_id)->where('paid_at', null)->first();

        if ($order == null) {
            return;
        }

        $shop = new Shop();
        $payment_details =    $shop
            ->setOrder($order) //what is being bought
            ->verifyPayment();
    }


    public function open_order_confirmation($order_id = '')
    {
        echo $this->buildView('emails/order_confirmation', ['order' => Orders::find($order_id)]);
    }

    /**
     * this is the default landing point for all request to our application base domain
     * @return a view from the current active template use: Config::views_template()
     * to find out current template
     */
    public function index($category = null)
    {
        echo "string";

        // $this->view('guest/shop', ['default_category'=>$category]);
    }


    public function order_detail($order_id)
    {
        $order = Orders::find($order_id);
        if ($order == null) {

            Redirect::back();
        }
        $this->view('guest/order_detail', ['order' => $order]);
    }



    public function re_confirm_order()
    {
        $shop = new Shop();
        $item_purchased = $shop->available_type_of_orders[$_REQUEST['item_purchased']];
        $full_class_name = $item_purchased['namespace'] . '\\' . $item_purchased['class'];
        $order = $full_class_name::where('id', $_REQUEST['order_unique_id'])->where('paid_at', null)->first();

        if ($order == null) {
            Redirect::to("/user/dashboard");
            return;
        }

        $shop->setOrder($order)->reVerifyPayment();
        $url = $order->after_payment_url();
        Redirect::to($url);
    }



    public function delivery($order_id)
    {
        $order_id = MIS::dec_enc('decrypt', $order_id);
        $order = Orders::where('id', $order_id)->Paid()->first();


        if ($order == null) {
            Redirect::back();
        }

        $time =  strtotime("+1 minute $order->paid_at");
        $now = time();
        if ($now < $time) {
        }
        $order->order_detail = $order->delivery_details();

        $this->view('guest/delivery', compact('order'));
    }





    public function callback()
    {
        $shop = new Shop();
        $item_purchased = $shop->available_type_of_orders[$_REQUEST['item_purchased']];
        $full_class_name = $item_purchased['namespace'] . '\\' . $item_purchased['class'];
        $order_id = $_REQUEST['order_unique_id'];
        $order = $full_class_name::where('id', $order_id)->where('paid_at', null)->first();

        if ($order == null) {
            Redirect::back();
        }

        $shop->setOrder($order)->verifyPayment();


        $url = $order->after_payment_url();


        header("content-type:application/json");
        echo json_encode(compact('url', 'order'));



        /*		$shop = new Shop();
		$item_purchased = $shop->available_type_of_orders[$_REQUEST['item_purchased']];
	 	$full_class_name = $item_purchased['namespace'].'\\'.$item_purchased['class'];		 	
	 	$order_id = $_REQUEST['order_unique_id'];
	 	$order = $full_class_name::where('id' ,$order_id)->where('paid_at', null)->first();

		$shop->setOrder($order)->verifyPayment();

		Redirect::to('user/my-games');*/
    }



    public function coinbase_commerce_webhook()
    {
        $settings = SiteSettings::find_criteria('coinbase_commerce_keys')->settingsArray;

        $mode = $settings['mode']['mode'];

        $api_keys =  $settings[$mode];

        $apiClientObj = ApiClient::init($api_keys['secret_key']);
        $apiClientObj->setTimeout(6);
        $secret = $api_keys['webhook_secret'];


        $headerName = 'X-Cc-Webhook-Signature';
        $headers = getallheaders();
        $signraturHeader = isset($headers[$headerName]) ? $headers[$headerName] : null;
        $payload = trim(file_get_contents('php://input'));

        $event = Webhook::buildEvent($payload, $signraturHeader, $secret);

        $order_id = $event["metadata"]["order_unique_id"];
        $item_purchased = $event["metadata"]["item_purchased"];


        $shop = new Shop();
        $item_purchased = $shop->available_type_of_orders[$item_purchased];
        $full_class_name =  $item_purchased['class'];
        $order = $full_class_name::where('id', $order_id)->where('paid_at', null)->first();
        $shop->setOrder($order)->verifyPayment();
    }


    public function accrue_webhook()
    {

        $secret = $_ENV['ACCRUE_WEBHOOK_SECRET'];
        $headerName = 'X-CASHRAMP-TOKEN';
        $headers = getallheaders();

        $payload = Input::all();

        $order_id = SubscriptionOrder::extractIdFromRef($payload['data']['reference']);
        $item_purchased = "packages";

        $shop = new Shop();
        $item_purchased = $shop->available_type_of_orders[$item_purchased];
        $full_class_name =  $item_purchased['class'];
        $order = $full_class_name::where('id', $order_id)->where('paid_at', null)->first();

        $shop->setOrder($order)->verifyPayment();
    }

    public function checkout($checkout_type = 'standard')
    {

        $shop = new Shop();

        $item_purchased = $shop->available_type_of_orders[$_REQUEST['item_purchased']];

        $full_class_name = $item_purchased['namespace'] . '\\' . $item_purchased['class'];
        $order_id = $_REQUEST['order_unique_id'];

        $order = $full_class_name::where('id', $order_id)->where('user_id', $this->auth()->id)->where('paid_at', null)->first();


        if ($order == null) {
            Session::putFlash("info", "Invalid Request");
            return;
        }

        $shop = new Shop();
        $attempt =    $shop
            ->setOrder($order)
            ->setPaymentMethod($_REQUEST['payment_method'])
            ->initializePayment()
            ->attemptPayment();


        if ($attempt == false) {
            Redirect::back();
        }

        switch ($checkout_type) {
            case 'standard':
                $shop->goToGateway();
                break;

            case 'inline':
                header("content-type:application/json");
                echo json_encode($attempt);
                break;

            default:
                # code...
                break;
        }
    }





    public function product_detail($product_id = null)
    {
        $product = Products::find($product_id);
        $this->view('guest/product-details', ['product' => $product]);
    }


    public function retrieve_cart_in_session()
    {

        header("content-type:application/json");


        if (!isset($_SESSION['cart'])) {
            $cart = [];
            print_r(json_encode($cart));
        }

        $cart = json_decode($_SESSION['cart'], true);

        if ($this->auth()) {
            // $cart = json_decode($this->auth()->cart, true);
        }

        foreach ($cart['$items'] as $key =>  $item) {
            // $item_array =  json_decode($item, true);
            unset($cart['$items'][$key]['$$hashKey']);
            $items[] = $item;
        }
        print_r(json_encode($cart));
    }


    public function update_cart()
    {
        $_SESSION['cart'] = ($_POST['cart']);
        if ($this->auth()) {
            // $this->auth()->update(['cart' => $_POST['cart']]);
        }
    }



    public function empty_cart_in_session()
    {
        Shop::empty_cart_in_session();
    }



    public function fetch_ads()
    {
        Redirect::to("");
        // Session::putFlash("danger", "Hi there");
        $per_page = 36;

        $request_date = (!isset($_GET['date'])) ? date("Y-m-d") : $_GET['date'];
        $page = (!isset($_GET['page'])) ? 1 : $_GET['page'];

        $skip = (($page - 1) * $per_page);

        $today = date("Y-m-d");
        // $request_date = '';
        // $request_date = '2021-06-16';
        $running_ad = Tip::Running($request_date)->get();

        //check for performance
        foreach ($running_ad as $key => $ad) {

            $details = $ad->getDetails;
            $round = $ad->getround(null, null, $request_date); //current round

            $games = $details['games'];
            $intro = $games[$round]['intro'] ?? '';



            foreach ($games as $game_round => $game) {

                $date = $game['period']['date']; #
                //prevent checkking previous dates performance twice
                if ($date != $today) {
                    continue;
                }


                if (!$this->admin()) {
                    // echo "ad"    ;
                }


                //$ad->check_performance($date);
            }
        }


        $sieve = $_REQUEST;
        $filter =  new  TipFilter($sieve);


        if ($this->admin()) {

            $sql = Tip::RunningForAdmin($request_date)
                ->Filter($filter)
                ->skip($skip)
                ->take($per_page)->with('paper');
        } else {
            //so we can show referrals adverts first			
            $referral_id = $_COOKIE['referral'] ?? 1;

            $sql = Tip::Running($request_date)
                ->Filter($filter);

            $editor_exist = User::AllEditors()->where('id', $referral_id)->first();

            if ($editor_exist) {
                $sql->orderByRaw("FIELD(editor_id,$referral_id) DESC");
            }


            $sql->skip($skip)
                ->take($per_page)
                ->with('paper');
        }


        $running_ad = $sql->get();
        $total = $sql->count();

        $responsive = true;


        $code = Config::currency('code');
        $exchange = new ExchangeRate;

        foreach ($running_ad as $key => $ad) {
            $detail = $ad->getDetails;
            $round = $ad->getround(null, null, $request_date);
            $ad->round = $detail['games'][$round];

            //conversion
            $priced_currency = $ad->round['currency'] ?? 'USD';
            $conversion = $exchange->setFrom($priced_currency)
                ->setTo($code)
                ->setAmount($ad->round['cost'])
                ->getConversion();

            $ad->price = $detail['games'][$round]['cost'] = $conversion['destination_value'];
            $detail['games'][$round]['currency'] = $code;
            $ad->detail = $detail;
            $ad->view = $this->buildView('composed/ad', compact('ad', 'responsive', 'round'), true, true);
        }


        $running_ad = collect($running_ad)->keyBy('id')->toArray();
        header("content-type:application/json");
        echo json_encode(compact('running_ad', 'total', 'per_page', 'page'));
    }



    public function retrieve_shipping_settings()
    {
        header("Content-type: application/json");
        // echo CmsPages::where('page_unique_name', 'shipping_details')->first()->page_content;
    }
}
