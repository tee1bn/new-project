<?php


namespace v2\Filters\Filters;

use User;
use Filters\QueryFilter;
use Filters\Traits\RangeFilterable;
use MIS;

/**
 * 
 */
class LinePredictionFilter extends QueryFilter
{
    use RangeFilterable;




    public function lastest($column)
    {
        if ($column == null) {
            return;
        }
        $this->builder->latest($column);
    }
    public function current_event($column)
    {
        if ($column == null) {
            return;
        }

        $this->builder->whereRaw("time is not null  and time >= now()");
    }

    public function sort_record_by(...$order_bys)
    {
        if ($order_bys == null) {
            return;
        }
        foreach ($order_bys as $column => $direction) {
            $this->builder->orderBy($column, $direction);
        }
    }




    public function take($take = null)
    {

        if ($take == null) {
            return;
        }
        $this->builder->take($take);
    }

    public function today($id = null)
    {

        if ($id == null) {
            return;
        }
        $today = date("Y-m-d");
        $this->builder->whereBetween('time', ["$today 00:00:00", "$today 23:59:59"]);
    }


    public function ref($ref = null)
    {

        if ($ref == null) {
            return;
        }
        $this->builder->where('id', 'like', "%$ref%");
    }

    public function sports(...$sports)
    {
        if ($sports == null) {
            return;
        }
        $this->builder->whereIn('sport', $sports);
    }

    public function tournaments(...$tournaments)
    {
        if ($tournaments == null) {
            return;
        }
        $this->builder->whereIn('tournament', $tournaments);
    }

    public function category(...$category)
    {
        if ($category == null) {
            return;
        }

        $this->builder->whereIn('category_name', $category);
    }

    public function tips(...$tips)
    {
        if ($tips == null) {
            return;
        }
        $this->builder->whereIn('tips_prediction', $tips);
    }

    public function odds_range($start = null, $end = 1000)
    {

        if (($start == null)) {
            return;
        }
        $this->builder->WhereRaw("(odd >= $start and odd <= $end) ");
    }
    public function team($team = null)
    {

        if (($team == null)) {
            return;
        }

        $this->builder->whereRaw(" (home_team like ? OR away_team like ? )", array('%' . $team . '%', '%' . $team . '%'));
    }

    public function playing($date = null)
    {

        if ($date == null) {
            return;
        }

        $this->builder->whereBetween('time', ["$date 00:00:00", "$date 23:59:59"]);
    }

    public function markets(...$markets)
    {
        if ($markets == null) {
            return;
        }


        $this->builder->whereIn('translated_market', $markets);
    }


    public function popularity($popularity = null)
    {
        if (($popularity == null)) {
            return;
        }

        $this->builder->orderBy("gravity", "desc");
    }
}
