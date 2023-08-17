<?php

namespace v2\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Job extends Eloquent
{

    protected $fillable = [
        'queue',
        'payload',
        'attempts',
        'reserved_at',
        'available_at',
        'created_at',
        'failed_at',
    ];


    protected $table = 'jobs';

    const UPDATED_AT = NULL;



    public function markAsExecuted()
    {
        return $this->delete();
    }

    public function markAsFailed()
    {
        $this->failed_at = date("Y-m-d H:i:s");
        return $this->save();
    }

    public  function scopeToBeWorked($query)
    {
        $now = date("Y-m-d H:i:s");
        return $query->whereRaw("(attempts < 3 and (available_at is null or available_at < '$now') )");

        return $query; //->where('failed_at', null);

    }


    public function getDetailsArrayAttribute()
    {
        if ($this->payload == null) {
            return [];
        }

        return json_decode($this->payload, true);
    }
}
