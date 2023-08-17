<?php


namespace v2\Filters\Filters;

use User;
use Filters\QueryFilter;
use Filters\Traits\RangeFilterable;



/**
 * 
 */
class ConversionLogFilter extends QueryFilter
{
    use RangeFilterable;

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




    public function bill_id($bill_id = null)
    {

        if ($bill_id == null) {
            return;
        }

        $this->builder->where('bill_id', '=', "$bill_id");
    }


    public function bill_type($type = null)
    {

        if ($type == null) {
            return;
        }

        $this->builder->where('bill_type', '=', "$type");
    }


    public function usage_date($start_date = null, $end_date = null)
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
}
