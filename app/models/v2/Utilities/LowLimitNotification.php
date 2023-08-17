<?php

namespace v2\Utilities;

use SiteSettings;
use v2\Jobs\Job;
use v2\Jobs\Jobs\SendLowLimitNoticeEmail;


class LowLimitNotification
{


    public $subscription = array();
    public $settings;
    public $days_limit;
    public $notice_interval;
    public $last_sent_date;
    public $low_unit_minimum_percent;


    public function __construct()
    {
        $this->settings = SiteSettings::low_limit_settings();
        $this->low_unit_minimum_percent = $this->settings['low_unit_minimum_percent'];
        $this->days_limit = $this->settings['low_limit_minimum_days'];
        $this->notice_interval = $this->settings['minimum_notice_interval'];
    }

    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
        return $this;
    }

    public function canSendNotice()
    {

        $last_sent_date = $this->subscription->user->getLowLimitSettings('notice_sent_at');

        if ($last_sent_date == "") return true;

        if (time() > strtotime("$last_sent_date + {$this->notice_interval}")) {
            return true;
        }
        return false;
    }

    public function sendNoticeByPlan()
    {
        $datetime1 = date_create(date('Y-m-d'));
        $datetime2 = date_create($this->subscription->expires_at);
        $diff = date_diff($datetime1, $datetime2);
        $days = $diff->format("%a");
        if ($days > $this->days_limit) return;
        $subscription = $this->subscription;
        SendLowLimitNoticeEmail::dispatch(compact('subscription'));
        $this->updateSentTime();
    }

    public function sendNoticeByUnit()
    {

        if ($this->subscription->units <= 0) return;

        $unused_percent = ($this->subscription->units / (int)$this->subscription->details['no_of_units']) * 100;


        if ($unused_percent > $this->low_unit_minimum_percent) return;


        $subscription = $this->subscription;
        SendLowLimitNoticeEmail::dispatch(compact('subscription'));
        $this->updateSentTime();
    }

    public function updateSentTime()
    {
        $this->subscription->user->updateSettings([
            "low_limit" => array(
                'notice_sent_at' => date('Y-m-d H:i:s')
            )
        ]);
    }

    public function sendNoticeIfBalanceIsLow()
    {

        if (!$this->canSendNotice()) return;


        if ($this->subscription->type == 'unit') {
            $this->sendNoticeByUnit();
        }



        if ($this->subscription->type == 'plan') {
            $this->sendNoticeByPlan();
        }
    }
}
