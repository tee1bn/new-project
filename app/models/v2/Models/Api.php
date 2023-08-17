<?php

namespace v2\Models;

use MIS;
use User;
use  Filters\Traits\Filterable;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Api extends Eloquent
{

    use  Filterable;

    protected $fillable = [
        'api_key',
        'name',
        'user_id',
        'details',
        'status',
    ];


    protected $table = 'apis';



    public function getDisplayNameAttribute()
    {
        return $this->name == '' ? 'default' : $this->name;
    }


    public function switch()
    {
        $state = [0 => "off", 1 => "on"][(int)!$this->status];
        \Session::putFlash("success", "turned $state successfully.");

        return $this->update(['status' => (int)!$this->status]);
    }

    public function is_enabled()
    {
        return $this->status == 1;
    }


    public function generateSmartKey()
    {

        $key = MIS::dec_enc("encrypt", json_encode([
            'id' => $this->id,
            'user_id' => $this->user_id,
            't' => time(),
        ]));

        return $key;
    }



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function getDetailsAttribute($value)
    {
        if ($this->value == null) {
            return [];
        }

        return json_decode($this->value, true);
    }


    public function scopeEnabled($query)
    {
        return $query->where('status', 1);
    }

    public function getshownApiKeyAttribute()
    {
        return $this->api_key === null ? $this->generateSmartKey() : $this->api_key;
    }


    public function getActiveStatusAttribute()
    {
        if ($this->status == 1) {
            $label = '<span class="badge badge-success">Enabled</span>';
        } else {
            $label = '<span class="badge badge-danger">Disabled</span>';
        }

        return $label;
    }
}
