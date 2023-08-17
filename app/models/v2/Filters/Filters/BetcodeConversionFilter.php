<?php


namespace v2\Filters\Filters;

use User;
use Filters\QueryFilter;
use Filters\Traits\RangeFilterable;


/**
 * 
 */
class BetcodeConversionFilter extends QueryFilter
{
    use RangeFilterable;




    public function lastest($column)
    {
        if ($column == null) {
            return;
        }

        $this->builder->latest($column);
    }

    public function sort_record_by(...$order_bys)
    {
        if ($order_bys == null) {
            return;
        }

        foreach ($order_bys as $column => $direction) {
            $this->builder->orderBy("gravity", "desc");
        }
    }



    public function take($take = null)
    {

        if ($take == null) {
            return;
        }
        $this->builder->take($take);
    }


    public function by_plan($id = null)
    {
        if ($id == null) {
            return;
        }

        $today = date("Y-m-d");
        $time =  date("H:i:s", strtotime("-1 hours"));

        $this->builder->whereRaw("(updated_at between '$today 00:00:00' and '$today $time')");
    }


    public function today_with_delay($id = null)
    {

        if ($id == null) {
            return;
        }
        $today = date("Y-m-d");
        $time =  date("H:i:s", strtotime("-2 hours"));

        $this->builder->whereBetween('updated_at', ["$today 00:00:00", "$today $time"]);
    }

    public function today($id = null)
    {

        if ($id == null) {
            return;
        }
        $today = date("Y-m-d");
        $this->builder->whereBetween('updated_at', ["$today 00:00:00", "$today 23:59:59"]);
    }



    public function attempted_well($id = null)
    {

        if ($id == null) {
            return;
        }

        $this->builder->Attempted()->where('destination_code', "!=", null);
    }

    public function not_hidden($id = null)
    {

        if ($id == null) {
            return;
        }

        $this->builder->where('hide', null);
    }

    public function unsuccessful($id = null)
    {

        if ($id == null) {
            return;
        }

        $this->builder->Unsuccessful();
    }


    public function has_home_entries($id = null)
    {

        if ($id == null) {
            return;
        }

        $this->builder->where('home_entries', "!=", null)->where('gravity', ">", 1);
    }


    public function dest_bookie($id = null)
    {

        if ($id == null) {
            return;
        }
        $this->builder->where('dest_bookie_id', "$id");
    }


    public function home_bookie($id = null)
    {

        if ($id == null) {
            return;
        }
        $this->builder->where('home_bookie_id', "$id");
    }



    public function ref($ref = null)
    {

        if ($ref == null) {
            return;
        }
        $this->builder->where('id', 'like', "%$ref%");
    }



    public function bookies_train($bookies_train = null)
    {
        if ($bookies_train == null) {
            return;
        }
        $this->builder->where('bookies_train', "=",  "$bookies_train");
    }



    public function user($name = null)
    {
        if ($name == null) {
            return;
        }

        $user_ids = User::WhereRaw(
            "firstname like ? 
            OR lastname like ? 
            OR username like ? 
            OR email like ? 
            OR phone like ? 
            ",
            array(
                '%' . $name . '%',
                '%' . $name . '%',
                '%' . $name . '%',
                '%' . $name . '%',
                '%' . $name . '%'
            )
        )->get()->pluck('id')->toArray();

        $this->builder->whereIn('user_id', $user_ids);
    }




    public function status($status = null)
    {
        if ($status == null) {
            return;
        }

        $this->builder->where('status', '=', $status);
    }


    public function created_at($start_date = null, $end_date = null)
    {

        if (($start_date == null) &&  ($end_date == null)) {
            return;
        }

        $date = compact('start_date', 'end_date');

        if ($end_date == null) {
            $date = $start_date;
        }

        $this->date($date, 'created_at');
    }

    public function bookies($bookies = null)
    {
        if ($bookies == null) {
            return;
        }

        $bookies = explode(",", $bookies);
        $this->builder->whereIn('home_bookie_key', $bookies)->orWhereIn('dest_bookie_key', $bookies);
    }

    public function events_range($start = null, $end = 1000)
    {

        if (($start == null)) {
            return;
        }
        $this->builder->whereRaw("((home_total_events >= $start and home_total_events <= $end)  or  (dest_total_events >= $start and dest_total_events <= $end))");
    }

    public function odds_range($start = null, $end = 1000)
    {

        if (($start == null)) {
            return;
        }
        $this->builder->whereRaw("((home_total_odds >= $start and home_total_odds <= $end)  or  (dest_total_odds >= $start and dest_total_odds <= $end))");
    }

    public function popularity($popularity = null)
    {
        if (($popularity == null)) {
            return;
        }

        $this->builder->orderBy("gravity", "desc");
    }
}
