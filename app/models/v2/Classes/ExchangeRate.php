<?php

namespace v2\Classes;



class ExchangeRate
{
    /**
     * currency to covert from
     *
     * @var string
     */
    public $from;


    /**
     * currency to convert to
     *
     * @var string
     */
    public $to;

    public $amount = 1;

    /**
     * USD/NGN --381
     *
     * @var array
     */
    public $usd_rates = [
        'UNT' => 25,
        'USD' => 1,
        'NGN' => 500,
        'EUR' => 0.98,
        'GBP' => 0.83,
        'GHS' => 12,  //14.47
        'KES' => 122,
        'ZAR' => 17.09,
        'TZS' => 2326,
        'UGX' => 3798,
        'XOF' => 595,
        'XAF' => 595,
        'CAD' => 1.26,
        'AUD' => 1.34,
        'RWF' => 1026.48,
    ];


    /**
     *  returns the rate of exchange of a currency pair
     *
     * @param string $symbol
     * @return int
     */
    public function getRate($symbol = "USDNGN")
    {
        $array = str_split($symbol, 3);
        $from = $array[0];
        $to = $array[1];

        $rate = 1 / $this->usd_rates[$from] * $this->usd_rates[$to];

        return $rate;
    }

    public function getConversion()
    {
        //peg from base currency to dollar first
        $symbol = "{$this->from}{$this->to}";
        $exchange_rate = $this->getRate($symbol);
        $destination_value = $exchange_rate * $this->amount;

        $r_exchange_symbol = "{$this->to}{$this->from}";
        $r_exchange_rate = $this->getRate($r_exchange_symbol);


        $response = [
            'from' => $this->from,
            'to' => $this->to,
            "$symbol" => $exchange_rate,
            "{$this->from}" => $this->amount,
            "{$this->to}" => $destination_value,
            "destination_value" => $destination_value,
            "$r_exchange_symbol" => $r_exchange_rate,
        ];

        return $response;
    }


    /**
     * Set the value of to
     *
     * @return  self
     */
    public function setTo(string $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Set the value of from
     *
     * @return  self
     */
    public function setFrom(string $from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
