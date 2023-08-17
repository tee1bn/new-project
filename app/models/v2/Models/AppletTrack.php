<?php

namespace v2\Models;

use MIS;
// use Illuminate\Database\Capsule\Manager as DB;
use User;
use Filters\Traits\Filterable;
use Config;
use Illuminate\Database\Eloquent\Model as Eloquent;

class AppletTrack extends Eloquent
{

    use  Filterable;

    protected $fillable = [
        "id",
        "applet_id",
        "host",
        "auth",
        "conversions",
    ];


    protected $table = 'applet_track';




    public static function track($applet, $host, $type)
    {

        $line = self::updateOrCreate(["host" => $host]);
        $auth = $type == "auth" ? $line->auth + 1 : $line->auth;
        $conversion = $type == "conversion" ? $line->conversion + 1 : $line->conversion;

        $line->update([
            "applet_id" => $applet->id,
            "auth" => $auth,
            "conversion" => $conversion,
        ]);
    }
}
