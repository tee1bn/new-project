<?php

use v2\Classes\Bookies;
use v2\Models\BetcodeConversion;
use Illuminate\Database\Capsule\Manager as DB;


/**
 *
 */
class EmbedController extends controller
{


    public function __construct()
    {
        if (Session::get("embed_app_name") == null || Session::get("embed_app_name") == "App") {
            $app_name = $_GET['app_name'] ?? "App";
            Session::put("embed_app_name", $app_name);
        }
    }

    public function app_name()
    {
        return Session::get("embed_app_name");
    }



    public function c($conversion_id = null)
    {
        $conversion = BetcodeConversion::find($conversion_id);

        if ($conversion == null || (!$conversion->isShowable())) {
            Session::putFlash("danger", "Record not found.");
            Redirect::to("c/conversions");
        }

        $next =  BetcodeConversion::where('id', '>', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$next) {
            $next = $conversion;
        }


        $prev =  BetcodeConversion::where('id', '<', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('created_at', 'desc')
            ->first();


        if (!$prev) {
            $prev = $conversion;
        }


        $this->view('shared/conversion_result', get_defined_vars());
    }

    public function prediction($conversion_id = null)
    {
        $conversion = BetcodeConversion::find($conversion_id);

        if ($conversion == null || (!$conversion->isShowable())) {
            Session::putFlash("danger", "Record not found.");
            Redirect::to("c/conversions");
        }

        $next =  BetcodeConversion::where('id', '>', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$next) {
            $next = $conversion;
        }


        $prev =  BetcodeConversion::where('id', '<', $conversion_id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->orderBy('created_at', 'desc')
            ->first();


        if (!$prev) {
            $prev = $conversion;
        }


        $this->view('shared/prediction', get_defined_vars());
    }



    public function converted_codes()
    {


        $sieve = array_merge($_REQUEST, [
            'attempted_well' => 1,
            "today_with_delay" => 1,
            "not_hidden" => 1,
            'lastest' => "created_at",
        ]);



        // print_r((new Bookies)->getAvailabilityOfAll());
        extract(BetcodeConversion::InvokeQuery($sieve, 50, true));


        $this->view('shared/converted_codes_page', get_defined_vars(), true, true);
    }

    public function predictions_page()
    {

        $today = date("Y-m-d");
        $sql = "
     SELECT MIN(id) id, home_bookie_id, booking_code, SUM(gravity) AS g FROM `betcodes_conversions` 
         where 
                  (`created_at` BETWEEN '$today 00:00:00' and '$today 23:59:59')
                  
        and hide is null
        and ends_at > now()
         and home_entries is not null
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

        $this->view('shared/predictions_page', get_defined_vars(), true, true);
    }
}

// <iframe src="http://localhost/poolscompiler/embed/winning_calculator" style="width: 100%; height: 70em; border: none;"></iframe>
