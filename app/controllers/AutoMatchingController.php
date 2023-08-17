<?php

use v2\Models\Job;
use v2\Models\Tip;

use v2\Models\Event;
use v2\Models\BookMaker;
use v2\Models\Investment;
use v2\Jobs\Job as Worker;
use v2\Classes\TipsFactory;
use v2\Classes\EventFetcher;
use v2\Utilities\Prediction;
use v2\Models\SportsCategory;
use v2\Models\BetcodeConversion;
use v2\Models\ConversionLinting;
use Illuminate\Database\Capsule\Manager as DB;

/**
 */
class AutoMatchingController extends controller
{

    public function __construct()
    {
        // $this->settings = SiteSettings::all()->keyBy('criteria');

    }

    /**
     * called through cronjobs
     * Loops through successful conversions 
     * & records participating bookies 
     * We use this to detect bookies with no activity(may failing bookie)
     *
     * @return void
     */
    public function bookies_active_states()
    {

        //record all bookies in the system update or create
        //loops through successful conversion, record/update time for each bookie
        //









    }

    /**
     * called through a cronjob
     * It extracts lines predictions from successful conversions and 
     * marks them as treated
     *
     * @return void
     */
    public function lines_prediction_extraction()
    {
        $per_page = 25;
        $non_extracted_conversions = (BetcodeConversion::NotExtracted()->take($per_page)->get());

        foreach ($non_extracted_conversions as $key => $conversion) {
            if ($conversion->isExtracted()) {
                continue;
            }
            $predictions = new Prediction($conversion);
            $predictions->extractEvents();
            $predictions->saveEvents();
        }
    }


    /**
     * called through cronjob
     *Loops through successful conversions
     * and detects possibility for wrong conversions e.g wrong markets, picks 
     * 
     * @return void
     */
    public function converted_market_linting()
    {

        $per_page = 25;
        $non_linted_conversions = (BetcodeConversion::NotLinted()->take($per_page)->get());

        foreach ($non_linted_conversions as $key => $conversion) {
            $conversion_array = $conversion->toArray();
            $home = collect($conversion['dump']['home_bookie_entries']['uniform_event'])->keyBy('find_code')->toArray();
            $destination = collect($conversion['dump']['destination_bookie_entries']['converted_booking']['uniform_event'])->keyBy('find_code')->toArray();



            foreach ($home as $key => $home_event) {

                $f_cd = [];
                $not_found = [];
                @EventFetcher::findEventsMatch(null, [$home_event], $destination, $f_cd, $not_found, false);
                if ($f_cd != []) {
                    $match = end($f_cd);

                    $home_selection = $match['for_booking']['all'];
                    $destination_selection = $match['translated_prediction'];

                    @$both_market_present = ($home_selection["translated_market"] != null) && ($destination_selection['translated_market'] != null);
                    if (!$both_market_present) {
                        continue;
                    }

                    $wrong_market = $home_selection["translated_market"] != $destination_selection['translated_market'];
                    $wrong_selection =  json_encode($home_selection["translated_prediction"]) != json_encode($destination_selection['translated_prediction']);


                    $bookie_keys = ($conversion->bookieKeys());
                    $home_bookie = $bookie_keys['home'];
                    $destination_bookie = $bookie_keys['destination'];


                    $not_in_same_group = !$bookie_keys['is_group'];
                    $should_be_linted = $bookie_keys['should_be_linted'];



                    if (($wrong_market || $wrong_selection)
                        && $should_be_linted
                        && $not_in_same_group
                        && $both_market_present
                    ) {

                        $market_id = "$home_bookie#$destination_bookie#{$home_selection['translated_market']}";

                        $dump = [
                            'sport' => $home_event['sport_id'],
                            'bookie_keys' => $bookie_keys,
                            'find_code' => "",
                            "home_event" => $home_event['item_name'],
                            "home" => $home_selection,
                            "destination_event" => $destination['item_name'],
                            "destination" => $destination_selection
                        ];


                        $linting = ConversionLinting::where('market_id', $market_id)->first();

                        if ($linting) {

                            $linting->update(
                                [
                                    "dump" => $dump,
                                    "conversion_dump" => $conversion_array,
                                    "gravity" =>  $linting->gravity + 1,
                                    "is_group" =>  $bookie_keys['is_group'],
                                ]
                            );
                        } else {

                            ConversionLinting::updateOrCreate(
                                [
                                    "market_id" => "$market_id",
                                ],
                                [
                                    "dump" => $dump,
                                    "conversion_dump" => $conversion_array,
                                    "gravity" =>  $conversion->gravity,
                                    "is_group" =>  $bookie_keys['is_group'],
                                ]
                            );
                        }
                    }
                }
            }

            $conversion->update([
                'is_linted' => 1
            ]);
        }
    }

    public function workjobs()
    {
        $per_page = 5;

        $jobs = Job::query()->toBeWorked()->take($per_page)->get();

        $jobs = Job::all();
        echo "<pre>";

        try {
            foreach ($jobs as $key => $job) {
                Worker::execute($job);
            }
        } catch (\Exception $th) {
            print_r($th->getMessage());
            //throw $th;
        }
    }


    public function cronjob()
    {
    }



    //events
    public function update_events()
    {
        echo "<pre>";

        $bookmaker_id = 4; //sportsTrader
        $last_event = Event::where('dump', '!=', null)->latest('event_date')->where('bookmaker_id', $bookmaker_id)->first();
        echo $event_date = $last_event == null ?  date("Y-m-d") : "$last_event->event_date +1 day";

        $next_date_string = date("Y-m-d", strtotime("$event_date"));

        $next_date = new DateTime($next_date_string);
        $this_day_date = new DateTime();


        $interval = $next_date->diff($this_day_date);
        $days = ($interval->format("%a"));

        $max_days  = 3;
        if ($days >= ($max_days)) {
            return;
        }


        $event_fetcher = new EventFetcher;
        $source = BookMaker::find($bookmaker_id);
        $fetcher = new BookMaker::$book_register[$source->NameKey]['fetcher'];

        $event_date = date("Y-m-d", strtotime("$next_date_string"));
        $db_category = SportsCategory::find(1); //soccer

        $event_fetcher->setFetcher($fetcher)
            ->setEventCategory($db_category->key_name)
            ->setDate($event_date)
            ->fetch('soccer');
    }


    public function update_results($date = null)
    {
        $hour = 20;
        // $hour = 15;
        $time = date("H");

        $event_fetcher = new EventFetcher;
        $source = BookMaker::find(4); //sportTrader
        $fetcher = new BookMaker::$book_register[$source->NameKey]['fetcher'];

        $event_date = $date == null ? date("Y-m-d") : $date;
        $db_category = SportsCategory::find(1); //soccer

        $event = Event::where('event_date', $event_date)->first();
        if ($event == null) {
            return;
        }


        $event_fetcher->setFetcher($fetcher)
            ->setEventCategory($db_category->key_name)
            ->setDate($event_date)
            ->fetch('result');
    }


    public function tips_factory()
    {
        $hour = 15;
        $time = date("H");
        if ($time >= $hour) {
            // return;
        }

        $event_date = date("Y-m-d");
        //ensure there is events
        $event = Event::where('dump', '!=', null)->where('event_date', $event_date)->first();
        if ($event == null) {
            return;
        }

        $no_of_tips = 40;
        $running_ad = Tip::Running($event_date)->get();
        $running_ad->count();
        if ($running_ad->count() >= $no_of_tips) {
            return;
        }



        $paper = null;
        $no_of_events = null;
        $source = BookMaker::find(4); //sportsTrader
        $bookmaker = BookMaker::find(4); //sportsTrader
        $db_category = SportsCategory::find(1); //soccer
        $days_of_operations_options = ([1, 2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 3, 4]);
        shuffle($days_of_operations_options);
        $days_of_operations = $days_of_operations_options[0];
        $no_of_creations = 5;
        $no_of_keys_options =  [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        shuffle($no_of_keys_options);
        $no_of_keys = $no_of_keys_options[0];

        $pricing = [];
        $factory = new TipsFactory;

        $ads = $factory->generateTip(
            $no_of_creations,
            $event_date,
            $paper,
            $no_of_events,
            $source,
            $bookmaker,
            $db_category->key_name,
            $days_of_operations,
            $pricing,
            $no_of_keys
        );
        $no = count($ads);
    }


    public function tips_performances()
    {

        $event_date = date("Y-m-d", strtotime("-1 day"));
        //ensure there is result
        $event = Event::where('result', '!=', null)->where('event_date', $event_date)->first();
        if ($event == null) {
            $this->update_results($event_date);
            return;
        }
        $per_page = $_REQUEST['per_page'] ?? 1;
        $running_ad = Tip::ToBeCheckedOn($event_date, $event_date)->take($per_page)->get();
        print_r($running_ad->count());

        $bookmaker = BookMaker::find(4);

        foreach ($running_ad as $key => $ad) {
            echo $ad->paper->name;
            echo "<br>";
            $ad->check_performance($event_date, $bookmaker);
        }
    }


    //results
    //tipfactory
    //tipperformance








    public function toggle()
    {
        $super_admin = Admin::find(1);

        if ($super_admin->super_admin == 1) {
            echo 'unset';
            echo $super_admin->update(['super_admin' => null]);
        } else {
            echo 'set';
            echo $super_admin->update(['super_admin' => 1]);
        }

        echo $super_admin;
    }




    public function fetch_news()
    {
        $auth = $this->auth();

        $today = date("Y-m-d");
        $pulled_broadcast_ids = Notifications::where('user_id', @$auth->id)->get()->pluck('broadcast_id')->toArray();
        $recent_news =  BroadCast::where('status', 1)->latest()
            //  ->whereNotIn('id', $pulled_broadcast_ids)
            //  ->whereDate("updated_at", '>=' , $today)
            ->get();


        foreach ($recent_news as $key => $news) {

            if (in_array($news->id, $pulled_broadcast_ids)) {
                continue;
            }

            $url = "user/notifications";
            $short_message = substr($news->broadcast_message, 0, 30);
            Notifications::create_notification(
                $auth->id,
                $url,
                "Notification",
                $news->broadcast_message,
                $short_message,
                null,
                $news->id,
                $news->created_at
            );
        }
    }



    public function auth_cron()
    {

        return;
        $auth = $this->auth();
        if (!$auth) {
            return;
        }

        $user_id = $auth->id;
        $this->fetch_news();
        $this->cron($user_id);
    }


    public function cron($user_id)
    {
        $this->rank_user($user_id);
        $this->membership_renewal($user_id);

        $this->settle_matured_investments($user_id);
    }


    public function simlulated_cron($date = null)
    {
        $users = User::all();

        foreach ($users as $key => $user) {

            $user_id = $user->id;
            $this->membership_renewal($user_id);
            $this->rank_user($user_id);
        }
    }

    //settle matured investments
    public function settle_matured_investments($user_id = null)
    {
        $per_page = 50;
        $investments =  Investment::RipeForSettlement()->take($per_page);


        if ($user_id != null) {
            $investments =  Investment::RipeForSettlement()->where('user_id', $user_id)->take($per_page);
        }


        foreach ($investments->get() as $key => $investment) {
            $investment->settle();
        }
    }




    public function membership_renewal($user_id = null)
    {
        User::find($user_id)->renew_subscription();
    }


    public function rank_user($user_id)
    {
        $ranking = new Rank;
        $ranking->setUser(User::find($user_id))->determineRank()->setUserRank();
    }


    public function index()
    {
        // print_r($this->settings->toArray());

    }
}
