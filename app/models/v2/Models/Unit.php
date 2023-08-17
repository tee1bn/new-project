<?php

namespace v2\Models;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;
use  Exception;


use  v2\Traits\Wallet as BookRecords;
use  Filters\Traits\Filterable;


/**
 * units
 */
class Unit extends Eloquent
{

    use  Filterable;
    use BookRecords {
        availableBalanceOnUser as TraitavailableBalanceOnUser;
        bookBalanceOnUser as TraitbookBalanceOnUser;
    }
    protected $fillable = [
        'user_id',
        'order_id',
        'admin_id',
        'upon_user_id',
        'amount',
        'paid_at',
        'expires_at',
        'type',
        'earning_category',
        'status',
        'identifier',
        'comment',
        'extra_detail'
    ];


    protected $table = 'units';


    public static $statuses = [
        'pending' => 'pending',
        'completed' => 'completed',
        'cancelled' => 'cancelled'
    ];


    public static $types = [
        'credit' => 'credit',
        'debit' => 'debit',
    ];




    public static function availableBalanceOnUser($user_id, $category = null, $as_at = null, $daterange = null, $balance_field = null)
    {
        $available_balance = self::TraitavailableBalanceOnUser($user_id, $category, $as_at, $daterange, $balance_field);
        return $available_balance;
    }


    public static function bookBalanceOnUser($user_id, $category = null, $as_at = null, $daterange = null)
    {
        $available_balance = self::TraitbookBalanceOnUser($user_id, $category, $as_at, $daterange);

        return $available_balance;
    }



    public function is_complete()
    {
        return $this->status == 'completed';
    }


    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }




    public function getExtraDetailAttribute($value)
    {
        if ($this->value == null) {
            return [];
        }

        return json_decode($this->value, true);
    }



    public function paymentMethod()
    {
        $payment_details = json_decode($this->payment_details, true);
        $gateway = str_replace("_", " ", $payment_details['gateway']);

        return $gateway;
    }


    public function getTransactionIDAttribute()
    {

        $payment_details = json_decode($this->payment_details, true);
        $id = $payment_details['ref'] ?? '';
        $gateway = str_replace("_", " ", $payment_details['gateway']);
        $method = "{$id}<br><span class='badge badge-primary'>{$gateway}</span>";

        return $method;
    }


    public function is_paid()
    {
        return (bool) ($this->paid_at != null);
    }


    public function generateOrderID()
    {

        $substr = substr(strval(time()), 7);
        $order_id = "CBC{$this->id}U{$substr}";

        return $order_id;
    }


    public function getDepositPaymentStatusAttribute()
    {
        if ($this->paid_at != null) {

            $label = '<span class="badge badge-success">Paid</span>';
        } else {
            $label = '<span class="badge badge-danger">Unpaid</span>';
        }

        return $label;
    }


    public static function chargeConvesion($user, $conversion, $channel, $cost, $comment = "conversion events")
    {

        $extra_detail = [
            'channel' => $channel
        ];

        $balance_field = [
            'model' => $user,
            'field' => "unit",
        ];



        DB::beginTransaction();

        try {

            $debit  = self::createTransaction(
                'debit',
                $user->id ?? null,
                null,
                $cost,
                'completed',
                'conversion',
                $comment,
                null,
                $conversion->id ?? null,
                null,
                json_encode($extra_detail),
                null,
                $balance_field
            );

            $bill_id = $debit == false ? null : $debit->id;
            $log = ConversionLog::logConversion($user, $conversion, $bill_id, "token", $channel);



            DB::commit();
            return $debit;
        } catch (\Exception $e) {

            DB::rollback();
        }
        return false;
    }


    public static function giveReferralBonusOn($user)
    {
        $identifier = "{$user->introduced_by}#ref#{$user->id}";
        $amount = 5;
        $comment = "referral bonus";

        if ($user->introduced_by == 1) {
            return true;
        }


        try {

            $credit  = self::createTransaction(
                'credit',
                $user->introduced_by,
                $user->id,
                $amount,
                'completed',
                'conversion',
                $comment,
                $identifier
            );
            return $credit;
        } catch (Exception $e) {
            print_r($e->getMessage());
        }

        return false;
    }
}
