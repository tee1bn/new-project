<?php

namespace app\controllers\api;

use Input;
use Validator;
use v2\Models\Unit;
use v2\Classes\Bookies;
use v2\Utilities\Code\Arr;
use v2\Models\BetcodeConversion;
use v2\Classes\BetCodesConverter;

/**
 *
 */
class ConversionController
{

    public $api_auth;

    public function __construct()
    {
    }


    public function setApiAuth($api_auth)
    {
        $this->api_auth = $api_auth;
    }


    //booking endpoint 



    public function get_supported_bookies()
    {
        //charge
        $user = $this->api_auth['user'];
        if ($user->unit > 0) {
            $charge = Unit::chargeConvesion($user, null, "a", 1);
        } else {
            $subscriptions = $user->subscriptions;
            if ($subscriptions == null) {
                return false;
            }
            $sub_to_charge =  $subscriptions->first();
            $charge = $sub_to_charge->chargeConvesion($user, null, 'a', 1);
        }

        $bookies = (new Bookies)->getAvailabilityForAPI();


        $form = [
            "*" => [
                "bookie" => "betarena:ng",
                "from" => "1",
                "to" => "0",
                "name" => "betarena -Nigeria",
                "brand" => "betarena",
                "img" => "https://www.betarena.ng/content/betarena-b3t4ar/uploads/2021/07/logo.png"
            ]
        ];


        $bookies = Arr::deepKeySift($bookies, $form);

        return ["data" => compact('bookies'), "message" => "", "status" => 200];
    }



    public function get_conversion($id)
    {
        $conversion = BetcodeConversion::where('id', $id)
            ->Attempted()
            ->where('destination_code', '!=', null)
            ->with(['home_bookie', 'destination_bookie'])
            ->first();
        $conversion->slug;

        if ($conversion == null) {
            return false;
        }



        return ["data" => compact('conversion'), "message" => "", "status" => 200];
    }


    public function convert_v2()
    {
        $response = $this->convert();

        $conversion_object = BetcodeConversion::find($response['data']['conversion']['id']);
        if (!$conversion_object->isOk() || $conversion_object->dump == []) {
            return $response;
        }

        $response['data']['conversion']['dump'] =  $conversion_object->getMergedListOfConvertedEvents();

        $conversion = [
            "id" => 16,
            "bookies_train" => "ng/ng",
            "booking_code" => "LAP9P",
            "destination_code" => "7B16A8FB",
            "dump" => [
                "used_neq" => false,
                "home" => [
                    "bookie" => "22bet:ng",
                    "odds" => 459.24,
                    "no_of_entries" => 5
                ],
                "sports" => [
                    "soccer" => 5
                ],
                "destination" => [
                    "bookie" => "sportybet:ng",
                    "odds" => 144.23,
                    "no_of_entries" => 4
                ],
                "lists" => [
                    "*" => [
                        "sport" => [
                            "id" => "soccer",
                            "icon" => "fa fa-futbol"
                        ],
                        "is_neq" => false,
                        "is_converted" => true,
                        "exempt_reason" => null,
                        "home" => [
                            "item_name" => "Sabah Baku - Partizan Belgrade",
                            "home_team" => "Sabah Baku",
                            "away_team" => "Partizan Belgrade",
                            "item_date" => "2023-08-10 17:00:00",
                            "sport_id" => "soccer",
                            "market_name" => "1x2",
                            "outcome_name" => "X",
                            "odd_value" => 3.32,
                            "item_utc_date" => "2023-08-10 16:00:00"
                        ],
                        "destination" => [
                            "item_name" => "Sabah Masazir - FK Partizan Belgrade",
                            "home_team" => "Sabah Masazir",
                            "away_team" => "FK Partizan Belgrade",
                            "item_date" => "2023-08-10 17:00:00",
                            "tournament_name" => "UEFA Europa Conference League",
                            "category_name" => "International Clubs",
                            "sport_id" => "soccer",
                            "market_name" => "1X2",
                            "outcome_name" => "Draw",
                            "odd_value" => "3.30"
                        ]
                    ]
                ]
            ],
            "channel" => "a",
            "status" => 4,
            "starts_at" => "2023-08-10 17:00:00",
            "ends_at" => "2023-08-10 20:00:00",
            "created_at" => "2023-08-10 16:10:23",
            "updated_at" => "2023-08-10 16:24:47",
            "percent_progress" => 100,
            "errors" => null,
            "home_bookie" => [
                "name" => "22bet",
                "key_name" => "22bet"
            ],
            "destination_bookie" => [
                "name" => "sportybet",
                "key_name" => null
            ]
        ];

        $response['data']['conversion'] = Arr::deepKeySift($response['data']['conversion'], $conversion);

        return $response;
    }

    public function convert(...$var)
    {
        /*         echo '<pre>';
        print_r($_REQUEST);
        */
        $validator = new Validator;

        $validator->check($_REQUEST, [
            'booking_code' => [
                'required' => true,
                'min' => 3,
                'max' => 32,
                'no_special_character' => true,
                // 'name'=>"Booking Codwe" ,
            ],
            'from' => [
                'required' => true,
                'min' => 3,
                'max' => 32,
            ],
            'to' => [
                'required' => true,
                'min' => 3,
                'max' => 32,
            ],

        ]);




        //check allowable bookies on api channel
        $avail_bookies = (new Bookies)->getAvailabilityForAPI();


        $home_bookie = Input::get('from');
        $destination_bookie = Input::get('to');
        if ($avail_bookies[$home_bookie]['from'] != 1) {
            $validator->addError("from", "You cannot convert *from* this bookie because it is NOT supported on API presently.
             check supported_bookies endpoint for the updated list");
        }
        if ($avail_bookies[$destination_bookie]["to"] != 1) {
            $validator->addError("to", "You cannot convert *to* this bookie because it is NOT supported on the API presently. 
            check supported_bookies endpoint for updated list.");
        }
        if (!$validator->passed()) {
            return ["data" => (Input::errors()), "message" => "invalid request", "status" => 400];;
        }


        $from = explode(":", Input::get('from'));
        $from_bookmaker = $from[0];
        $from_bookmaker_country =  $from[1] ?? null;


        $to = explode(":", Input::get('to'));
        $to_bookmaker =  $to[0];
        $to_bookmaker_country =  $to[1] ?? null;


        try {
            $converter = new BetCodesConverter;
            $converter
                ->setUser($this->api_auth['user'] ?? null)
                ->ChargeForConversion(true)
                ->setChannel('a')
                ->setCode(Input::get('booking_code'))
                ->setHomeBookie($from_bookmaker, $from_bookmaker_country)
                ->setDestinationBookie($to_bookmaker, $to_bookmaker_country)
                ->convert()
                ->attemptCharge();

            $response = $converter->getResponse();


            if (
                !is_object($converter->response) ||
                !method_exists($converter->response, 'isOk') ||
                !$converter->response->isOk() ||
                $converter->response->dump == []
            ) {
                $errors = implode("\n", $response['errors']);
                throw new \Exception("{$errors}", 1);
            }



            //sieve here
            $conversion = [
                "errors" => null,
                "id" => null,
                "booking_code" => "LAP9P",
                "bookies_train" => "ng/ng",
                "updated_at" => "2023-08-10 16:10:42",
                "created_at" => "2023-08-10 16:10:23",
                "status" => 4,
                "channel" => "a",
                "starts_at" => "2023-08-10 17:00:00",
                "ends_at" => "2023-08-10 20:00:00",

                "dump" => [
                    "home_bookie_key" => "22bet",
                    "destination_bookie_key" => "sportybet",
                    "slug" => "",
                    "title" => "22bet code to sportybet",
                    "link" => "",
                    "home_bookie_entries" => [
                        "meta" => [],
                        "summary" => [
                            "no_of_entries" => 5,
                            "booking_code" => "LAP9P",
                            "odds_value" => 459.2351572992
                        ],
                        "found_events" => [
                            "*" => [
                                // "is_uniform" => true,
                                // "find_code" => null,
                                // "bet_code" => 7103,
                                // "item_id" => null,
                                // "odds_collection" => [],
                                "item_name" => "Sabah Baku - Partizan Belgrade",
                                "home_team" => "Sabah Baku",
                                "away_team" => "Partizan Belgrade",
                                "item_date" => "2023-08-10 17:00:00",
                                "sport_id" => "soccer",
                                "market_name" => "1x2",
                                "outcome_name" => "X",
                                "odd_value" => 3.32,
                                "item_utc_date" => "2023-08-10 16:00:00"
                            ]
                        ],
                    ],
                    "destination_bookie_entries" => [
                        "summary" => [
                            "booking_code" => "7B16A8FB",
                            "odds_value" => 144.230625,
                            "no_of_entries" => 4
                        ],
                        "no_of_translated_lines" => 5,
                        "converted_booking" => [
                            "meta" => [],
                            "uniform_event" => [
                                "*" => [
                                    // "is_uniform" => true,
                                    // "item_id" => null,
                                    // "find_code" => "sab#fk ",
                                    // "bet_code" => "11188",
                                    "item_name" => "Sabah Masazir - FK Partizan Belgrade",
                                    "home_team" => "Sabah Masazir",
                                    "away_team" => "FK Partizan Belgrade",
                                    "item_date" => "2023-08-10 17:00:00",
                                    "sport_id" => "soccer",
                                    "market_name" => "1X2",
                                    "outcome_name" => "Draw",
                                    "odd_value" => "3.30"
                                ],
                            ],
                            "summary" => [
                                "no_of_entries" => 4,
                                "booking_code" => "7B16A8FB"
                            ]
                        ]
                    ]
                ],
                "destination_code" => "7B16A8FB",
                "percent_progress" => 100,
                "home_bookie" => [
                    "name" => "22bet",
                    "key_name" => "22bet",
                ],
                "destination_bookie" => [
                    "name" => "sportybet",
                    "key_name" => null,
                ]
            ];

            // $response['conversion'] = Arr::deepKeySift($response['conversion'], $conversion);


            return ["data" => $response, "message" => "success", "status" => 200];
        } catch (\Exception $e) {

            return ["data" => $response, "message" => "something went wrong: {$e->getMessage()}", "status" => 500];
        }
    }
}
