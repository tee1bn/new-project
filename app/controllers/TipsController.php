<?php

use v2\Shop\Shop;
use v2\Models\Tip;
use Filters\Filters\TipFilter;

/**
 *
 */
class TipsController extends controller
{


    public function __construct()
    {
    }


    public function view_cart()
    {

        $shop = new Shop;
        $cart = json_decode($_SESSION['cart'], true)['$items'];

        if (count($cart) == 0) {
            Session::putFlash("info", "Please add item to the cart.");
            Redirect::to('tips');
        }

        $url = Config::domain() . '/shop/fetch_ads?';

        $this->view('guest/cart', compact('shop', 'url'));
    }


    public function index()
    {
        /*        echo "<pre>";
        print_r(Location::location());

        return;
*/
        $this_year = date("Y");

        $per_page = 36;

        $date = (!isset($_GET['date'])) ? date("Y-m-d") : $_GET['date'];

        $page = (!isset($_GET['page'])) ? 1 : $_GET['page'];
        $skip = (($page - 1) * $per_page);

        $sieve = $_REQUEST['sieve'] ?? [];
        $query = array_merge([], $sieve);

        $http_build_query = array_merge($query, compact('page'));

        $domain = Config::domain();

        $url = $domain . '/shop/fetch_ads?' . http_build_query($http_build_query);

        $filter =  new  TipFilter($query);

        if ($this->admin()) {

            $sql = Tip::RunningForAdmin($date)
                ->Filter($filter)
                ->skip($skip)
                ->take($per_page)->with('paper');

            $total = Tip::RunningForAdmin($date)->count();

            $data = Tip::Running($date)
                ->Filter($filter)->count();
        } else {

            $data = Tip::Running($date)
                ->Filter($filter)->count();

            $sql = Tip::Running($date)
                ->Filter($filter)
                ->skip($skip)
                ->take($per_page)->with('paper');

            $total = Tip::Running($date)->count();
        }

        $running_ad = $sql->get();
        $note = MIS::filter_note($running_ad->count(), $data, $total,  $sieve, 1);

        /*	if ((($year == 2020) && ($week < 39) ) || (($week > $current_week) && (date("Y") == $year)) ){

				Session::putFlash("danger", "This records cannot be fetched. Please try another search.");
				$running_ad = collect([]);
				$total = 0;
				$url= null;
			}
*/

        $this->view('guest/tips', compact('url', 'total', 'per_page', 'page', 'running_ad', 'sieve','note'));
    }
}
