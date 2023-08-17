<?php

use v2\Models\Tip;
use v2\Models\Event;
use v2\Models\Paper;
use v2\Models\BookMaker;
use v2\Classes\TipsFactory;
use v2\Classes\EventFetcher;
use v2\Models\SportsCategory;
use v2\Classes\Apifootball\Apifootball;

/**
 *
 */
class FactoryController extends controller
{

	public function __construct()
	{
	}



	public function check_performance()
	{
		$running_date = $_REQUEST['running_date'] ?? null;
		$performance_date = $_REQUEST['performance_date'];
		$per_page = $_REQUEST['per_page'] ?? 1;

		$result_source = BookMaker::find($_REQUEST['result_source_id']);
		
		echo "<pre>";
		print_r($result_source);

		//ensure result is set
		
        $tips = Tip::ToBeCheckedOn($running_date, $performance_date)->take($per_page)->get();
        // $tips = Tip::Running($running_date)->take($per_page)->get();
		echo "{$tips->count()} tips";
		
		
		foreach ($tips as $key => $tip) {
			echo $tip->paper->name;
		    echo $tip->id;
		    // return;
			$r = $tip->check_performance($performance_date, $result_source);			
		}

	}


	public function fetch_event()
	{
		$json = file_get_contents('php://input');
		$input = json_decode($json, TRUE);

		$event_fetcher = new EventFetcher;
		$source = BookMaker::find($input['fetcher_id']);
		$fetcher = new BookMaker::$book_register[$source->NameKey]['fetcher'];

		$event_date = date("Y-m-d", strtotime($input['event_date']));
		$db_category = SportsCategory::find($input['category_id']);

		$event_fetcher->setFetcher($fetcher)
			->setEventCategory($db_category->key_name)
			->setDate($event_date)
			->fetch($input['what_to_fetch']);

		Session::putFlash("success", "Event Fetched successfully");
	}

	public function factory()
	{


		$fetchers = BookMaker::all();
		$sports_categories = SportsCategory::all();
		$papers = Paper::fetch_available_paper(true)->with('editor')->get();

		$factory_data  = compact('fetchers', 'sports_categories', 'papers');

		header("content-type:application/json");
		$response  = compact('factory_data');

		echo json_encode($response);
	}



	public function create_tips()
	{

		$json = file_get_contents('php://input');
		$input = json_decode($json, TRUE);
		/* echo "<pre>";
		print_r($input);
 		*/
		$event_date = $input['event_date'] == null ? null : date("Y-m-d", strtotime($input['event_date']));


		$paper = Paper::find($input['paper_id']) ?? null;
		$no_of_events = $input['no_of_events'] ?? null;
		$source = BookMaker::find($input['event_source_id']);
		$bookmaker = BookMaker::find($input['bookmaker_id']);
		$db_category = SportsCategory::find($input['category_id']);
		$days_of_operations = $input['days_of_operations'] ?? null;
		$no_of_creations = $input['no_of_creations'] ?? 1;
		$no_of_keys = $input['no_of_keys'] ?? null;

		$pricing = $input['pricing'] == null ? [] : explode(",", $input['pricing']);

		$factory = new TipsFactory;
		$ads = $factory->generateTip(
			$no_of_creations,
			$event_date,
			$paper,
			$no_of_events,
			$source,
			$bookmaker,
			$db_category->key_name,
			$days_of_operations,
			$pricing,
			$no_of_keys
		);
		$no = count($ads);
		Session::putFlash("success", "$no Tip(s) Created successfully");
	}

	public function fetch_event_data()
	{

		$date = date("Y-m-d");
		$yesteday = date("Y-m-d", strtotime("-1 day"));
		$events = Event::whereDate('event_date', '>=', $yesteday)
			// ->Exclude(['dump', 'odds', 'result'])
			->with('bookmaker', 'category')
			->get()
			->map(function ($item) {
				$item->n_labels = $item->getLabelsAttribute();
				unset($item->dump);
				unset($item->odd);
				unset($item->result);
				return $item;
			});
/*
			$event = $events->map(function($item){
				unset($item->dump);
				unset($item->odds);
				unset($item->result);
				return $item;
			});*/

		
		$fetchers = BookMaker::all();
		$sports_categories = SportsCategory::all();
		$what_to_fetch = ['soccer', 'result'];

		$events_data  = compact('events', 'fetchers', 'sports_categories', 'what_to_fetch');

		header("content-type:application/json");
		$response  = compact('events_data');

		echo json_encode($response);
	}
}
