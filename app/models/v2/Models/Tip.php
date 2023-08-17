<?php


namespace v2\Models;

use Config;
use v2\Models\Paper;
use v2\Classes\Gbeisele;
use v2\Models\BookMaker;
use v2\Classes\ResultChecker;
use Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Model as Eloquent;
use v2\Traits\HasDetails;

class Tip extends Eloquent
{
	use Filterable;
	use HasDetails;


	protected $fillable = [
		'editor_id',
		'paper_id',
		'start_date',
		'end_date',
		'failed_date',
		'checked_performances',
		'bookmaker_id',
		'predictions',
		'booking_code',
		'days_of_operation',
		'is_published',

	];


	protected $table = 'tips';



	public function get_performance($date, $result_source = null)
	{

		$detail = $this->getDetails;
		$round = $this->getround(null, null, $date); //current round

		$round_games = $detail['games'][$round];
		$game_date = $round_games['period']['date'];

		$prediction = $round_games['games'];

		$checker = new ResultChecker;
		if ($result_source == null) {
			$source  = BookMaker::find(1);
		} else {
			$source = $result_source;
		}

		$performance =	$checker
			->setSource($source)
			->setGameSourceId($this->bookmaker_id)
			->setEventDate($date)
			->setGames($prediction)
			->runCheck()
			->getPerformance();

		// $detail['games'][$round]['games'] = $performance;
		// $this->update(['predictions' => json_encode($detail)]);
		return $performance;
	}

	public function has_key($date)
	{


		$detail = $this->getDetails;

		$games = $detail['games'];

		$round = $this->getround(null, null, $date); //current round

		$keys = $games[$round]['keys'] ?? [];

		if (isset($keys) && count($keys) > 0) {
			return $keys;
		}

		return false;
	}

	public function has_performed_key($date, $result_source = null)
	{

		$detail = $this->getDetails;

		$shown_keys = $this->has_key($date);


		if (!$shown_keys) {
			return false;
		}

		$performance = $this->get_performance($date, $result_source);
		$performance_object = collect($performance['games'])->keyBy('item_id')->toArray();

		$result = [];
		foreach ($shown_keys as $key => $item_id) {
			$game = $performance_object[$item_id];
			$result[] = (int) $game['outcome']['win'] ?? 0;
		}

		$response =  count($result) == array_sum($result);

		return $response;
	}



	public function getmadeViewAttribute($responsive = null, $request_date = null)
	{
		$responsive = ($responsive === null) ? true : $responsive;
		$controller = new \home;
		$ad = $this;

		$round = $ad->getround(null, null, $request_date);

		return $controller->buildView('composed/ad', compact('ad', 'responsive', 'round'), true, true);
		return $controller->buildView('composed/ad', compact('ad', 'responsive'), true);
	}




	public function check_performance($date, $result_source = null)
	{
		$detail = $this->getDetails;
		$round = $this->getround(null, null, $date); //current round



		$performance = $this->get_performance($date, $result_source);

		
		$response = (int) $performance['win'];

		$checks = $this->getDetailsArrayAttribute('checked_performances');
		$checks['dates'][] = $date;
		$checks['dates'] = array_unique($checks['dates']);
		$this->updateDetailsByKey('dates', $checks['dates'], 'checked_performances');

		// if performance passed
		if ($response == 1) {
			$detail['games'][$round]['games'] = $performance['games'];
			$detail['games'][$round]['win'] = $performance['win'];
			$this->update(['predictions' => json_encode($detail)]);

			return;
		}

		//failed games
		//if bought
		$order_history = AdOrderHistory::history($date);
		$bought =	$order_history->has_record($this->id);

		if ($bought) {
			$this->mark_as_failed();
			return;
		}

		//no purchase
		//if key failed drop also
		if ($this->has_key($date) && ($this->has_performed_key($date, $result_source) == false)) {
			$this->mark_as_failed();
			return;
		}


		if (($this->id % 3) == 0) {
			$this->mark_as_failed();
			return;
		}

		//not purchase
		//not failed key


		// gbe ise le
		// include a performed games from result
		$this->gbe_ise_le($date, $result_source);
	}


	public function gbe_ise_le($date, $result_source = null)
	{
		//stop if editor is not simulated
		$editor = $this->paper->editor;


		if (!$editor->isA('s_tipster')) {
			return;
		}


		$gbe_ise_le  = new Gbeisele;

		$response = $gbe_ise_le->setDate($date)
			->setResultSource($result_source)
			->setTip($this)
			->setPerformance()
			->setResultChecker()
			->run()
			->response;


		$this->update(['predictions' => json_encode($response)]);
	}




	public function scopePublished($query)
	{
		return $query->where('is_published', 1);
	}

	public function scopeNotPublished($query)
	{
		return $query->where('is_published', '!=', 1);
	}

	public function publish()
	{
		if ($this->is_published()) {
			return;
		}

		$this->update(['is_published' => 1]);
		return $this;
	}

	public function unPublish()
	{
		$this->update(['is_published' => null]);
		return $this;
	}

	public function getAdminEditHref()
	{
		$domain = Config::domain();
		$link = "$domain/admin/edit_advert/$this->id";

		return $link;
	}


	public function getEditorEditHrefAttribute()
	{
		$domain = Config::domain();
		$link = "$domain/editor/edit_advert/$this->id";

		return $link;
	}



	public function getEditorDeleteHrefAttribute()
	{
		$domain = Config::domain();
		$link = "$domain/editor/delete_advert/$this->id";

		return $link;
	}


	public function getEditorStatHrefAttribute()
	{
		$domain = Config::domain();
		$link = "#";

		return $link;
	}



	public function get_games($round = 'all')
	{
		$details = $this->getDetails;
		switch ($round) {
			case 'all':

				return $details['games'];
				break;

			default:

				return $details['games'][$round];

				break;
		}
	}



	public function getround($week = null, $year = null, $date = null)
	{

		$today = $date == null ? date("Y-m-d") : date("Y-m-d", strtotime($date));

		$games = $this->getDetails['games'];

		foreach ($games as $round => $game) {
			if ($game['period']['date'] == $today) {
				return (int) $round;
				break;
			}
		}
	}


	public function paper()
	{
		return $this->belongsTo(Paper::class, 'paper_id');
	}

	public function mark_as_failed()
	{
		$this->update(['failed_date' => date("Y-m-d")]);
	}


	public function scopeNotFailed($query)
	{
		return $query->where('failed_date', null);
	}

	public function scopeFailed($query)
	{
		return $query->where('failed_date', '!=', null);
	}

	public function is_failed()
	{
		return $this->failed_date != null;
	}


	public function getFailedStatusAttribute()
	{
		if (!$this->is_failed()) {
			return "<span class='badge badge-sm badge-warning'>ongoing</span>";
		}

		return "<span class='badge badge-sm badge-danger'>failed</span>";
	}


	public static function scopeRunningForAdmin($query, $date = null)
	{
		if ($date == null) {
			$today = date("Y-m-d");
		} else {

			$today = $date;
		}

		return $query->whereDate('start_date', '<=', $today)
			->whereDate('end_date', '>=', $today)
			->where('is_published', 1);
	}


	public function scopeToBeCheckedOn($query, $running_date, $performance_date)
	{
		return $query->Running($running_date)
			->whereRaw("(checked_performances not like '%$performance_date%' OR `checked_performances` is null)");
	}

	public function scopeNotToBeCheckedOn($query, $running_date, $performance_date)
	{
		return $query->Running($running_date)
			->where('checked_performances', 'like', "%$performance_date%");
	}

	public function scopeRunning($query, $date = null)
	{
		if ($date == null) {
			$today = date("Y-m-d");
		} else {

			$today = $date;
		}

		return $query->whereDate('start_date', '<=', $today)
			->whereDate('end_date', '>=', $today)
			->where('failed_date', null)
			->where('is_published', 1);
	}


	public function bookmaker()
	{
		return $this->belongsTo(BookMaker::class, 'bookmaker_id');
	}



	public function tipster()
	{
		return $this->belongsTo('User', 'editor_id');
	}

	public function editor()
	{
		return $this->belongsTo('User', 'editor_id');
	}


	public function getPredictionsArray()
	{
		if ($this->predictions == null) {
			return [];
		}

		return json_decode($this->predictions, true);
	}



	public function getgetDetailsAttribute()
	{
		return json_decode($this->predictions,  true);
	}
}
