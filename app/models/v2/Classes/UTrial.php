<?php

namespace v2\Classes;

use Session;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


class UTrial
{
    public $max_trials = 0;
    public $user;
    public $start_from = "2023-01-07";

    public function __construct()
    {
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $user;
    }


    public function getPeriod()
    {
        return [
            'start' => null,
            'end' => null,
            'key' => date("Y-m-01"),
            'key' => "trial",
        ];
    }

    public function trialLeft()
    {
        if (!$this->user) {
            return 0;
        }

        if (strtotime($this->user->created_at) <= strtotime($this->start_from)) {
            return 0;
        }



        $trials = $this->user->trials ?? [];
        $trials_left = $this->max_trials - strlen($trials[$this->getKey()]);

        return max(0, $trials_left);
    }

    public function getKey()
    {
        $key =  $this->getPeriod()['key'];
        return $key;
    }

    public function canTry()
    {
        if (!$this->user) {
            return false;
        }


        if (strtotime($this->user->created_at) <= strtotime($this->start_from)) {
            return false;
        }

        $domain = \Config::domain();

        //user must have verified email
        if (!$this->user->has_verified_email()) {
            Session::putFlash("danger", "Please <a href='$domain/verify/email'>verify your email</a> to access free trials.");
            return false;
        }



        if ($this->trialLeft() <= 0) {
            return false;
        }

        return true;
    }



    public function countAttempt()
    {


        $trials = $this->user->trials ?? [];


        $this_month = $this->getKey();
        $trials = array_filter($trials, function ($month) use ($this_month) {
            if (strtotime($month) < strtotime($this_month)) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_KEY);



        $trials[$this_month] = $trials[$this_month] == '' ? '1' : "{$trials[$this_month]}1";


        $this->storeCookie($trials);
    }

    public function storeCookie($value = null)
    {
        if (!$this->user) {
            return false;
        }

        if ($this->trialLeft() == 0) {
            return;
        }

        $this->user->updateTrials($value);
        return;
    }
}
