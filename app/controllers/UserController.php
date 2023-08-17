<?php

use v2\Shop\Shop;
use v2\Models\Api;
use v2\Models\Unit;
use v2\Models\Applet;
use v2\Models\Wallet;
use v2\Classes\Bookies;
use v2\Models\Document;
use app\models\UniOrder;
use v2\Models\Commission;
use v2\Models\Withdrawal;
use v2\Security\TwoFactor;
use v2\Classes\ExchangeRate;
use v2\Models\Wallet\Journals;
use Filters\Filters\OrderFilter;
use Filters\Filters\WalletFilter;
use Filters\Filters\WithdrawalFilter;
use v2\Filters\Filters\JournalsFilter;
use Filters\Filters\SupportTicketFilter;
use v2\Jobs\Jobs\SendOrderFollowUpEmail;
use Filters\Filters\SubscriptionOrderFilter;
use Illuminate\Database\Capsule\Manager as DB;

/**
 *
 */
class UserController extends controller
{


    public function __construct()
    {

        if (!$this->admin()) {
            $this->middleware('current_user')
                ->mustbe_loggedin()
                ->must_have_verified_email();
            // ->must_have_verified_company();
        }
    }

    public function refresh_balance_by_unit()
    {

        $auth = $this->auth();
        $last_week = date("Y-m-d", strtotime("-1 week"));


        $orders = UniOrder::where("user_id", $auth->id)
            ->UnPaid()
            ->latest()
            ->whereDate("created_at", ">=", $last_week)
            ->take(4)
            ->get();



        foreach ($orders as $key => $order) {
            $shop = new Shop();
            $shop->setOrder($order)->reVerifyPayment();
        }

        $balance_field = [
            'model' => $auth,
            'field' => "unit",
            'refresh' => "1",
        ];

        $unit = Unit::availableBalanceOnUser($auth->id, null, null, null, $balance_field);
    }


    public function refresh_balance()
    {

        $auth = $this->auth();
        $last_week = date("Y-m-d", strtotime("-1 week"));

        if (true) {

            $orders = SubscriptionOrder::where("user_id", $auth->id)
                ->UnPaid()
                ->latest()
                ->whereDate("created_at", ">=", $last_week)
                ->take(3)
                ->get();


            foreach ($orders as $key => $order) {
                $shop = new Shop();
                $shop->setOrder($order)->reVerifyPayment();
            }

            $this->refresh_balance_by_unit();
            return;
        }



        $orders = UniOrder::where("user_id", $auth->id)
            ->UnPaid()
            ->latest()
            ->whereDate("created_at", ">=", $last_week)
            ->take(4)
            ->get();



        foreach ($orders as $key => $order) {
            $shop = new Shop();
            $shop->setOrder($order)->reVerifyPayment();
        }

        return;



        $balance_field = [
            'model' => $auth,
            'field' => "unit",
            'refresh' => "1",
        ];

        $unit = Unit::availableBalanceOnUser($auth->id, null, null, null, $balance_field);
    }


    public function choose_membership($membership_id)
    {
        $auth = $this->auth();
        $membership = SubscriptionPlan::find($membership_id);

        if ($membership == null) {
            return;
        }

        $subscription_id = $membership_id;

        $personal_settings = $auth->SettingsArray;
        $personal_settings['membership_choice'] = $membership_id;

        $auth->save_settings($personal_settings);

        if ($membership_id < 2) {

            Redirect::back();
        }

        $response = SubscriptionPlan::create_subscription_request($subscription_id, $auth->id,  true);

        Redirect::back();
    }


    public function orders()
    {

        $auth = $this->auth();
        $sieve = $_REQUEST;

        $query = SubscriptionOrder::where('user_id', $auth->id)
            ->where('payment_details', "!=", NULL)
            ->latest('created_at');

        $sieve = array_merge($sieve, [
            // "date" => ["last_6month", "created_at"]
        ]);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 25;
        $skip = (($page - 1) * $per_page);

        $filter = new  SubscriptionOrderFilter($sieve);

        $data = $query->Filter($filter)->count();

        $subscription_orders = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered


        $note = MIS::filter_note($subscription_orders->count(), $data, $query->count(),  $sieve, 1);

        $this->view('auth/orders', get_defined_vars());
    }


    public function direct_ranks()
    {
        $direct_ranks = $this->auth()->referred_members_downlines(1)[1];
        $direct_ranks = User::whereIn('id', collect($direct_ranks)->where('rank', '>', -1)->pluck('id')->toArray())->get();
        $this->view('auth/direct_ranks', compact('direct_ranks'));
    }

    public function send_email_code()
    {
        echo "<pre>";

        $this->create_email_code();
    }


    public function resources($category_key = null)
    {

        $category = Document::$categories[$category_key] ?? null;

        $documents = Document::where('category', $category)->get();
        $title = "$category";

        if ($documents->isEmpty()) {
            $documents = Document::get();
            $title = "All Documents";
        }

        $this->view('auth/resources', compact('title', 'documents'));
    }

    public function faqs()
    {
        $this->view('auth/faqs');
    }

    public function supportmessages($value = '')
    {
        $this->view('auth/support-messages');
    }




    public function submit_2fa()
    {
        $auth = $this->auth();

        if ($_POST['code'] == '') {
            Session::putFlash('danger', "Invalid Code");
            Redirect::back();
        }

        $this->verify_2fa_only();


        $existing_settings = $auth->SettingsArray;

        $twofa_recovery = MIS::random_string(10);
        if (!$auth->has_2fa_enabled()) {
            $existing_settings['enable_2fa'] = 1;
            $existing_settings['2fa_recovery'] = $twofa_recovery;

            Session::putFlash('success', "2FA enabled successfully");
        } else {
            $existing_settings['enable_2fa'] = 0;
            Session::putFlash('success', "2FA disabled successfully");
        }

        $auth->save_settings($existing_settings);

        Redirect::back();
    }

    public function two_factor_authentication()
    {

        $auth = $this->auth();

        if ($auth->has_2fa_enabled()) {

            $image = null;
        } else {

            $_2FA = new TwoFactor($auth);
            $image = $_2FA->getQrCode();
        }

        $this->view('auth/two-factor-authentication', compact('image'));
    }



    public function submit_hash_rate($order_id = '')
    {
        echo "<Pre>";
        print_r($_POST);
        $auth = $this->auth();
        $order = Wallet::where('user_id', $auth->id)->where('id', $_POST['order_id'])->first();


        if ($order == null) {
            Session::putFlash("danger", "Invalid Request");
            Redirect::back();
        }

        $extra_detail = $order->ExtraDetailArray;
        $extra_detail['hash_rate'] = $_POST['hash_rate'];


        $order->update([
            'extra_detail' => json_encode($extra_detail)
        ]);

        Redirect::back();
    }



    public function submit_make_deposit()
    {


        $rules_settings = SiteSettings::find_criteria('rules_settings');
        $min_deposit = $rules_settings->settingsArray['min_deposit_usd'];

        $this->validator()->check(Input::all(), array(
            'amount' => [
                'required' => true,
                'positive' => true,
                'min_value' => $min_deposit,
            ],
            'payment_method' => [
                'required' => true,
            ],
        ));


        if (!$this->validator->passed()) {

            Session::putFlash('danger', Input::inputErrors());
            Redirect::back();
        }

        DB::beginTransaction();

        try {


            $deposit = Wallet::create([
                'user_id' => $this->auth()->id,
                'amount' => Input::get('amount'),
                'earning_category' => 'deposit',
                'comment' => 'Funding Account',
                'type' => 'credit',
                'status' => 'pending',
                'payment_method' => $_POST['payment_method'],
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Session::putFlash("danger", "We could not initialize the payment process. Please try again");
            Redirect::back();
        }

        $callback_param = http_build_query([
            'item_purchased' => $deposit->name_in_shop,
            'order_unique_id' => $deposit->id,
            'payment_method' => $deposit->payment_method,
        ]);


        $callback_url = "shop/checkout?$callback_param";


        /*		$deposit->mark_paid();
                $shop = new Shop();
                $shop->empty_cart_in_session();
        */

        Redirect::to("$callback_url");


        // $this->deposit_checkout($deposit->id);

    }



    public function notifications($notification_id = 'all')
    {

        $auth = $this->auth();
        $per_page = 50;
        $page = $_GET['page'] ?? 1;

        switch ($notification_id) {
            case 'all':
                $notifications = Notifications::all_notifications($auth->id, $per_page, $page);
                $total = Notifications::all_notifications($auth->id)->count();
                break;

            default:

                $total = null;

                $notifications = Notifications::where('user_id', $auth->id)->where('id', $notification_id)->first();

                Notifications::mark_as_seen([$notifications->id]);


                if ($notifications == null) {
                    Session::putFlash("danger", "Invalid Request");
                    Redirect::back();
                }



                if ($notifications->DefaultUrl != $notifications->UsefulUrl) {

                    Redirect::to($notifications->UsefulUrl);
                }

                break;
        }



        $this->view('auth/notifications', compact('notifications', 'per_page', 'total'));
    }



    public function commission_history()
    {

        $auth = $this->auth();

        $sieve = $_REQUEST;
        $sieve = array_merge($sieve);

        $query = Commission::for($auth->id)->latest();
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  WalletFilter($sieve);

        $balance = $query->sum('amount');

        $total = $query->count();

        $data = $query->Filter($filter)->count();

        $sql = $query->Filter($filter);

        $records = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered


        $balance = Commission::availableBalanceOnUser($auth->id);

        $note = MIS::filter_note($records->count(), $data, $total,  $sieve, 1);

        $this->view('auth/commission_history', compact('records', 'balance', 'sieve', 'data', 'per_page', 'note'));
    }




    public function company()
    {
        $company = $this->auth()->company;
        $this->view('auth/company', compact('company'));
    }


    public function order($order_id = null)
    {

        $order = SubscriptionOrder::where('id', $order_id)->where('user_id', $this->auth()->id)->first();
        echo $this->buildView('auth/order_detail', compact('order'));
    }



    public function products()
    {
        $this->view('auth/products');
    }


    public function products_orders()
    {
        $this->view('auth/products_orders');
    }


    public function view_cart()
    {

        $cart = json_decode($_SESSION['cart'], true)['$items'];

        if (count($cart) == 0) {
            Session::putFlash("info", "Your cart is empty.");
            Redirect::to('user/shop');
        }
        $this->view('auth/view_cart');
    }


    public function shop()
    {

        $products = $this->auth()->accessible_products();

        $this->view('auth/shop', compact('products'));
    }


    public function create_upgrade_request($subscription_id = null)
    {

        $validator = new Validator;
        $auth = $this->auth();
        $plans = SiteSettings::getPlans();
        $packages = collect($plans['plans'])->keyBy('id')->toArray();


        $cart = $packages[Input::get('id')];
        $exchange = new ExchangeRate;
        $priced_currency = SiteSettings::pricedCurrency();
        $code = Config::currency('code');

        $conversion = $exchange->setFrom($priced_currency)
            ->setTo($code)
            ->setAmount($cart['price'])
            ->getConversion();

        $cart['priced_currency'] = $priced_currency;
        $cart['converted_price'] = round($conversion['destination_value'], 2);
        $cart['end_currency'] = $code;

        $cart = array_map(function ($item) {
            if ($item === INF) {
                return "INF";
            }
            return $item;
        }, $cart);



        if (Input::get('destination_bookies') != null) {
            $dest_bookie = array_map(function ($item) {
                return 1;
            }, array_flip(Input::get('destination_bookies')));


            if (count($dest_bookie) > $cart['no_of_destination_bookies']) {
                $validator->addError("Bet shop bookies", "Bookies can not be more than {$cart['no_of_destination_bookies']}");
            }
            $cart['destination_bookies'] = $dest_bookie;
        }

        if (Input::get('attempted_bookies') != null) {
            $attempted_bookies = array_map(function ($item) {
                return 1;
            }, array_flip(Input::get('attempted_bookies')));



            if (count($attempted_bookies) > $cart['no_of_bookies']) {
                $validator->addError("allowed bookies", "Bookies can not be more than {$cart['no_of_bookies']}");
            }
            $cart['attempted_bookies'] = $attempted_bookies;
        }


        if (!$validator->passed()) {
            Session::putFlash('danger', Input::inputErrors());
            Redirect::back();
        }


        $new_order = SubscriptionOrder::updateOrcreate(
            ['id' => null],
            [
                'payment_method' => Input::get('payment_method'),
                'user_id' => $auth->id ?? null,
                'plan_id' => Input::get('id'),
                'price'  => $cart['converted_price'],
                'details' => json_encode($cart),
            ]
        );

        $order = $new_order;
        $follow_up_at = date("Y-m-d H:i:s", strtotime("+ 20 minute"));
        SendOrderFollowUpEmail::dispatch(compact('order'), $follow_up_at);
        $follow_up_at = date("Y-m-d H:i:s", strtotime("+ 80 minute"));
        SendOrderFollowUpEmail::dispatch(compact('order'), $follow_up_at);


        $_SESSION['shop_checkout_id'] = $new_order->id;

        $shop = new Shop();
        $shop
            // ->setOrderType('order') //what is being bought
            ->setOrder($new_order)
            ->setPaymentMethod(Input::get('payment_method') ?? 'rave')
            ->setPaymentType();



        $payment_details = $shop->initializePayment()
            ->attemptPayment();

        header("content-type:application/json");
        echo json_encode($payment_details);
    }



    public function account_plan()
    {
        $this->view('auth/account_plan');
    }


    public function reports()
    {
        $this->view('auth/report');
    }


    public function submit_user_transfers()
    {
        echo "<pre>";



        $rules_settings = SiteSettings::find_criteria('rules_settings');
        $transfer_fee = $rules_settings->settingsArray['user_transfer_fee_percent'];
        $min_transfer = $rules_settings->settingsArray['min_transfer_usd'];


        $this->validator()->check(Input::all(), array(
            'amount' => [
                'required' => true,
                'positive' => true,
                'min_value' => $min_transfer,
            ],
            'wallet' => [
                'required' => true,
            ],

            'username' => [
                'required' => true,
                'exist' => 'User|username',
            ],

        ));


        if (!$this->validator->passed()) {

            Session::putFlash('danger', Input::inputErrors());
            Redirect::back();
        }

        $auth = $this->auth();
        $from = $auth->id;
        $amount = Input::get('amount');
        $username = Input::get('username');
        $wallet = Input::get('wallet');

        $to = User::where('username', $username)->first()->id;


        $wallet_to_use = Wallet::$wallets[Input::get('wallet')];
        $wallet_class = $wallet_to_use['class'];
        $wallet_category = $wallet_to_use['category'];


        $transfer = $wallet_class::makeTransfer($from, $to, $amount, $wallet_category, 'deposit');
        $currency = Config::currency();
        $formatted_amount = MIS::money_format($amount);

        if ($transfer == true) {

            Session::putFlash('success', "$currency$formatted_amount transfer initiated successfully to $username");
        } else {

            Session::putFlash('danger', "Transfer Failed");
        }

        Redirect::back();
    }








    public function user_transfers()
    {
        $auth = $this->auth();
        $wallet = new Wallet;
        $deposit_balance = Wallet::availableBalanceOnUser($auth->id, 'deposit');

        $this->view('auth/user-transfers', compact('wallet', 'deposit_balance'));
    }


    public function make_deposit()
    {
        $auth = $this->auth();
        $shop = new Shop;
        $deposits = Wallet::for($auth->id)->Category('deposit')->Credit()->latest()->get();
        $deposit_balance = Wallet::TraitavailableBalanceOnUser($auth->id, 'deposit');

        $this->view('auth/make_deposit', compact('shop', 'deposit_balance', 'deposits'));
    }


    public function my_wallet()
    {
        $this->view('auth/my_wallet');
    }


    public function make_withdrawal()
    {
        $this->view('auth/make_withdrawal');
    }

    public function payout_methods()
    {
        $this->withdrawal_methods();
    }
    public function withdrawal_methods()
    {
        $this->view('auth/withdrawal_methods');
    }



    public function withdrawals()
    {

        $query = Withdrawal::where('user_id', $this->auth()->id)->latest();


        $sieve = $_REQUEST;
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  WithdrawalFilter($sieve);

        $data = $query->Filter($filter)->count();

        $withdrawals = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered




        $this->view('auth/withdrawal-history', compact('withdrawals', 'sieve', 'data', 'per_page'));



        // $this->view('auth/withdrawal-history', compact('withdrawals'));
    }



    public function fetch_testimonial($testimony_id)
    {
        $testimony = Testimonials::find($testimony_id);
        header("content-type:application/json");

        echo $testimony;
    }

    public function update_testimonial()
    {

        echo "<pre>";
        $testimony_id = Input::get('testimony_id');

        $auth = $this->auth();

        $testimony = Testimonials::where('user_id', $auth->id)->where('id', $testimony_id)->NotApproved()->first();

        $attester = $auth->lastname . ' ' . $auth->firstname;

        Testimonials::updateOrCreate(
            [
                'id' => $_POST['testimony_id']
            ],
            [
                'attester' => $attester,
                'user_id' => $auth->id,
                'content' => Input::get('testimony'),
                'type' => Input::get('type'),
                'video_link' => Input::get('video_link'),
                'intro' => Input::get('intro'),
                'approval_status' => 0
            ]
        );


        Session::putFlash('success', 'Testimonial updated successfully. Awaiting approval');

        Redirect::back();
    }


    public function create_testimonial()
    {
        if (Input::exists() || true) {

            $auth = $this->auth();

            $testimony = Testimonials::create([
                'attester' => $auth->lastname . ' ' . $auth->firstname,
                'user_id' => $auth->id,
                'content' => Input::get('testimony')
            ]);
        }
        Redirect::to("user/edit_testimony/{$testimony->id}");
    }


    public function edit_testimony($testimony_id = null)
    {
        $testimony = Testimonials::where('user_id', $this->auth()->id)->where('id', $testimony_id)->NotApproved()->first();

        if (($testimony == null)) {
            Session::putFlash('danger', 'Invalid Request');
            Redirect::back();
        }


        $this->view('auth/edit_testimony', ['testimony' => $testimony]);
    }


    public function view_testimony()
    {
        $this->view('auth/view-testimony');
    }


    public function testimony()
    {

        $auth = $this->auth();
        $testimonials = Testimonials::where('user_id', $auth->id)->latest()->get();
        $this->view('auth/testimony', compact('testimonials'));
    }


    public function documents()
    {
        $show = false;
        $this->view('auth/documents', compact('show'));
    }


    public function news()
    {
        $this->view('auth/news');
    }

    public function language()
    {
        $this->view('auth/language');
    }


    public function profile()
    {
        $this->view('auth/profile');
    }

    public function upload_payment_proof()
    {
        $order_id = $_POST['order_id'];
        $order = SubscriptionOrder::find($order_id);
        $order->upload_payment_proof($_FILES['payment_proof']);
        Session::putFlash('success', "#$order_id Proof Uploaded Successfully!");
        Redirect::back();
    }

    public function contact_us()
    {
        $this->view('auth/contact-us');
    }


    public function support()
    {
        $auth = $this->auth();

        $sieve = $_REQUEST;
        $sieve = array_merge($sieve);

        $query = SupportTicket::where('user_id', $auth->id)->latest();
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  SupportTicketFilter($sieve);

        $data = $query->Filter($filter)->count();

        $tickets = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered


        $this->view('auth/support', compact('tickets', 'sieve', 'data', 'per_page'));
    }


    public function view_ticket($ticket_id)
    {

        $support_ticket = SupportTicket::find($ticket_id);

        $this->view('auth/support-messages', [
            'support_ticket' => $support_ticket
        ]);
    }

    public function package_invoice($order_id = null)
    {
        $order = SubscriptionOrder::where('id', $order_id)->where('user_id', $this->auth()->id)->first();

        if ($order == null) {
            Redirect::back();
        }

        echo $order->getInvoice();
    }


    public function index()
    {
        $this->dashboard();
    }


    public function accounts()
    {
        $this->view('auth/accounts');
    }


    public function change_password()
    {
        $this->accounts();
    }


    public function games()
    {
        $auth = $this->auth();

        $query = Orders::where('user_id', $auth->id)/*->whereIn('id', [2])*/->Paid()->latest();

        $sieve = $_REQUEST;
        // $sieve = array_merge($sieve, $extra_sieve);
        $total = $query->count();
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 20;
        $skip = (($page - 1) * $per_page);

        $filter = new OrderFilter($sieve);

        $data = $query->Filter($filter)->count();

        $orders = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered

        $note = MIS::filter_note($orders->count(), $data, $total,  $sieve, 1);


        foreach ($orders as $key => $order) {
            //this makes live edit of ad reflect in order purchases
            $order->order_detail = $order->delivery_details();
        }

        $shop = new Shop;

        $this->view('auth/games', compact('orders', 'note', 'total', 'per_page', 'sieve', 'shop'));
    }


    public function update_applet()
    {

        $json = file_get_contents('php://input');
        $input = json_decode($json, TRUE);
        header("content-type:application/json");

        //validate
        $validator = new Validator;
        $domains = explode(",", $input['details']['domain']);

        $validator->check($input['details'], [
            "domain" => [
                // "domain" => true,
                "required" => true,
            ]
        ]);

        if (!in_array($input['details']['domain'], ["*", "localhost"]) && !$validator->is_domain_name_valid($input['details']['domain'])) {
            $validator->addError("domain", "Domain is not valid");
        }



        $validator->check($input, array(
            'name' => [
                'required' => true,
                'max' => '32',
                'min' => '2',
            ],
        ));


        // print_r(Input::inputErrors());

        if (!$validator->passed()) {
            Session::putFlash("danger", Input::inputErrors());
            echo json_encode([]);
            return;
        }


        $auth = $this->auth();

        try {

            $applet = Applet::updateOrCreate([
                "id" => $input['id'],
                "user_id" => $auth->id,
            ], $input);
            Session::putFlash("success", "changes saved successfully");
        } catch (\Throwable $th) {
        }

        echo json_encode([]);
    }

    public function update_link()
    {

        $json = file_get_contents('php://input');
        $input = json_decode($json, TRUE);
        header("content-type:application/json");
        $domain = Config::domain();

        //validate
        $validator = new Validator;
        $domains = explode(",", $input['details']['domain']);

        $validator->check($input['details'], [
            "domain" => [
                // "domain" => true,
                // "required" => true,
            ]
        ]);


        $validator->check($input, array(
            'name' => [
                'required' => true,
                'max' => '32',
                'min' => '2',
            ],
        ));


        if (!$validator->passed()) {
            Session::putFlash("danger", Input::inputErrors());
            echo json_encode([]);
            return;
        }

        //add default domain/or convertbetcodes
        $parse = parse_url($domain);
        $input['details']['domain'] = $parse['host'];

        $auth = $this->auth();

        try {

            $applet = Applet::updateOrCreate([
                "id" => $input['id'],
                "user_id" => $auth->id,
            ], $input);
            Session::putFlash("success", "changes saved successfully <br>{$applet->PublicLink}");
        } catch (\Throwable $th) {
        }

        echo json_encode([]);
    }


    public function get_applet($id)
    {
        $auth = $this->auth();
        $applet = Applet::where("user_id", $auth->id)->where("id", $id)->first();

        if ($applet->details == null) {
            $applet = new Applet;
            $applet = Applet::getDefaultInstance();
            $applet->details = $applet->details;
        }


        //get bookies
        $home_bookies = (new Bookies)->getAvailabilityOfFrom();
        ksort($home_bookies);

        $destination_bookies = (new Bookies)->getAvailabilityOfTo();
        ksort($destination_bookies);

        extract($applet->getIntegrationCode());


        header("content-type:application/json");
        echo json_encode(compact('applet', "home_bookies", "destination_bookies", "embed_code", "inline_js_code"));
    }

    public function applets()
    {

        $auth = $this->auth();
        $applets = Applet::where("user_id", $auth->id)->whereRaw("(type = 'applet' or type is null)")->get();

        $this->view('auth/applets', get_defined_vars());
    }


    public function conversion_links()
    {
        $auth = $this->auth();

        $links = Applet::where("user_id", $auth->id)->isType('link')->get();

        $this->view('auth/conversion_links', get_defined_vars());
    }

    public function submit_affiliate_application()
    {


        $validator = new Validator;

        $validator->check($_POST, [
            "currency" => [
                "require" => true,
                "min" => 3,
                "max" => 3,
            ],
            "agreed_to_terms" => [
                "require" => true,
                "equal" => 1,
            ],
        ]);


        if (!$validator->passed()) {
            Session::putFlash("danger", Input::errors());
            Redirect::back();
        }

        $auth = $this->auth();

        if ($auth->isApprovedAffiliate()) {
            # code...
            Redirect::back();
        }


        $auth->updateSettings([
            "affiliate" => Input::all()
        ]);

        Session::putFlash("success", "submitted successfully");
        Redirect::back();
    }

    public function affiliate()
    {
        $auth = $this->auth();


        if (!$auth->isApprovedAffiliate()) {

            Session::putFlash("danger", "Please complete the affiliate form");
            Redirect::to("user/affiliate_application");
        }

        $settings = $auth->getAffiliateSettings();

        $user_currency = strtolower($settings['settings']['currency']);

        $account =  $auth->getAccount("{$user_currency}_wallet");




        $today_journal_filter = [
            "journal_date" => [
                "start_date" => date("Y-m-d"),
                "end_date" => date("Y-m-d"),
            ]
        ];
        $response = ($account->transactions(100, 1, $today_journal_filter, []));
        $earnings_today = $response['total_a_credit'];


        $this_month_journal_filter = [
            "journal_date" => [
                "start_date" => date("Y-m-01", time()),
                "end_date" => date("Y-m-t", time()),
            ]
        ];

        $response = ($account->transactions(100, 1, $this_month_journal_filter, []));
        $earnings_this_month = $response['total_a_credit'];


        $general_settings = SiteSettings::getAffiliateCommissionStructure();
        $period_of_payment = $general_settings['period_of_payment'];
        $period_ago = date("Y-m-d 00:00:00", strtotime("-$period_of_payment"));


        $referred_all_time = $auth->all_downlines_by_path('enrolment', false, 1)
            ->whereBetween('created_at', [$period_ago, date("Y-m-d h:i:s")])
            ->count();



        // $referred_all_time_l1 = $auth->all_downlines_by_path('enrolment', false, 2)->count();


        $last_month =  date("Y-m-t", strtotime(" -1 month"));


        $available_balance = $account->get_balance();


        //balance; all pending withdrawal
        $sieve = array_merge($_REQUEST, [
            "tag" => "withdrawal",
            "notes" => "withdrawal",
            "user_id" => $auth->id,
            "status" => '3,4',
            /*       "journal_date" => [
                "start_date" => date("Y-m-d"),
                "end_date" => date("Y-m-d"),
            ]
         */
        ]);
        $filter = new JournalsFilter($sieve);
        $query = Journals::latest();
        $query->Filter($filter);

        $total_earnings = $query->Filter($filter)
            ->sum('c_amount');  //filtered


        //last_paid
        $last_paid = array_merge($_REQUEST, [
            "tag" => "withdrawal",
            "notes" => "withdrawal",
            "user_id" => $auth->id,
            "status" => '3,4',
        ]);
        $filter = new JournalsFilter($last_paid);
        $query = Journals::latest();
        $query->Filter($filter);

        $last_payment = $query
            ->Filter($filter)
            ->first()->c_amount ?? 0;  //filtered


        $this->view('auth/affiliate', get_defined_vars());
    }
    public function affiliate_application()
    {

        $auth = $this->auth();

        if ($auth->isApprovedAffiliate()) {
            Redirect::to("user/affiliate");
        }

        $this->view('auth/affiliate_application', get_defined_vars());
    }



    public function create_applet()
    {
        $applet = Applet::createNewApplet();
        Redirect::to("user/edit_applet/?id=$applet->id");
    }

    public function create_link()
    {
        $applet = Applet::createNewLink();
        Redirect::to("user/edit_link/?id=$applet->id");
    }


    public function edit_link()
    {
        $id = $_GET['id'];
        $auth = $this->auth();
        $applet = $id == null ?  new Applet : Applet::where("user_id", $auth->id)->where("id", $id)->first();
        $this->view('auth/edit_link', get_defined_vars());
    }

    public function edit_applet()
    {
        $id = $_GET['id'];
        $auth = $this->auth();
        $applet = $id == null ?  new Applet : Applet::where("user_id", $auth->id)->where("id", $id)->first();
        $this->view('auth/edit_applet', get_defined_vars());
    }


    public function switch_api($id = null)
    {
        $auth = $this->auth();
        $api = Api::where("user_id", $auth->id)->where("id", $id)->first();

        if ($api == null) {
            Session::putFlash("danger", "invalid request");
            return;
        }

        $api->switch();
    }

    public function api()
    {
        $auth = $this->auth();
        $apis = Api::where('user_id', $auth->id)->get();

        $this->view('auth/api', get_defined_vars());
    }

    public function pay_selected_plan($plan_id)
    {

        $shop = new Shop;

        $plans = SiteSettings::getPlans();
        $packages = $plans['plans'];


        $exchange = new ExchangeRate;
        $priced_currency = SiteSettings::pricedCurrency();

        $code = Config::currency('code');

        foreach ($packages as $key => &$package) {
            //conversion
            $conversion = $exchange->setFrom($priced_currency)
                ->setTo($code)
                ->setAmount($package['price'])
                ->getConversion();

            $package['priced_currency'] = round((float) $priced_currency, 2);
            $package['converted_price'] = round((float) $conversion['destination_value'], 2);
            $package['end_currency'] = $code;
        }


        $this->view('auth/pay_selected_plan', get_defined_vars());
    }

    public function dashboard()
    {
        $this->view('auth/dashboard', get_defined_vars());
    }

    public function broadcast()
    {

        $auth = $this->auth();

        $sieve = $_REQUEST;
        $query = BroadCast::Published()->latest();
        // ->where('status', 1);  //in review
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $data = $query->count();

        $news = $query
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered


        $this->view('auth/broadcast', compact('news', 'sieve', 'data', 'per_page'));
    }
}
