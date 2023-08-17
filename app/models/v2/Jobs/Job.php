<?php

namespace v2\Jobs;

use v2\Jobs\Contracts\Job as JobInterface;

/**
 * This class is reponsible for scheduling a job and working a job
 */
class Job
{


    public  static function schedule(JobInterface $job)
    {
        $job->schedule();
    }

    public  static function execute($db_job)
    {
        $payload = $db_job->DetailsArray;
        $class = $payload['class'];
        $job = (new $class);

        
        foreach ($payload['properties'] as $value) {
            
            if ($value['type'] == 'model') {
                $name = $value['name'];
                $model = $value['value']::find($value['id']);
                $job->$name = $model;
            } else {
                $name = $value['name'];                 
                $job->$name = $value['value'];
            }
        }
        
        //add this dbjob into the built job instance
        $job->db_job = $db_job;
      

        return   $job->execute() ? $db_job->markAsExecuted() :  $db_job->markAsFailed();
    }
}
