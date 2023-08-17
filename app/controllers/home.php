<?php

use v2\Jobs\Job;
use v2\Shop\Shop;
use v2\Models\Api;
use v2\Models\Unit;
use v2\Classes\Trial;
use v2\Models\Applet;
use League\Csv\Reader;
use v2\Classes\UTrial;

use v2\Models\Wp\Post;
use v2\Classes\Bookies;
use app\models\UniOrder;
use v2\Models\BookMaker;
use v2\Classes\Countries;
use v2\Utilities\Code\Arr;
use v2\Jobs\Jobs\SendEmail;
use v2\Models\RememberAuth;
use v2\Classes\EventFetcher;
use v2\Models\ConvertedCode;
use v2\Utilities\Prediction;
use v2\Classes\Betking\Event;
use v2\Classes\ChainConverter;
use v2\Classes\QuickBetEditor;
use v2\Models\Wallet\Journals;
use CoinbaseCommerce\ApiClient;
use v2\Models\BetcodeConversion;
use v2\Classes\Sofascore\Sofascore;
use v2\Models\Wp\TermsRelationship;
use v2\Utilities\Affiliates\Payout;
use v2\Models\Wallet\ChartOfAccount;
use CoinbaseCommerce\Resources\Charge;
use v2\Filters\Filters\JournalsFilter;
use v2\Shop\Payments\Flutterwave\Rave;
use v2\Shop\Payments\Paypal\Subscription;
use v2\Classes\Bet9ja\Soccer\QuickUniMarket;
use v2\Models\Wallet\Classes\AccountManager;
use Illuminate\Database\Capsule\Manager as DB;
use v2\Jobs\Jobs\SendEmailForCreatedSupportTicket;
use v2\Jobs\Jobs\SendEmailForCreatedSupportMessage;
use v2\Classes\NearestEquivalent\Basketball\NearestEquivalent;

/**
 * this class is the default controller of our application,
 *
 */


class home extends controller
{


  public function __construct()
  {
  }


  public function withdrawal()
  {

    echo "<pre>";



    $payout = new Payout;
    $payout
      ->setMonth("2023-05")
      ->setCurrency()
      ->setSettings()
      ->getWallets()
      ->createWithdrawalRequests();



    return;

    DB::beginTransaction();
    /* 
    $sieve = array_merge($_REQUEST, [
      "tag" => "withdrawal",
      "notes" => "withdrawal",
      "user_id" => 1,
      "status" => 2,
           "journal_date" => [
        "start_date" => date("Y-m-d"),
        "end_date" => date("Y-m-d"),
      ]
 
    ]);


    $filter = new JournalsFilter($sieve);
    $query = Journals::latest();
    $query->Filter($filter);


    $journals = $query->Filter($filter)
      ->sum('c_amount');  //filtered

    print_r($journals);
    return;
 */


    // print_r($payout);


    return;
    // die;
    $auth = User::find(6);
    $amount_requested = 200;
    $method_details = v2\Models\UserWithdrawalMethod::for($auth->id, 'ngn_bank');

    // print_r($method_details->toArray());
    // print_r(SiteSettings::getAffiliateCommissionStructure());
    // return;
    // die;


    try {

      $withdrawal_account = $auth->getAccount('ngn_wallet');
      $withdrawal_request = [
        'withdrawal_account' => $withdrawal_account->id,
        'withdrawal_method' => $withdrawal_account->id,
        'amount' => $amount_requested,
        'status' => 2, //pending
        'collect_withdrawal_fee' => false,
        'narration' => "withdrawal",
        'journal_date' => null,
        'user_id' => $auth->id,
        'currency' => "NGN",
        'identifier' => "#ap#y-m-d#u",
        "method" =>  json_encode($method_details->toArray())
      ];

      print_r($withdrawal_request);
      // die;
      $request = AccountManager::withdrawal($withdrawal_request);


      $request->updateDetailsByKey('withdrawal_method', ($method_details->toArray()));

      $payables = [
        "amount" => $amount_requested,
        "payable" => $amount_requested,
      ];

      $request->updateDetailsByKey('payables', $payables);

      if (!$request) {
        throw new Exception("Error Processing Request", 1);
      }

      DB::commit();
      Session::putFlash('success', "Withdrawal initiated successfully");
    } catch (Exception $e) {
      DB::rollback();
      Session::putFlash('danger', "Something went wrong. Please try again.");
    }
  }


  public function test()
  {
    echo "<pre>";



    $data = [
      "title" => "This is the Title",
      "author" => "Author: Hohnkin pop",
      "chapters" => [
        [
          "title" => "title 1",
          "workshop" => "tworkshop",
          "exercises" => [
            "s" => "sssss",
            "no_1" => [
              [
                "question" => "q1.1",
              ],
            ],
            "no_2" => [
              [
                "question" => "q2.1",
              ],
            ],
          ],
        ],

        [
          "title" => "title 2",
          "workshop" => "tworkshop",
          "exercises" => [
            "no_1" => [
              [
                "question" => "q1.1",
                "ans" => "a1.1",
              ],
            ],
            "no_2" => [
              [
                "question" => "q2.1",
                "ans" => "a2.1",
              ],
              [
                "question" => "q2.1",
                "ans" => "a2.1",
              ],
            ],
          ],
        ],

      ],
    ];




    $sieve = [
      // "title" => true,
      "chapters" =>
      [
        "*" => [
          "title" => true,
        ]
      ],
    ];

    $data  = [

      "betarena:ng" => [
        "bookie" => "betarena:ng",
        "from" => "1",
        "to" => "0",
        "name" => "betarena -Nigeria",
        "brand" => "betarena",
        "img" => "https://www.betarena.ng/content/betarena-b3t4ar/uploads/2021/07/logo.png"
      ],
      "luckybet:ng" => [
        "bookie" => "luckybet:ng",
        "from" => "1",
        "to" => "0",
        "name" => "luckybet -Nigeria",
        "brand" => "luckybet",
        "img" => "https://www.luckybet.ng/content/luckybet-l4kbet/uploads/2020/11/luckybet_logo_260x48grdot.png"
      ],
      "betxperience:ng" => [
        "bookie" => "betxperience:ng",
        "from" => "1",
        "to" => "0",
        "name" => "betxperience -Nigeria",
        "brand" => "betxperience",
        "img" => "https://www.betxperience.com/content/betxperience-8et3xp/uploads/2020/12/BETXP.png"
      ]


    ];




    $sieve = [
      "*" => [
        "bookie" => "betxperience:ng",
        "from" => "1",
        // "to" => "0",
        "name" => "betxperience -Nigeria",
        "brand" => "betxperience",
      ]
    ];


    print_r(Arr::deepKeySift($data, $sieve));





    return;
    $c = BetcodeConversion::first();


    var_dump($c->usedNearestEquivalent());

    return;


    echo $t = "^1^ To Save Match Point And Win The Match - YES";
    preg_match_all("/(.*1.*yes)/i", $t, $matches);

    print_r($matches);




    return;
    $pvs  = [
      "total=41.5|quarternr=1",
      "total=4.5",
      "total=45",
      "total=45",
    ];

    $r = [];
    foreach ($pvs as $key => $value) {

      preg_match("/(?<=total\=)\d+\.+\d|(?<=total\=)\d+/", "$value", $matches);

      print_r($matches);
    }

    print_r($r);

    // $c = BetcodeConversion::find(8);
    // print_r($c->getMergedListOfConvertedEvents());


    return;
    $needle = [
      "item_name" => "Yellow-Red KV Mechelen - Gent",
      "home_team" => "Yellow-Red KV Mechelen",
      "away_team" => "Gent",
      "sport_id" => 1,
    ];

    $haystack = [
      [
        "item_name" => "Coritiba - Red Bull Bragantino",
        "home_team" => "Coritiba",
        "away_team" => "Red Bull Bragantino",
        "sport_id" => 1,
      ]
    ];


    $self_bookie_entries = [];
    $exempted_events = [];

    EventFetcher::findEventsMatch(null, [$needle], $haystack, $self_bookie_entries, $exempted_events);


    print_r(compact('self_bookie_entries'));
    // print_r(compact('exempted_events'));



    return;
    $c = BetcodeConversion::find(295910);


    print_r($c->getMergedListOfConvertedEvents());
    return;

    // isset()

    $ne = new NearestEquivalent;

    /* 
    $ne->setUniformPrediction([
      "translated_market" => '_1x2',
      "translated_prediction" => '2',
      "translated_point_value" => null,
    ])
      ->setSportId('soccer')
      ->setPreferredMarkets(['double_chance'])
      ->getNearestEquivalent();

 */



    $prediction = [
      "translated_market" => 'over_under_incl_overtime',
      "translated_prediction" => 'o',
      "translated_point_value" => 173.5,
    ];


    $r = $ne
      ->setPreferredMarkets(['over_under_incl_overtime'])
      ->setSpecAvailability([170, 171, 172, 173, 174, 175, 176, 177])
      ->setUniformPrediction($prediction)
      ->getNearestEquivalent();


    print_r($prediction);
    var_dump($r);
    print_r($ne->getUniformEquivalent());


    return;
    $t1 = "under + gg";
    $t2 = "over and gg";

    $s1 = preg_split("/(\+|and)/i", $t1);
    $s2 = preg_split("/(\+|and)/i", $t2);
    print_r(compact('s1', 's2'));
    print_r(json_encode($t1));
    print_r(json_encode($s2));
    die;

    $pattern = "/(Goals Away Team|Home Exact Goals)/i";
    preg_match("$pattern", "Home Exact Goals", $m);
    print_r($m);

    preg_match("$pattern", "Goals Away Team", $m);
    print_r($m);


    die;


    BookMaker::updateOrCreate(['name' => "pixwin", "key_name" => 'pixwin'], []);
    BookMaker::updateOrCreate(['name' => "betcoza", "key_name" => 'betcoza'], []);
    BookMaker::updateOrCreate(['name' => "sportpesa", "key_name" => 'sportpesa'], []);




    return;
    $sub = SubscriptionOrder::Paid()->first();

    print_r($sub->details);

    return;
    var_dump("O" == "o");

    return;
    //transform
    $texts = ["over/under (2.5)", "total goals",  "over/under (0.5)", "over/under (3.5)"];

    // $r = ["total goals 2.5", "total goals 0.5", "total goals 3.5"];

    $r = [];


    foreach ($texts as $key => $market) {
      $pattern = "/over\/under \((\d+.\d+)\)/i";
      preg_match($pattern, $market, $matches);
      // preg_match_all($pattern, $market, $matches);
      if (!isset($matches[0])) {
        continue;
      }


      $new_market = "total goals {$matches[1]}";

      $r[$key] = str_ireplace($market, $new_market, $market);
      print_r($matches);
    }
    print_r($r);


    die;

    $conversion = BetcodeConversion::find(85);

    print_r($conversion->bookieKeys());

    die;
    $markets = EventFetcher::getQuickUniMarkets("soccer", "bet9ja");

    print_r($markets);


    die;
    $r = EventFetcher::convertToUTC("2023-05-28 16:30:00", "ke");
    print_r($r);

    echo "<br>";
    echo gmdate("Y-m-d H:i:s");
    return;

    $order = SubscriptionOrder::first();

    echo $this->view('emails/order_follow_up', compact('order'), true, true);


    return;

    $user = User::find(1);
    $notice_message = "Erro";

    $body = (new controller)->buildView('emails/low_unit_notification', compact('user', 'notice_message'));

    echo $body;

    return;


    $today  = date("Y-m-d 23:59:59");
    $rollover_settings = SiteSettings::SubRollOverSettings();
    $rollover_within_x_days = $rollover_settings['rollover_within_x_days'];
    $date_x_days_ago = date("Y-m-d H:i:s", strtotime("$today -$rollover_within_x_days"));



    print_r($date_x_days_ago);


    //get last previously expired unit as rollover
    $last_sub =  SubscriptionOrder::where('user_id', 1)
      ->Paid()
      ->where('rolled_over', '0')
      ->where('units', '>', 0)
      ->whereBetween('expires_at', [$date_x_days_ago, $today])
      ->Expired()
      ->latest('paid_at')
      ->first();


    print_r($last_sub->toArray());

    return;
    define('MIN_NO_OF_DRAWS',  7);


    echo MIN_NO_OF_DRAWS;


    return;

    $codes = Rave::getBankCodes();

    print_r($codes);

    return;
    print_r(getcwd());

    //load the CSV document from a file path
    $path = "./../app/models/v2/Shop/Payments/Flutterwave/disburse_bank_codes.csv";
    $csv = Reader::createFromPath($path, 'r');
    $csv->setHeaderOffset(0);

    $header = $csv->getHeader(); //returns the CSV header record
    $records = $csv->getRecords();


    print_r(collect($records)->pluck('Currency')->unique());
    print_r(collect($records)->groupBy('Currency'));

    return;

    $bal = ChartOfAccount::find(3)->get_balance("2022-12-31");

    print_r($bal);

    die;



    return;
    /* $v = AvailableCurrency::available()->orderBy('code')->get()->toArray();
    $v = SiteSettings::AvailableCurrencies()->toArray();
    $v = Config::currency('code');
    print_r($v);

 */

    $shop = new Shop();
    $order = SubscriptionOrder::find(56);

    $shop->setOrder($order)->reVerifyPayment();




    return;


    die;
    $conversion = BetcodeConversion::find(4);
    $predictions = new Prediction($conversion);
    $predictions->extractEvents();
    $predictions->saveEvents();


    echo
    SubscriptionOrder::where('user_id', 1)
      ->Paid()
      ->PlanPricing()
      ->NotExpired()
      // ->Active()
      ->orderBy('plan_id', 'DESC')
      ->oldest('expires_at')
      ->toSql();



    return;
    $domain = Config::domain();

    $dom = new DomDocument();

    $html = file_get_contents("$domain/test.html");

    // $html = str_ireplace(['id="bettingtabs"', 'id="SearchEventsWidgetLoader"'], "", $html, $count);

    $dom->loadHTML($html);
    $dom->normalizeDocument();
    $dom->preserveWhiteSpace = false;
    $xpath =  new \SimpleXMLElement($dom->saveXML());

    ob_clean();
    echo "<pre>";

    $views = $xpath->xpath('//*[@id="active-outcomes-list"]/div');

    $searched_elements = [];
    foreach ($views as $view) {
      $team_name =  $view->xpath("div/div[1]/span")[0]->__toString();
      $item_name = strtolower(trim(preg_replace(['/\n/', '/\s+/'], ' ', $team_name)));

      $e = explode(" - ", $item_name);
      $odds_value =  trim($view->xpath('div/div[2]/span')[0]->__toString());


      $outcome = trim($view->xpath('div/div[1]/div/span')[0]->__toString());
      $outcome = explode(" ", $outcome);
      array_pop($outcome);
      $outcome = trim(implode(" ", $outcome));

      $market = $view->xpath('div/div[1]/div/span')[1]->__toString();
      $market = strtolower(trim(preg_replace(['/\n/', '/\s+/'], ' ', $market)));

      $searched_elements[] = [
        "item_id" => ((array)$view)["@attributes"]['data-betslip-id'],
        "full_id" => ((array)$view)["@attributes"]['data-betslip-win'],
        'item_name' => $item_name,
        'home_team' => $e[0],
        'away_team' => $e[1],
        'market_name' => "$market",
        'outcome_name' => $outcome,
        'odds_value' =>  $odds_value,
      ];
    }
    /* 
'is_uniform' => true,
'find_code' => $find_code,
'bet_code' => $item['EventID'],
'item_id' => $item['EventID'],
'item_name' => $item_name,

'home_team' => $home_team,
'away_team' => $away_team,

'item_date' => $item_date,
'parent_id' => null,

'tournament_id' => null,
'tournament_name' => $item['LeagueName'],


'odds_collection' => [],
'sport_id' =>  $sport_category['name'] ?? null,
'sport_category' =>  $sport_category,
    
     */


    print_r($searched_elements);

    return;


    /* 
    
    
    [market_name] => 1X2
    [outcome_name] => Home
    [prediction_id] => _1x2::1
    
    ------to be generated------
    [sport_category] => 
    [is_uniform] => 1
    [item_name] => Leverkusen - Ferencvarosi Budapest
    [odd_value] => 1.33
            [find_code] => lev#fer
            [tournament_name] => UEFA Europa League
            [category_name] => International Clubs
    
     */


    $pay_load = [
      "events" => [
        [
          "home_team" => "Man Utd ",
          "away_team" => "Betis",
          "item_date" => "2023-03-09 20:00:00",
          "sport_id" => "soccer",
          "pick" => [
            "market" => "over_under",
            "prediction" => "o",
            "specifier" => 1.5,
          ],
        ],
      ],
      "destination_bookie" => "sportybet:ng"
    ];



    $bet_editor = new QuickBetEditor;
    $response = $bet_editor
      ->setHomeBookie()
      ->setUniformLinesFromAPI($pay_load['events'])
      ->setDestinationBookie($pay_load['destination_bookie'])
      ->setModel()
      ->edit();

    ob_clean();

    echo "<pre>";
    print_r($response->dest_entries);

    die;
    /* 
      ->setConversionId($conversion_id)
      ->edit();
 */


    return;
    $conversion = BetcodeConversion::find(56);

    $attempted_bookies = $details['attempted_bookies'] ?? [];
    $destination_bookies = $details['destination_bookies'] ?? [];
    $c = $conversion->getMergedListOfConvertedEvents();
    $home_bookie = $c['home']['bookie'];
    $destination_bookie = $c['destination']['bookie'];


    print_r($attempted_bookies);
    print_r($destination_bookie);


    die;

    $markets = EventFetcher::getQuickUniMarkets("soccer", "bet9ja");




    print_r($markets);

    die;
    /* EventFetcher::checkDictionaryForMatch('xxx', 'yyy');
    return; */


    $qb = new QuickBetEditor;

    $r = $qb->loadBookingCode("375f7528", "sportybet:ng", "bet9ja:ng");

    print_r($r);


    return;

    print_r(explode(":", "_1x2::x"));

    $markets = EventFetcher::getQuickUniMarkets("soccer", "bet9ja");

    // var_dump($markets);

    print_r($markets);


    die;
    $pre_extractor = new Prediction(BetcodeConversion::find(9));
    $pre_extractor->extractEvents()->saveEvents();


    die;
    $bookies = (new Bookies)->getAvailabilityOfTo();


    echo "<prE>";

    print_r($bookies);


    return;
    // echo ConvertedCode::all();
    echo (new ConvertedCode)->table;

    die;
    $response = BetcodeConversion::find(70);
    $view = $this->buildView('guest/edit_response_view', compact('response'), true, true);

    echo $view;
    return;

    echo '<pre>';


    $m = EventFetcher::getSportCategory('soccer', 'bet9ja');



    print_r($m);

    die;

    print_r($m);


    return;
    $uni_markets = new QuickUniMarket;

    $m = $uni_markets->getMarkets();


    print_r($m);


    return;

    echo date("Y-m-d", "1676663100");
    echo "<br>";
    echo date("Y-m-d", "1677009600");
    return;

    $home_team = 'ac milan';
    $away_team = 'Tottenham';


    $is_match = EventFetcher::checkSubstringMatch(
      compact('home_team', 'away_team'),
      ["home_team" => "ac milan", "away_team" => "tottenham hotspur"],
    );


    var_dump($is_match);

    return;

    $p1_pattern = "/1 Half - (W1|W2|X) \+ Total 2/i";
    $string = "1 Half - W2 + Total 2";
    preg_match($p1_pattern, $string, $matches);
    // preg_match("/\d+\s*\-*\s*\d+/i", $string, $matches);
    // preg_match("/(?<=\()(\d+\.\d+|\d+)(?=\))/i", $string, $matches);
    print_r($matches);


    return;
    // $order = SubscriptionOrder::find(6);
    // $order->give_affiliate_commission();
    $commission = Journals::find(18);

    echo  $this->view('emails/commission_paid', compact('commission'));

    return;

    try {
      //code...
      $auth = User::find(1);
      // $auth = false;;
      $trial = new UTrial;
      $trial->setUser($auth);
      $charge = !$trial->canTry();

      var_dump($charge);
      $trial->countAttempt();

      // $charge = true;
      $trial_left  = $trial->trialLeft();

      var_dump($trial_left);
    } catch (\Throwable $th) {

      print_r($th->getMessage());
    }
    return;

    /*     $withdrawal = Journals::find(5);
    echo $this->view('emails/completed_withdrawal', compact('withdrawal'));
    return;



    die;
    // print_r(User::find(1)->activeSubscriptions()->first()->toArray());
    print_r(User::find(1)->subscription->toArray());


    return;

    print_r(SubscriptionOrder::find(23)->details);
    print_r(SiteSettings::getPlans());

    return;

    BookMaker::updateOrCreate(['name' => "frapapa", "key_name" => 'frapapa'], []);
    BookMaker::updateOrCreate(['name' => "betxperience", "key_name" => 'betxperience'], []);
    BookMaker::updateOrCreate(['name' => "betarena", "key_name" => 'betarena'], []);
    BookMaker::updateOrCreate(['name' => "luckybet", "key_name" => 'luckybet'], []);

    return;
    echo    $expires_at = date("Y-m-d", strtotime("+100 months"));

    return;

    echo (int)  strtotime("2022/11/23 13:00:00") > time();

    die;
    $conversion = BetcodeConversion::first();
    print_r($conversion->bookieKeys());

    die;

    $user = User::find(1);

    print_r($user->subscriptions->first()->toArray());

    return;
    print_r(SiteSettings::getSubscriptionPlans());
    print_r(User::find(1)->activeSubscriptions());
    return;
    $response = SubscriptionOrder::find(27);

    print_r($response->mark_paid());


    return;
    // print_r(SiteSettings::getAffiliateCommissionStructure());
    $commission = 20;
    $identifier = "sss";
    $comment = "test";
    $tag = "affiliate_commission";
    $payment_currency = "ngn";

    $receiver = User::find(1);


    /*  
    $account =  $receiver->getAccount('ngn_wallet');

    $today_journal_filter = [
      "journal_date" => [
        "start_date" => date("Y-m-d"),
        "end_date" => date("Y-m-d"),
      ]
    ];
    $response = ($account->transactions(100, 1, $today_journal_filter, []));
    $earnings_today = $response['total_credit'];


    $this_month_journal_filter = [
      "journal_date" => [
        "start_date" => date("Y-m-01", time()),
        "end_date" => date("Y-m-t", time()),
      ]
    ];

    $response = ($account->transactions(100, 1, $this_month_journal_filter, []));
    $earnings_this_month = $response['total_credit'];



    return;
    print_r($response['transactions']->toArray());
    print_r($response['transactions']->toArray());
    print_r($account->get_balance());
    return;
    $line =  AccountManager::payAffiliateCommission([
      'receiver' => $receiver,
      'amount' => $commission,
      'identifier' => $identifier,
      'narration' => $comment,
      'currency' => $payment_currency,
      'tag' => $tag,
    ]);
    return;

    $order = UniOrder::find(30);
    $order->give_affiliate_commission();

    return;

    // echo $user->getAccount('unit_wallet');
    die;
 */

    $domain = Config::domain();

    $dom = new DomDocument();

    $html = file_get_contents("$domain/test.html");

    $html = str_ireplace(['id="bettingtabs"', 'id="SearchEventsWidgetLoader"'], "", $html, $count);

    $dom->loadHTML($html);
    $dom->normalizeDocument();
    $dom->preserveWhiteSpace = false;
    $xpath =  new \SimpleXMLElement($dom->saveXML());
    $views = $xpath->xpath("//*[@data-value]");
    echo "<pre>";
    $searched_elements = [];
    foreach ($views as $view) {
      $view = (array)$view;
      $attributes = $view['@attributes'];

      $item_name = strtolower(trim($attributes['data-eventtitle']));
      $home_team = trim(explode(" v ", $item_name)[0]);
      $away_team = trim(explode(" v ", $item_name)[1]);
      $item_date = strtolower(trim($attributes['data-eventdate']));
    }

    // print_r($xpath);
    die;
    $images = $xpath->xpath("//img");

    foreach ($images as $key => $img) {
      $img->attributes()->class = "{$img->attributes()->class} img-fit-cover img-object-top rounded-left";
    }
    // print_r($img[0]->attributes()->class);

    print_r(strip_tags($xpath->asXML()));
    return;
    $applet = Applet::find(26);

    echo $applet->HashId;

    echo Applet::FindByHashId("nm");
    return;

    $lines = '
  ';

    $message = "Some markets are not available: Winner, Winner, Both halves under 1.5, Winner";


    $items_strings = str_replace("a market of", "", explode(": ", $message)[1]);
    $items = explode(",", $items_strings);
    // print_r($items);
    $unavailable_items = array_map(function ($item) {

      $item_name = str_ireplace(" vs. ", " - ", strtolower(trim($item)));
      $teams = explode(" vs. ", $item);
      $home_team = strtolower(trim($teams[0]));
      $away_team = strtolower(trim($teams[1]));
      $find_code = EventFetcher::getFindCode($home_team, $away_team);

      $item = [
        'find_code' => $find_code,
        'bet_code' =>  null,
        'item_id' =>  null,
        'item_name' => $item_name,
        'home_team' => $home_team,
        'away_team' => $away_team,
        'item_date' => null,
      ];

      return $item;
    }, $items);

    // print_r($unavailable_items);

    $postable_lines = json_decode($lines, true)["postable_lines"];

    // print_r($postable_lines);
    // die;

    $allowed_lines = array_filter($postable_lines, function ($item) use ($unavailable_items) {
      $item_name = $item['item_name'];

      $teams = explode(" - ", $item_name);
      $home_team = strtolower(trim($teams[0]));
      $away_team = strtolower(trim($teams[1]));
      $find_code = EventFetcher::getFindCode($home_team, $away_team);
      $item_date = $item["item_utc_date"];


      $item = [
        'find_code' => $find_code,
        'bet_code' =>  null,
        'item_id' =>  null,
        'item_name' => $item_name,
        'home_team' => $home_team,
        'away_team' => $away_team,
        'item_date' => $item_date,
      ];

      $found_matches = [];
      $exempted_events = [];
      EventFetcher::findEventsMatch(null, [$item], $unavailable_items, $found_matches, $exempted_events);

      // print_r($found_matches);
      if (($found_matches) == []) {
        return true;
      }


      //record into DB
      return false;
    });

    // print_r($unavailable_items);
    // print_r($postable_lines);
    print_r(count($allowed_lines));
    print_r($allowed_lines);

    $ip = gethostbyname("betway.co.za");
    echo "$ip";
    /* 
    echo $api = Api::find(6);

    echo json_encode([
      "key" => ($api->generateSmartKey())
    ]);

 */

    print_r($tzlist);

    echo EventFetcher::convertToUTC("2022-09-24 13:00:00", "gh");

    return;
    $conversion = BetcodeConversion::find(10135);

    print_r(($conversion->getMergedListOfConvertedEvents()));


    return;
    $domain = Config::domain();
    $dom = new DomDocument();

    $html = file_get_contents("$domain/fulltest.html");
    //fetch html and sanitise
    $html = str_ireplace([
      'id="accordion"',
    ], "", $html, $count);

    $dom->loadHTML($html);
    $dom->normalizeDocument();
    $dom->preserveWhiteSpace = false;
    $xpath =  new \SimpleXMLElement($dom->saveXML());


    $sr_market = "18~total=2.5";

    $views = $xpath->xpath("//*[@data-srmarketfeedid='$sr_market']");

    $views = (array)$views;


    //match betway expected selection id
    $outcome = null;
    foreach ($views as  $selection) {
      $selection = (array)$selection;
      if ($selection['@attributes']['data-sroutcomefeedid'] == 12) {
        $outcome = $selection['@attributes']['id'];
        break;
      }
    }
    $outcomes[] = $outcome;


    print_r($outcomes);

    return;
    foreach ($views as $key => $view) {
      $view = (array)$view;
      $attributes = $view['@attributes'];
      print_r($attributes);
    }

    return;
    // print_r($xpath);

    $i = 1;
    foreach ($xpath as $key => $node) {
      # code...
      print_r($node);
      echo $i++;
    }
    //code...


    /* /html/body/h3[3] */


    $shop = new Shop();
    $order = SubscriptionOrder::first();

    if ($order == null) {
      echo "already paid";
      return;
    }

    $shop->setOrder($order)->reVerifyPayment();




    return;
    BookMaker::updateOrCreate(['name' => "betpawa", "key_name" => 'betpawa'], []);
    return;
    BookMaker::updateOrCreate(['name' => "spotika", "key_name" => 'spotika'], []);


    return;

    $bookies = new Bookies;
    print_r($bookies->bookies_by_regions);


    return;

    DB::raw("select JSON_EXTRACT(game_data, '$.player_assets.units') from games");
    return;

    // $timezone_abbreviations = DateTimeZone::listAbbreviations();
    // $timezone_identifiers = DateTimeZone::listIdentifiers();
    $r = BetcodeConversion::whereRaw("
        `home_bookie_id` = 1 
        and `dest_bookie_id` = 5
        and `booking_code` = 'EEDU5'
        and `bookies_train` = 'ng/ng' 
        and (`created_at` BETWEEN '2022-03-13 00:00:00' and '2022-03-13 23:59:59')
        order by `id` desc
        LIMIT 1");

    // echo $r->get();


    echo BetcodeConversion::getModel(2, 5, "EEDU5", "ng/ng", "2022-03-13");
    return;
    echo EventFetcher::convertToUTC("2022-01-01 00:00:00 +01:00", "ng");
    echo "\n";
    echo EventFetcher::convertToUTC("2022-03-12 16:00:00", "ng");


    return;
    $cairo = new DateTimeZone("Africa/cairo");
    $utc = new DateTimeZone("UTC");
    $date = new DateTime("2011-01-01 15:00:00 +01:00");
    $date->setTimezone($cairo);


    echo $date->format('Y-m-d H:i:s T');
    echo "\n";

    $date->setTimezone($utc);
    echo $date->format('Y-m-d H:i:s');

    return;

    $cc = new ChainConverter;
    $conversion =   $cc
      ->setCode("BCBJ71Q9")
      ->setChains(['sportybet:ng', "betika:ng", "msport:ng"])
      ->setModel()
      ->ReadyPostableLines();

    return;



    return;

    return;
    echo MIS::make_get("https://odibets.com.gh/api/bets?id=4K9I3A8&ref=share");
    $string = "Some markets are not available: a market of Crusaders FC vs. Glenavon FC";
    $string = str_ireplace("Some markets are not available: a market of", "", $string);
    print_r($string);


    return;

    $r =  json_decode("wqewqwrqw", true);
    print_r($r);
    // $r =  json_decode('{"d":"ds"}', true);
    print_r(EventFetcher::detectNumber("76-90+"));
    preg_match("/from (\d+) to (\d+)/", "first goal in the interval from 71 to 80 minutes", $matches);

    return;
    print_r(EventFetcher::detectNumber("3+"));
    print_r(EventFetcher::detectNumber("-3"));

    print_r(EventFetcher::detectNumber("3>="));
    print_r(EventFetcher::detectNumber("3=>"));
    print_r(EventFetcher::detectNumber(">=3"));
    print_r(EventFetcher::detectNumber("=>3"));
    print_r(EventFetcher::detectNumber("3>"));
    print_r(EventFetcher::detectNumber(">3"));

    print_r(EventFetcher::detectNumber("3<="));
    print_r(EventFetcher::detectNumber("3=<"));
    print_r(EventFetcher::detectNumber("<=3"));
    print_r(EventFetcher::detectNumber("=<3"));
    print_r(EventFetcher::detectNumber("3<"));
    print_r(EventFetcher::detectNumber("<3"));

    print_r(EventFetcher::detectNumber("3"));
  }



  public function contact_us()
  {
    // verify_google_captcha();

    echo "<pre>";
    print_r($_REQUEST);
    extract($_REQUEST);

    Input::exists();


    $client = User::where('email', $_POST['email'])->first();
    $support_ticket = SupportTicket::create([
      'subject_of_ticket' => $_POST['comment'],
      'user_id' => $client->id,
      'customer_name' => $_POST['full_name'],
      'customer_phone' => $_POST['phone'],
      'customer_email' => $_POST['email'],
      'department' => $_POST['department'],
    ]);

    $code = $support_ticket->id . MIS::random_string(7);
    $support_ticket->update(['code' => $code]);
    //log in the DB


    //queue notification
    $email_job = (new SendEmailForCreatedSupportTicket)->setUpWith($support_ticket);
    Job::schedule($email_job);


    Session::putFlash('success', "Message sent successfully.");

    Redirect::back();

    die();
  }


  /**
   * [flash_notification for application notifications]
   * @return [type] [description]
   */
  public function flash_notification()
  {
    header("Content-type: application/json");

    if (isset($_SESSION['flash'])) {
      echo json_encode($_SESSION['flash']);
    } else {
      echo "[]";
    }


    unset($_SESSION['flash']);
  }


  public function close_ticket()
  {
    $ticket = SupportTicket::where('code', $_REQUEST['ticket_code'])->first();
    $ticket->mark_as_closed();
    Redirect::back();
  }


  public function support_message()
  {

    $project_name = Config::project_name();
    $domain = Config::domain();

    $settings = SiteSettings::site_settings();
    $noreply_email = $settings['noreply_email'];
    $support_email = $settings['support_email'];


    $files = MIS::refine_multiple_files($_FILES['documents']);

    $ticket = SupportTicket::where('code', $_POST['ticket_code'])->first();
    $ticket->update(['status' => '0']);

    $message = SupportMessage::create([
      'ticket_id' => $ticket->id,
      'message' => $_POST['message'],
    ]);


    $message->upload_documents($files);

    $support_email_address = "$support_email";
    $_headers = "From: {$ticket->customer_email}";

    $client_email_message = "Dear Admin, Please respond to this support ticket on the admin <br>
	                            From:<br>
	                            $ticket->customer_name,<br>
	                            $ticket->customer_email,<br>
	                            $ticket->customer_phone,<br>
	                            Ticket ID: $ticket->code<br>
	                            <br>
	                             ";
    $client_email_message .= $message->message;

    $client_email_message = $ticket->compile_email($client_email_message);

    $mailer = new Mailer();

    $mailer->sendMail(
      "$support_email_address",
      "$project_name Support - Ticket ID: $ticket->code",
      $client_email_message,
      "Support"
    );

    Redirect::back();
  }


  public function index($page = null)
  {

    switch ($page) {
      case 'supportmessages':

        $this->view('guest/support-messages');

        break;

      case null:



        $this->view('guest/index', get_defined_vars());

        break;

      default:

        $this->view('guest/error-404');
        break;
    }
  }
}
