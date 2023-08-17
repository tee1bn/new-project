<?php


use v2\Models\Applet;
use v2\Classes\Bookies;
use v2\Models\BookMaker;
use v2\Classes\Prediction;
use v2\Classes\EventFetcher;
use v2\Models\ConversionLog;
use v2\Models\LinePrediction;
use v2\Classes\QuickBetEditor;
use v2\Models\BetcodeConversion;
use v2\Classes\BetCodesConverter;
use v2\Classes\Bet9ja\Soccer\QuickUniMarket;
use Illuminate\Database\Capsule\Manager as DB;
use v2\Utilities\Prediction as UtilitiesPrediction;

/**
 * this class is the default controller of our application,
 *
 */
class ConversionsController extends controller
{


    public function __construct()
    {
    }



    public function prev($id = null)
    {
        $conversion =  BetcodeConversion::where('id', '<', $id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('updated_at', 'desc')
            ->first();


        if (!$conversion) {
            Session::putFlash("danger", "Record not found.");
            Redirect::back();
        }

        Redirect::to("c/$conversion->id");
    }


    public function next($id = null)
    {
        $conversion =  BetcodeConversion::where('id', '>', $id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('updated_at', 'asc')
            ->first();

        if (!$conversion) {
            Session::putFlash("danger", "Record not found.");
            Redirect::back();
        }

        Redirect::to("c/$conversion->id");
    }

    public function edit($conversion_id)
    {
        $domain = Config::domain();
        $conversion = BetcodeConversion::find($conversion_id);

        if ($conversion == null || (!$conversion->hasHomeEntries())) {
            // Session::putFlash("danger", "Record not found.");
            // Redirect::back();
        }

        if (!$conversion->isEditable()) {
            # code...
        }

        $this->view('guest/edit_booking_code', get_defined_vars());
    }


    public function view_edit($conversion_id)
    {


        $domain = Config::domain();

        $conversion = BetcodeConversion::find($conversion_id);

        if ($conversion == null || (!$conversion->isShowable())) {
            Session::putFlash("danger", "Record not found.");
            Redirect::to("/");
        }


        $this->view('guest/edited_result', get_defined_vars());
    }

    public function book_edit()
    {

        echo "<pre>";



        $lines = Input::get('lines');
        $conversion_id = Input::get('conversion_id');
        $destination_bookie_key = Input::get('destination_bookie_key');
        $destination_bookie = explode(":", $destination_bookie_key)[0];
        $uniform_events = [];


        try {

            foreach ($lines as $key => $line) {
                if ($line['select'] != '1') {
                    continue;
                }

                $line_array = (json_decode($line['event'], true));
                $line_array['is_uniform'] = 1;


                //add find_code
                $find_code = EventFetcher::getFindCode($line_array['home_team'], $line_array['away_team']);
                $line_array['find_code'] = $find_code;

                //add sport category
                $sport_category = EventFetcher::getSportCategory($line_array['sport_id'], $destination_bookie);
                $line_array['sport_category'] = $sport_category;

                //add translated prediction
                $markets = EventFetcher::getQuickUniMarkets($line_array['sport_id'], $destination_bookie);
                $translated_prediction = (collect($markets)->flatten(2)->keyBy('view')->toArray()[$line['selection']]);
                $line_array['translated_prediction'] = $translated_prediction;


                array_push($uniform_events, $line_array);
            }

            $bet_editor = new QuickBetEditor;
            $response = $bet_editor->setUniformLines($uniform_events)
                ->setConversionId($conversion_id)
                ->setDestinationBookie($destination_bookie_key)
                ->setModel()
                ->edit();



            if ($response->isOk()) {
                $view = $this->buildView('guest/edit_response_view', compact('response'), true, true);
            } else {
                $view = "We could not book your edit. Pls try again.";
            }
            ob_clean();

            $data = compact('view');

            header("content-type:application/json");
            echo json_encode($data);
        } catch (\Exception $e) {
            Session::putFlash("danger", "Please try again");
            // Redirect::back();
        }
    }




    public function index($conversion_id)
    {


        $domain = Config::domain();

        $conversion = BetcodeConversion::find($conversion_id);

        if ($conversion == null || (!$conversion->hasHomeEntries())) {
            Session::putFlash("danger", "Record not found.");
            Redirect::to("c/conversions");
        }

        $next =  BetcodeConversion::where('id', '>', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('updated_at', 'asc')
            ->first();


        $prev =  BetcodeConversion::where('id', '<', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('updated_at', 'desc')
            ->first();


        $this->view('guest/conversion_result', get_defined_vars());
    }

    public function converted_codes()
    {
        $domain = Config::domain();
        // die("This page is unavailable for now. <a href='$domain'>Go Home </a>");

        $auth = $this->auth();
        $today_key = $auth && $auth->canSeeConvertedCodesRealTime()  ? "today" : "today_with_delay";

        $_REQUEST['not_hidden'] = 1;

        switch ($today_key) {
            case 'today':

                $sieve = array_merge($_REQUEST, [
                    'attempted_well' => 1,
                    "today" => 1,
                    'lastest' => "updated_at",
                ]);
                break;

            default:

                $sieve = array_merge($_REQUEST, [
                    'attempted_well' => 1,
                    "today_with_delay" => 1,
                    'lastest' => "updated_at",
                ]);
                break;
        }


        extract(BetcodeConversion::InvokeQuery($sieve, 25, true));
        $bookies = (new Bookies)->getAvailabilityOfAll();


        $this->view('guest/conversions', get_defined_vars());
    }

    public function unsuccessful()
    {

        $sieve = array_merge($_REQUEST, [
            'unsuccessful' => 1,
            'today' => 1,
            'lastest' => "updated_at",
        ]);

        extract(BetcodeConversion::InvokeQuery($sieve, null, true));
        $bookies = (new Bookies)->getAvailabilityOfAll();

        $this->view('guest/unsuccessful-conversions', get_defined_vars());
    }


    public function link($id = null)
    {

        $link = Applet::findByHashId($id);

        //ensure this is LIVE
        if ($link == null) {
            Session::putFlash("danger", "Link not found.");
            Redirect::to("pg/conversion_link");
        }


        $this->view('guest/link', get_defined_vars(), true, true);
    }


    public function hot_predictions()
    {


        $today = date("Y-m-d");
        $sql = "
     SELECT MIN(id) id, home_bookie_id, booking_code, SUM(gravity) AS g FROM `betcodes_conversions` 
         where 
                  (`updated_at` BETWEEN '$today 00:00:00' and '$today 23:59:59')

        and home_entries is not null
        and hide is null
        and ends_at > now()
         and gravity > 3

        GROUP BY booking_code, home_bookie_id 
        ORDER BY g DESC LIMIT 0, 20 ;

        ";


        $ids_obj = collect(DB::select(DB::raw($sql)));

        $ids = collect(DB::select(DB::raw($sql)))->pluck('id')->toArray();
        $hots = BetcodeConversion::whereIn("id", $ids)->get()->keyBy('id');

        $hot_predictions = $ids_obj->map(function ($item) use ($hots) {
            $new =  $hots[$item->id] ?? null;
            if (!$new) {
                $new->gravity = $item->g;
            }
            return $new;
        });

        $hot_predictions = $hot_predictions->sortByDesc('gravity');
        $this->view('guest/hot_predictions', get_defined_vars());
    }



    public function hot_prediction($conversion_id)
    {

        $domain = Config::domain();
        $conversion = BetcodeConversion::find($conversion_id);

        if ($conversion == null || (!$conversion->isShowable())) {
            Session::putFlash("danger", "Record not found.");
            Redirect::to("c/hot-predictions");
        }

        $next =  BetcodeConversion::where('id', '>', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('updated_at', 'asc')
            ->first();
        $prev =  BetcodeConversion::where('id', '<', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('updated_at', 'desc')
            ->first();

        $this->view('guest/hot_prediction', get_defined_vars());
    }


    public function conversions()
    {
        Redirect::to("c/converted-codes");
    }

    public function lines_predictions()
    {

        $sieve = array_merge($_REQUEST, [
            'current_event' => 1,
            'lastest' => "time",
        ]);

        extract(LinePrediction::InvokeQuery($sieve, null, true));

        $markets =  EventFetcher::getQuickUniMarkets('soccer', 'bet9ja');

        $markets = array_map(function ($item) {
            return [
                "key" => $item['translated_market'],
                "value" => $item['market']
            ];
        }, $markets);

        $this->view('guest/line_predictions', get_defined_vars());
    }
}
