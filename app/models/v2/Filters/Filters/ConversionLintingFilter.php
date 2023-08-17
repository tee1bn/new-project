<?php


namespace v2\Filters\Filters;

use Admin;
use Filters\QueryFilter;
use Filters\Traits\RangeFilterable;



/**
 * 
 */
class ConversionLintingFilter extends QueryFilter
{
    use RangeFilterable;


    public function status($status = null)
    {
        if ($status == null) {
            return;
        }

        $this->builder->where('status', $status);
    }


    public function gravity($gravity = null)
    {
        if ($gravity == null) {
            return;
        }

        $this->builder->where('gravity', '>=', $gravity)->orderBy('gravity', 'desc');
    }



    public function market_id($item_id = null)
    {

        if ($item_id == null) {
            return;
        }

        $this->builder->where('market_id', 'like', "%$item_id%");
    }


    public function bookies($item_id = null)
    {

        if ($item_id == null) {
            return;
        }

        $this->builder->where('market_id', 'like', "%$item_id%");
    }

    public function user($user = null)
    {
        if ($user == null) {
            return;
        }

        $user_ids =  Admin::WhereRaw(
            "firstname like ? 
	                                      OR lastname like ? 
	                                      OR email like ? 
	                                      OR phone like ? 
	                                      OR username like ? 
	                                      ",
            array(
                '%' . $user . '%',
                '%' . $user . '%',
                '%' . $user . '%',
                '%' . $user . '%',
                '%' . $user . '%'
            )
        )->get()->pluck('id')->toArray();

        $this->builder->whereIn('admin_id', $user_ids);
    }


    public function ref($ref = null)
    {

        if ($ref == null) {
            return;
        }

        $refs = explode(",", $ref);

        // $this->builder->where('payment_details', 'like', "%$ref%");
        $this->builder->whereIn('id', $refs);
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
}
