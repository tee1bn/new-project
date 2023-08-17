<?php


class MeetingController extends controller
{

    public function testing()
    {
        $user = [
            "name" => "james",
            "surname" => "moses",
        ];

        $subscription = [
            "name" => "premium",
            "price" => "200",
        ];


        $this->view("meeting/ourfile", compact('user'));
    }



    public function index()
    {

        echo "we are in a meeting";
    }
}
