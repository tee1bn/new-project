<?php

namespace v2\Jobs\Traits;

use Location;
use v2\Models\Job as ModelJob;
use Illuminate\Database\Eloquent\Model;




/**
 * 
 */
trait Job
{
    public function setPropery($property)
    {
    }

    public static function dispatch(array $data, $available_at = null)
    {
        // $ip_location = Location::location();
        $email_job = (new static);
        foreach ($data as $key => $value) {
            $email_job->$key = $value;
        }
        $email_job->schedule($available_at);
    }


    public  function schedule($available_at = null)
    {
        $models = [];
        foreach (get_object_vars($this) as $property => $value) {
            $is_model = $this->$property instanceof Model;
            $type = $is_model ? 'model' : 'not_model';
            $models[] = [
                'name' => $property,
                'type' => $type,
                'value' => $is_model ? get_class($this->$property) : $value,
                'id' => $is_model ? $this->$property->getKey() : null,
            ];
        }

        $payload = [
            'properties' => $models,
            'class' => static::class,
        ];

        $job =  ModelJob::create([
            'payload' => json_encode($payload),
            'attempts' => 0,
            'available_at' => $available_at ?? null,
        ]);
    }

    public function execute($job)
    {
    }
}
