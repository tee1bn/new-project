<?php

use v2\Shop\Shop;
use v2\Models\Tip;
use v2\Models\Unit;
use v2\Models\Campaign;
use v2\Models\Document;
use v2\Models\Withdrawal;
use v2\Models\UserDocument;
use v2\Models\ConversionLog;
use Filters\Filters\TipFilter;
use v2\Models\Wallet\Journals;
use Filters\Filters\UserFilter;
use v2\Models\CampaignCategory;
use Filters\Filters\OrderFilter;
use v2\Models\ConversionLinting;
use v2\Models\InvestmentPackage;
use Filters\Filters\WalletFilter;
use Filters\Filters\CampaignFilter;
use v2\Models\Wallet\ChartOfAccount;
use Filters\Filters\WithdrawalFilter;
use Filters\Filters\TestimonialsFilter;
use Filters\Filters\UserDocumentFilter;
use Filters\Filters\SupportTicketFilter;
use Filters\Filters\CampaignCategoryFilter;
use v2\Filters\Filters\ConversionLogFilter;
use Filters\Filters\SubscriptionOrderFilter;
use Illuminate\Database\Capsule\Manager as DB;
use v2\Filters\Filters\ConversionLintingFilter;

/**
 * this class is the default controller of our application,
 *
 */
class AdminController extends controller
{


    public function __construct()
    {


        $this->middleware('administrator')->mustbe_loggedin();

        $this->checkAccesses();
    }



    public function checkAccesses()
    {

        $url = explode("/", MIS::current_url())[1];
        $admins_roles = [
            "marketer" => [
                "function" => "check_marketer",
                "emails" => ["tunnexten@gmail.com"],
            ]
        ];


        function check_marketer($url)
        {
            preg_match("/campaign/", $url, $matches);
            if (!$matches[0]) {
                Redirect::to("admin/all_campaigns");
            }
        }

        $admin = $this->admin();
        foreach ($admins_roles as $role => $privileges) {

            if (in_array($admin->email, $privileges['emails'])) {
                $method = $privileges['function'];
                $method($url);
            }
        }
    }


    public function change_conversion_linting_status($id, $status)
    {


        $lint = ConversionLinting::find($id);

        if ($lint == null) {
            Session::putFlash('danger', "Invalid Request.");
            Redirect::back();
        }


        DB::beginTransaction();

        try {

            $lint->markAs($status);
            $lint->update([
                'admin_id' => $this->admin()->id,
            ]);

            DB::commit();
            Session::putFlash('success', "Lint marked as $status");
        } catch (Exception $e) {
            DB::rollback();

            print_r($e->getMessage());
            die;
            Session::putFlash('danger', "Something went wrong. Please try again.");
        }


        Redirect::back();
    }



    public function all_campaigns()
    {


        $sieve = $_REQUEST;
        $sieve = array_merge($sieve, []);


        $query = Campaign::query();

        $page = (isset($_GET['page'])) ?  $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter =  new  CampaignFilter($sieve);

        $data =  $query->Filter($filter)->count();


        $campaigns =  $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filt


        $this->view('admin/all_campaigns', compact('sieve', 'data', 'per_page', 'campaigns'));
    }


    public function view_category($category_id)
    {
        $campaign_category = CampaignCategory::find($category_id);
        if ($campaign_category == null) {
            Session::putFlash('danger', "Invalid request");
            Redirect::back();
        }

        $this->view('admin/campaign_category_view',  compact('rows', 'campaign_category'));
    }

    public function campaigns_categories()
    {
        $sieve = $_REQUEST;
        $sieve = array_merge($sieve, []);


        $query = CampaignCategory::query();

        $page = (isset($_GET['page'])) ?  $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter =  new  CampaignCategoryFilter($sieve);

        $data =  $query->Filter($filter)->count();


        $campaigns_categories =  $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filt


        $this->view('admin/campaigns_categories', compact('sieve', 'data', 'per_page', 'campaigns_categories'));
    }

    public function create_campaign()
    {
        $campaign =  Campaign::create([
            'admin_id' => $this->admin()->id,
        ]);


        Redirect::to($campaign->editLink);
    }

    public function create_campaign_category()
    {
        $campaign =  CampaignCategory::create([
            'admin_id' => $this->admin()->id,
            'title' => "",
            'status' => 1,
        ]);


        Redirect::to($campaign->editLink);
    }


    public function edit_campaign($campaign_id)
    {
        $campaign =  Campaign::find($campaign_id);
        if ($campaign == null) {
            Session::putFlash('danger', 'Invalid request');
            Redirect::back();
        }

        $this->view('admin/edit_campaign', compact('campaign'));
    }


    public function edit_campaign_category($category_id)
    {
        $category =  CampaignCategory::find($category_id);
        if ($category == null) {
            Session::putFlash('danger', 'Invalid request');
            Redirect::back();
        }

        $this->view('admin/edit_campaign_category', get_defined_vars());
    }





























    public function edit_client_detail($client_id)
    {
        $client_id = MIS::dec_enc('decrypt', $client_id);
        $user = User::find($client_id);

        if ($user == null) {
            Session::putFlash("danger", "Client not found");
            Redirect::back();
        }


        $this->view('admin/edit_client_profile', compact('user'));
    }



    public function user_verification()
    {


        $sieve = $_REQUEST;
        $query = UserDocument::latest();
        // ->where('status', 1);  //in review


        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  UserDocumentFilter($sieve);

        $data = $query->Filter($filter)->count();

        $documents = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered


        $this->view('admin/user_verification', compact('documents', 'sieve', 'data', 'per_page'));
    }


    public function search($query = null)
    {

        $compact = $this->users_matters(['username' => $query]);
        $users = $compact['users'];
        $line = "";
        foreach ($users as $key => $user) {
            $username = $user->username;
            $fullname = $user->fullname;
            $line .= "<option value='$username'> $fullname ($username)</option>";
        }

        header("content-type:application/json");
        echo json_encode(compact('line'));
    }


    public function submit_manual_credit()
    {

        $validator = new Validator;
        $validator->check(Input::all(), array(
            'amount' => [
                'required' => true,
                'positive' => true,
            ],


            'type' => [
                'required' => true,
            ],
            'comment' => [
                'required' => true,
            ],

            'paid_at' => [
                'date' => "Y-m-d",
            ],

            /* 'username' => [
                'required' => true,
                'exist' => 'User|username',
            ], */

        ));



        $usernames = explode(",", $_POST['username']);
        $usernames = array_map(function ($item) {
            return trim($item);
        }, $usernames);


        $receivers = User::whereIn('username', $usernames)->get();

        $no_of_usernames = count($usernames);

        if ($receivers->count() != $no_of_usernames) {
            Session::putFlash("danger", "Users found{$receivers->count()} for {$no_of_usernames}usernames entered, not complete");
            Redirect::back();
        }


        if (!$validator->passed()) {
            Session::putFlash("danger", Input::inputErrors());
            Redirect::back();
        }




        $amount = $_POST['amount'];



        DB::beginTransaction();


        try {

            foreach ($receivers as $key => $receiver) {

                $direct_credit = Unit::createTransaction(
                    $_POST['type'],
                    $receiver['id'],
                    null,
                    $amount,
                    'completed',
                    'conversion',
                    $_POST['comment'],
                    null,
                    null,
                    null,
                    null,
                    $_POST['paid_at'],
                    null,
                    false
                );

                $direct_credit->update(['admin_id' => $this->admin()->id]);
            }

            DB::commit();
            Session::putFlash("success", "$amount {$_POST['type']} successful");
        } catch (Exception $e) {
            DB::rollback();
            Session::putFlash("danger", "Something went wrong.");
        }


        Redirect::back();
    }

    public function faqs()
    {
        $this->view("admin/faqs");
    }


    public function manual_credit()
    {
        $this->view("admin/manual_credit");
    }


    public function support_messages()
    {

        $this->view('admin/support-messages');
    }



    public function order_invoice($order_id = null)
    {

        $order  =  Orders::where('id', $order_id)->first();

        if ($order == null) {
            Redirect::back();
        }

        $order->getInvoice();
    }


    private function wallet_matters($extra_sieve, $class, $category = null)
    {

        $sieve = $_REQUEST;
        $sieve = array_merge($sieve, $extra_sieve);



        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  WalletFilter($sieve);



        $query = $class::latest()->Filter($filter);


        $total_credit  = $query->Credit()->Completed()->sum('amount');

        $query = $class::latest()->Filter($filter);
        $total_debit  = $query->Debit()->Completed()->sum('amount');

        $total_net = $total_credit - $total_debit;


        $query = $class::latest()->Category($category);
        $total_set = $query->count();
        // ->where('status', 1);  //in review


        $data = $query->Filter($filter)->count();

        // echo $query->toSql();/

        $records = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered



        $note = MIS::filter_note($records->count(), $data, $total_set,  $sieve, 1);

        return compact('records', 'sieve', 'data', 'per_page', 'note', 'total_debit', 'total_credit', 'total_net');
    }


    public function withdrawals()
    {

        $sieve = $_REQUEST;
        // $sieve = array_merge($sieve, $extra_sieve);

        $query = Withdrawal::latest();
        $total = $query->count();
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 100;
        $skip = (($page - 1) * $per_page);

        $filter = new  WithdrawalFilter($sieve);

        $data = $query->Filter($filter)->count();

        $withdrawals = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered

        $note = MIS::filter_note($withdrawals->count(), $data, $total,  $sieve, 1);

        $this->view('admin/withdrawal-history', compact('withdrawals', 'sieve', 'data', 'per_page', 'note'));
    }


    public function payout_wallets()
    {
        $compact = $this->wallet_matters([], 'v2\Models\Wallet');

        extract($compact);
        $page_title = 'Wallet';
        $wallet = 'payout';
        $this->view('admin/deposits', compact('records', 'sieve', 'data', 'per_page', 'page_title', 'wallet', 'note', 'total_credit', 'total_debit', 'total_net'));
    }


    public function commissions()
    {
        $compact = $this->wallet_matters(['earning_category' => 'commission'], 'v2\Models\Wallet');

        extract($compact);
        $page_title = 'Commissions';
        $wallet = 'commission';

        $this->view('admin/deposits', compact('records', 'sieve', 'data', 'per_page', 'page_title', 'wallet', 'note', 'total_credit', 'total_debit', 'total_net'));
    }


    public function ranks()
    {
        $compact = $this->wallet_matters([
            'earning_category' => 'rank'
        ], 'v2\Models\Commission');

        extract($compact);
        $page_title = 'Ranks Earning';

        $wallet = 'hotwallet';
        $this->view('admin/deposits', compact('records', 'sieve', 'data', 'per_page', 'page_title', 'wallet', 'note', 'total_credit', 'total_debit', 'total_net'));
    }



    public function deposits()
    {
        $compact = $this->wallet_matters([
            'earning_category' => 'deposit'
        ], 'v2\Models\Wallet');

        extract($compact);
        $page_title = 'Deposits';

        $wallet = 'deposit';
        $this->view('admin/deposits', compact('records', 'sieve', 'data', 'per_page', 'page_title', 'wallet', 'note', 'total_credit', 'total_debit', 'total_net'));
    }


    private function users_matters($extra_sieve)
    {

        $sieve = $_REQUEST;
        $sieve = array_merge($sieve, $extra_sieve);

        $query = User::latest();
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);


        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  UserFilter($sieve);

        $data = $query->Filter($filter)->count();

        $sql = $query->Filter($filter);

        // CategorySpoof::register_query($query);

        $users = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered


        $note = MIS::filter_note($users->count(), $data, User::count(),  $sieve, 1);

        return compact('users', 'sieve', 'data', 'per_page', 'note');
    }



    public function choose_membership($user_id = null, $subscription_id = null)
    {

        if (($user_id == null) || ($subscription_id == null)) {
            Session::putFlash("danger", "Invalid Request");
            Redirect::back();
        }

        $response = SubscriptionPlan::create_subscription_request($subscription_id, $user_id, null, true);
        Redirect::back();
    }



    public function user($user_id = null, $action = null)
    {


        if (($user_id == null) || ($action == null)) {
            Session::putFlash("danger", "Invalid Request");
            Redirect::back();
        }

        switch ($action) {
            case 'subscription':

                $user = User::find($user_id);
                $this->view('admin/user_subscription', compact('user'));
                return;
                break;

            default:
                # code...
                break;
        }


        Session::putFlash("danger", "Invalid Request");
        Redirect::back();
    }



    public function users()
    {


        $compact = $this->users_matters([]);
        extract($compact);
        $page_title = 'Users';

        $this->view('admin/users', compact('users', 'sieve', 'data', 'per_page', 'page_title', 'note'));
    }


    private function ticket_matters($extra_sieve)
    {

        $sieve = $_REQUEST;
        $sieve = array_merge($sieve, $extra_sieve);

        $query = SupportTicket::latest();
        $total_set = $query->count();
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


        $note = MIS::filter_note($tickets->count(), $data, $total_set,  $sieve, 1);

        return compact('tickets', 'sieve', 'data', 'per_page', "note");
    }



    public function open_tickets()
    {
        $sieve = ['status' => 0];
        $compact = $this->ticket_matters($sieve);
        extract($compact);
        $page_title = 'Open Tickets';

        $this->view('admin/all_tickets', compact('tickets', 'sieve', 'data', 'per_page', 'page_title'));
    }


    public function closed_tickets()
    {
        $sieve = ['status' => 1];
        $compact = $this->ticket_matters($sieve);
        extract($compact);
        $page_title = 'Closed Tickets';

        $this->view('admin/all_tickets', compact('tickets', 'sieve', 'data', 'per_page', 'page_title'));
    }


    public function update_cms()
    {

        DB::beginTransaction();

        try {

            CMS::updateOrCreate([
                'criteria' => $_POST['criteria']
            ], [
                'settings' => $_POST['settings'],
            ]);


            DB::commit();
            Session::putFlash("success", "Changes Saved");
        } catch (Exception $e) {
            DB::rollback();
            print_r($e->getMessage());
        }

        Redirect::back();
    }


    public function cms()
    {
        $this->view('admin/cms');
    }


    public function simulate_packages()
    {
        $this->view('admin/simulate_packages');
    }


    public function package_invoice($order_id = null)
    {

        $order = SubscriptionOrder::where('id', $order_id)->first();

        if ($order == null) {
            Redirect::back();
        }

        $order->invoice();
    }


    public function products_orders()
    {
        $sieve = $_REQUEST;
        $query = Orders::where('id', '!=', null)->latest();
        $sieve = array_merge($sieve);

        $page = (isset($_GET['page'])) ?  $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter =  new  OrderFilter($sieve);

        $data =  $query->Filter($filter)->count();

        $result_query = Orders::query()->Filter($filter);

        $total_amount  = $result_query->sum('amount_payable');

        $orders =  $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered

        $shown_total_amount  = $orders->sum('amount_payable');


        $shop = new Shop;

        $note = MIS::filter_note($orders->count(), ($data), (Orders::count()),  $sieve, 1);

        $this->view('admin/products_orders', get_defined_vars());
    }



    public function bulk_action_conversion_linting()
    {

        $records = ConversionLinting::whereIn('id', $_POST['records'])->get();

        DB::beginTransaction();
        try {
            foreach ($records as $key => $lint) {


                $lint->markAs($_POST['action']);
                $lint->update([
                    'admin_id' => $this->admin()->id,
                ]);
            }
            DB::commit();
            // Session::putFlash('success', "Lint marked as $status");
        } catch (Exception $e) {
            DB::rollback();
            // Session::putFlash('danger', "Something went wrong. Please try again.");
        }

        Redirect::back();
    }


    public function conversion_linting()
    {
        $sieve = $_REQUEST;
        $query = ConversionLinting::where('is_group', '0')->latest('updated_at');

        $total_set = $query->count();
        $sieve = array_merge($sieve);

        $page = (isset($_GET['page'])) ?  $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter =  new  ConversionLintingFilter($sieve);

        $data =  $query->Filter($filter)->count();


        $lintings =  $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered

        $note = MIS::filter_note($lintings->count(), ($data), $total_set,  $sieve, 1);

        $this->view('admin/conversion_linting', get_defined_vars());
    }



    public function fetch_subscription()
    {

        header("content-type:application/json");
        echo SubscriptionPlan::all();
    }


    public function order($order_id = null)
    {

        $order = Orders::where('id', $order_id)->first();


        if ($order == null) {
            Redirect::back();
        }

        $order->order_detail = $order->delivery_details();

        $this->view('admin/open_order', compact('order'));
    }


    public function update_subscription_plans()
    {


        foreach ($_POST['plan'] as $plan_id => $plan) {

            $subscription_plan = SubscriptionPlan::find($plan_id);
            $subscription_plan->update(['availability' => '']);
            print_r($subscription_plan->toArray());
            $subscription_plan->update($plan);
        }

        Session::putFlash("success", "Updated Succesfully.");

        Redirect::back();
    }


    public function products()
    {

        $this->view('admin/products');
    }



    public function fetch_documents_list()
    {


        $documents_settings = SiteSettings::documents_settings();

        header("content-type:application/json");

        $documents = ($documents_settings);


        echo json_encode(compact('documents'));
    }


    public function upload_supporting_document()
    {


        $documents_settings = SiteSettings::where('criteria', 'documents_settings')->first();

        $files = MIS::refine_multiple_files($_FILES['files']);


        foreach ($files as $key => $value) {
            $value['category'] = $_POST['category'][$key];
            $files[$key] = $value;
        }

        $combined_files = array_combine($_POST['label'], $files);

        Document::upload_documents($combined_files);
        // $response = $documents_settings->upload_documents($combined_files);
        Redirect::back();
    }


    public function delete_doc($id)
    {
        $document = Document::find($id);
        if ($document == null) {
            Session::putFlash("danger", "Document not found");
            Redirect::back();
        }

        DB::beginTransaction();
        try {

            $document->delete();
            DB::commit();
            Session::putFlash("success", "Document deleted succesfully");
        } catch (Exception $e) {
            Session::putFlash("danger", "Something went wrong");
        }

        Redirect::back();
    }


    public function delete_document($key)
    {

        $documents_settings = SiteSettings::where('criteria', 'documents_settings')->first();
        $response = $documents_settings->delete_document($key);
        header("content-type:application/json");

        echo json_encode(compact('response'));
    }


    public function confirm_payment($order_id)
    {

        $order = SubscriptionOrder::find($order_id);
        $status = $order->mark_paid();
        Redirect::back();
    }

    public function mark_subscription_unpaid($order_id)
    {

        $order = SubscriptionOrder::find($order_id);
        $status = $order->mark_unpaid();
        Redirect::back();
    }


    public function testimony()
    {

        $this->view('admin/testimony');
    }

    public function documents()
    {

        $all_documents = Document::all();
        // $documents_categories = Document::groupBy('category')->get()->pluck('category')->toArray();
        $documents_categories = Document::$categories;

        $show = true;
        $this->view('admin/documents', compact('show', 'all_documents', 'documents_categories'));
    }

    public function edit_testimony($testimony_id = null)
    {
        if (($testimony_id != null)) {
            $testimony = Testimonials::find($testimony_id);
            if (($testimony != null)) {

                $this->view('admin/edit_testimony', ['testimony' => $testimony]);
                return;
            } else {
                Redirect::to();
            }
        }
    }


    public function suspending_admin($admin_id = null)
    {

        $admin = Admin::find($admin_id);
        if ($admin == null) {
            Redirect::back();
        }


        if ($admin->is_owner()) {
            Session::putFlash('danger', "Invalid Request");
            Redirect::back();
        } else {

            $admin->delete();
            Session::putFlash('success', "Deleted Succesfully");
        }
        Redirect::back();
    }


    public function create_admin()
    {

        if (Input::exists()) {
        }

        $this->validator()->check(Input::all(), array(

            'firstname' => [

                'required' => true,
                'min' => 2,
                'max' => 20,
            ],
            'lastname' => [

                'required' => true,
                'min' => 2,
                'max' => 20,
            ],

            'email' => [

                'required' => true,
                'email' => true,
                'unique' => 'Admin'
            ],

            'username' => [

                'required' => true,

                'min' => 3,
                // 'one_word'=> true,
                'no_special_character' => true,
                'unique' => 'Admin',
            ],

            'phone' => [

                'required' => true,
                'min' => 9,
                'max' => 14,
                'unique' => 'Admin'

            ],

        ));

        if ($this->validator->passed()) {
            $admin = Admin::create([
                'firstname' => Input::get('firstname'),
                'lastname' => Input::get('lastname'),
                'email' => Input::get('email'),
                'phone' => Input::get('phone'),
                'username' => Input::get('username'),

            ]);
            if ($admin) {


                Session::putFlash('success', "Admin Created Succesfully.");
            }
        } else {


            Session::putFlash('info', Input::inputErrors());
        }
    }


    public function all_admins()
    {
        $admins = Admin::all();
        $this->view('admin/all_admins', compact('admins'));
    }



    public function add_admin()
    {


        $this->view('admin/add_admin');
    }


    public function administrators()
    {

        $this->view('admin/administrators');
    }


    public function accounts()
    {
        $this->view('admin/accounts');
    }



    public function profile($admin_id = null)
    {

        $admn  =  Admin::where('id', $admin_id)->first();
        if (($admn == null) || (($admn->is_owner())  && (!$this->admin()->is_owner()))) {

            Session::putFlash('danger', 'unauthorised access');
            Redirect::back();
        }

        $this->view('admin/profile', compact('admn'));
    }




    public function toggle_news($new_id)
    {

        $news = BroadCast::find($new_id);
        if ($news->status) {

            $update = $news->update(['status' => 0]);
            Session::putFlash('success', 'News unpublished succesfully');
        } else {

            $update = $news->update(['status' => 1]);

            Session::putFlash('success', 'News published succesfully');
        }

        Redirect::back();
    }


    public function delete_news($new_id)
    {

        $news = BroadCast::find($new_id);
        if ($news != null) {

            $update = $news->delete();
            Session::putFlash('success', 'Deleted succesfully');
        }


        Redirect::back("admin/news");
    }


    public function create_news()
    {

        print_r(Input::all());
        BroadCast::create([
            'broadcast_message' => Input::get('news'),
            'admin_id' => $this->admin()->id
        ]);
        Session::putFlash('success', 'News Created succesfully');

        Redirect::back();
    }


    public function broadcast()
    {
        $this->view('admin/broadcast');
    }


    public function viewSupportTicket($ticket_id)
    {

        $support_ticket_messages = SupportTicket::find($ticket_id)->messages;
        $support_ticket = SupportTicket::find($ticket_id);

        $this->view('admin/support-ticket-messages', [
            'support_ticket_messages' => $support_ticket_messages,
            'support_ticket' => $support_ticket
        ]);
    }


    public function create_testimonial()
    {

        if (Input::exists() || true) {

            $testimony = Testimonials::create([
                'attester' => Input::get('attester'),
                'content' => Input::get('testimony')
            ]);
        }
        Redirect::to("admin/edit_testimony/{$testimony->id}");
    }

    public function testimonials()
    {


        $sieve = $_REQUEST;
        // $sieve = array_merge($sieve, $extra_sieve);

        $query = Testimonials::latest()/*->where('video_link', '!=', null)*/;
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  TestimonialsFilter($sieve);

        $data = $query->Filter($filter)->count();

        $testimonials = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered

        $note = MIS::filter_note($testimonials->count(), ($data), (Testimonials::count()),  $sieve, 1);

        $this->view('admin/testimonials', compact('testimonials', 'sieve', 'data', 'per_page', 'note'));
    }


    public function publish_testimonial($testimonial_id)
    {

        $testimony = Testimonials::find($testimonial_id);
        if ($testimony->published_status) {

            $update = $testimony->update(['published_status' => 0]);
            Session::putFlash('success', 'Testimonial unpublished succesfully');
        } else {


            //check that this is approved
            if ($testimony->approval_status != 1) {

                Session::putFlash('danger', 'Testimonial must be approved before published. Please approve.');
                Redirect::back();
            }


            $update = $testimony->update(['published_status' => 1]);
            Session::putFlash('success', 'Testimonial published succesfully');
        }


        Redirect::back();
    }




    public function approve_testimonial($testimonial_id)
    {

        $testimony = Testimonials::find($testimonial_id);
        if ($testimony->approval_status) {

            $update = $testimony->update(['approval_status' => 0]);
            Session::putFlash('success', 'Testimonial disapproved succesfully');
        } else {

            $update = $testimony->update(['approval_status' => 1]);

            Session::putFlash('success', 'Testimonial approved succesfully');
        }


        Redirect::back();
    }

    public function delete_testimonial($testimonial_id)
    {

        $testimony = Testimonials::find($testimonial_id);
        if ($testimony != null) {

            $testimony->delete();
            Session::putFlash('success', 'Testimonial deleted succesfully');
        }


        Redirect::back();
    }


    public function update_testimonial()
    {

        echo "<pre>";
        $testimony_id = Input::get('testimony_id');
        $testimony = Testimonials::find($testimony_id);

        $testimony->update([
            'attester' => Input::get('attester'),
            'user_id' => $this->auth()->id,
            'content' => Input::get('testimony'),
            'type' => Input::get('type'),
            'video_link' => Input::get('video_link'),
            'intro' => Input::get('intro'),
            'approval_status' => 0
        ]);


        Session::putFlash('success', 'Testimonial updated successfully.');

        Redirect::back();
    }


    public function support_tickets()
    {
        $compact = $this->ticket_matters([]);
        extract($compact);
        $page_title = 'Tickets';


        $this->view('admin/all_tickets', compact('tickets', 'sieve', 'data', 'per_page', 'page_title', "note"));
    }




    public function companies()
    {
        $this->view('admin/companies');
    }


    public function testing()
    {
        $this->view('admin/sales');
    }


    public function settings()
    {
        $this->view('admin/settings');
    }


    public function user_profile($user_id = null)
    {

        if ($user_id == null) {
            Redirect::back();
        }


        $_SESSION[$this->auth_user()] = $user_id;

        $domain = Config::domain();
        $e = <<<EOL


				<style type="text/css">
					body {
	  				 margin: 0;
	   				overflow: hidden;
					}
					#iframe1 {
	   				 position:absolute;
	    				left: 0px;
	    				width: 100%;
	    				top: 0px;
	    				height: 100%;
					}
				</style>


	 		<iframe  id="iframe1" src="$domain/user/dashboard"></iframe>
EOL;

        echo "$e";
        // $this->view('admin/accessing_user_profile');
    }


    public function suspending_user($user_id)
    {


        if (User::find($user_id)->blocked_on) {

            $update = User::find($user_id)->update(['blocked_on' => null]);
            Session::putFlash('success', 'Ban lifted succesfully');
        } else {

            $update = User::find($user_id)->update(['blocked_on' => date("Y-m-d")]);

            Session::putFlash('success', 'User Blocked succesfully');
        }


        if ($update) {
        } else {
            Session::putFlash('flash', 'Could not Block this User');
        }


        Redirect::back();
    }


    public function dashboard()
    {
        $this->view('admin/dashboard');
    }


    public function factory()
    {
        $this->view('admin/factory');
    }


    public function membership_orders()
    {
        $sieve = $_REQUEST;
        // $sieve = array_merge($sieve, $extra_sieve);

        $query = SubscriptionOrder::latest();
        // ->where('status', 1);  //in review
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $filter = new  SubscriptionOrderFilter($sieve);

        $data = $query->Filter($filter)->count();
        $total_amount  = $query->Filter($filter)->sum('price');

        $subscription_orders = $query->Filter($filter)
            ->offset($skip)
            ->take($per_page)
            ->get();  //filtered


        $shown_total_amount  = $subscription_orders->sum('price');

        $shop = new Shop;
        $note = MIS::filter_note($subscription_orders->count(), ($data), (SubscriptionOrder::count()),  $sieve, 1);


        $this->view('admin/subscription_orders', get_defined_vars());
    }

    public function subscription_usage($order_id)
    {
        $sieve = $_REQUEST;
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);
        $subscription = SubscriptionOrder::find($order_id);
        $query = ConversionLog::where('bill_id', $order_id)->latest();


        $filter = new  ConversionLogFilter($sieve);

        $data = $query->Filter($filter)->get()->count();
        $convertion_logs = $query
            ->offset($skip)
            ->take($per_page)
            ->get();
        $note = MIS::filter_note($convertion_logs->count(), ($data), (ConversionLog::where('bill_id', $order_id)->count()),  $sieve, 1);
        $this->view('admin/subscription_usage', get_defined_vars());
    }

    public function conversion_logs()
    {
        $sieve = $_REQUEST;
        $sieve = array_merge($sieve);
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $per_page = 50;
        $skip = (($page - 1) * $per_page);

        $total_set = ConversionLog::count();
        $query = ConversionLog::query();


        $filter = new  ConversionLogFilter($sieve);


        $data = $query->Filter($filter)->count();


        $convertion_logs = $query
            ->offset($skip)
            ->take($per_page)
            ->get();


        $note = MIS::filter_note($convertion_logs->count(), ($data), ($total_set),  $sieve, 1);

        $this->view('admin/subscription_usage', get_defined_vars());
    }






    public function journals()
    {
        $sieve = $_REQUEST;
        extract(Journals::InvokeQuery($sieve));
        $this->view('admin/journals', get_defined_vars());
    }


    public function withdrawals_requests()
    {
        $sieve = array_merge($_REQUEST, [
            "tag" => "withdrawal",
            "notes" => "withdrawal",
            "latest" => "created_at",
        ]);

        extract(Journals::InvokeQuery($sieve));
        $this->view('admin/withdrawals_requests', get_defined_vars());
    }





    public function edit_journal($journal_id = '')
    {
        $journal = Journals::where('id', $journal_id)->where('company_id', 1)->first();


        if (!$journal->is_editable()) {
            Session::putFlash('info', "This Journal cannot be edited");
            Redirect::back();
        }

        $this->view('admin/edit_journal', get_defined_vars());
    }

    public function complete_journal($journal_id = '')
    {
        $journal = Journals::where('id', $journal_id)->where('company_id', 1)->first();


        if (!$journal->is_pending()) {
            Session::putFlash('info', "This Journal cannot be completed");
            Redirect::back();
        }
        $journal->completePending();

        Redirect::back();
    }

    public function decline_journal($journal_id = '')
    {
        $journal = Journals::where('id', $journal_id)->where('company_id', 1)->first();


        if (!$journal->is_pending()) {
            Session::putFlash('info', "This Journal cannot be completed");
            Redirect::back();
        }
        $journal->declinePending();

        Redirect::back();
    }


    public function reverse_journal($journal_id = '')
    {
        $journal = Journals::where('id', $journal_id)->where('company_id', 1)->first();


        if (!$journal->is_reversible()) {
            Session::putFlash('info', "This Journal cannot be completed");
            Redirect::back();
        }
        $journal->reverseJournal();

        Redirect::back();
    }



    public function view_journal($journal_id = '')
    {
        $journal = Journals::where('id', $journal_id)->where('company_id', 1)->first();


        if ($journal == null) {
            Session::putFlash('info', "Invalid request");
            Redirect::back();
        }

        $this->view('admin/view_journal', compact('journal'));
    }

    public function transactions($chart_of_account_id = null, $mode = 'base')
    {

        $sieve = $_REQUEST;
        $chart_of_account = ChartOfAccount::where('id', $chart_of_account_id)
            ->where('company_id', 1)
            ->first();

        $per_page = 50;
        $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $journal_sieve  = $sieve['journal'] ?? [];
        $line_items_sieve  = $sieve['line_items'] ?? [];

        $transactions = $chart_of_account->transactions($per_page, $page, $journal_sieve, $line_items_sieve);

        switch ($mode) {
            case 'base':
                $this->view('admin/transactions', get_defined_vars());
                break;

            default:
                $this->view('admin/transactions_local', get_defined_vars());
                break;
        }
    }




    public function e_wallets()
    {
        $sieve = $_REQUEST;
        extract(ChartOfAccount::InvokeQuery($sieve));
        $this->view('admin/bank_accounts', get_defined_vars());
    }
}
