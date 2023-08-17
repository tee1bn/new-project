<?php


/**
 *
 */
class DemoController extends controller
{


    public function __construct()
    {
    }


    public function applet()
    {
        $this->view('guest/applet');
    }


    public function index($bookie_key)
    {


        $bet9ja = <<<EL
        <script src="https://convertbetcodes.com/applet/applet.js?id=VDJ3YUZEdkhMK3g2ZTNYbjF4emlyRWJKc08vZVhVOEExcTAwWXNEMG51az0="></script>
EL;


        $betking = <<<EL
        <script src="https://convertbetcodes.com/applet/applet.js?id=MmNWNFlzTmg5djVCN0NTZ2JrU1AwUG5aUzVhUTNzcjFFdDZEVENLbStjND0="></script>
EL;


        $moniebet = <<<EL
       <script src="https://convertbetcodes.com/applet/applet.js?id=TWh3Zm14TndkMURBQzg1eDBuZkNZMlJrNEs1eXVSaDdpZzFRTll4MERmcz0="></script>
EL;

        $sportybet = <<<EL
        <script src="https://convertbetcodes.com/applet/applet.js?id=THEvcGhTNjNMWWFaZXRDZlpvekQ0anlINkppNXFZVThFaUR1c2xZTXNvOD0="></script>
EL;




        $bookies = [
            "bet9ja" => [
                "background" => " ",
                "name" => "bet9ja",
                "installation" => $bet9ja

            ],

            "betking" => [
                "background" => " ",
                "name" => "betking",
                "installation" => $betking

            ],


            "moniebet" => [
                "background" => " ",
                "name" => "moniebet",
                "installation" => $moniebet

            ],


            "sportybet" => [
                "background" => " ",
                "name" => "sportybet",
                "installation" => $sportybet

            ],

        ];


        $bookie = $bookies[$bookie_key];
        $this->view('guest/demo', get_defined_vars(), true, true);
    }
}
