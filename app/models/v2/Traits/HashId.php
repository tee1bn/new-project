<?php

namespace v2\Traits;

use Hashids\Hashids;

/**
 * 
 */
trait HashId
{


    public function getHashIdAttribute()
    {

        $project_name = $_ENV['APP_NAME'];
        $hashids = new Hashids($project_name);

        $hash_id = $hashids->encode($this->id); // o2fXhV

        return $hash_id;
    }


    public static function FindByHashId($hash_id)
    {

        $project_name = $_ENV['APP_NAME'];
        $hashids = new Hashids($project_name);

        $id = $hashids->decode($hash_id)[0] ?? null; // [1, 2, 3]

        return self::find($id);
    }
}
