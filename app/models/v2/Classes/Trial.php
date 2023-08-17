<?php

namespace v2\Classes;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


class Trial
{
    public $max_trials = 3;
    public $cookie_name = "cbc_trialxxx_";
    public $cookie;
    public $user;

    public function __construct()
    {
        $this->getCookie();
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
            'key' => date("Y-m"),
        ];
    }

    public function trialLeft()
    {
        $key =  $this->getKey();

        $left = $this->max_trials - strlen($this->cookie[$key]);

        return $left;
    }

    public function getKey()
    {
        $key =  $this->getPeriod()['key'];
        return $key;
    }

    public function canTry()
    {

        $this->getCookie();
        $key =  $this->getKey();
        $this->trialLeft();
        if (strlen($this->cookie[$key]) >= $this->max_trials) {
            return false;
        }

        return true;
    }



    public function countAttempt()
    {
        $key =  $this->getKey();
        $this->cookie[$key] .= "1";

        $this->storeCookie(json_encode($this->cookie));
    }

    public function storeCookie($value = null)
    {
        $code = [$this->getKey() => ""];

        $value = $value ?? json_encode($code);

        $expires = strtotime(date("Y-m-t"));
        setcookie($this->cookie_name, $value,  $expires, '/', null, false, true);
    }

    public function deleteCookie()
    {
        $expires = time() - 30000000;
        setcookie($this->cookie_name, null, $expires, '/', null, false, true);
    }


    public function getCookie()
    {
        if (
            !isset($_COOKIE[$this->cookie_name])
            ||  json_decode($_COOKIE[$this->cookie_name], true)[$this->getKey()] == null
        ) {
            $this->deleteCookie();
            $this->storeCookie();
        }
        $this->cookie =  json_decode($_COOKIE[$this->cookie_name], true);
    }
}
