<?php


namespace v2\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use v2\Models\BookMaker;
use v2\Models\SportsCategory;

class Event extends Eloquent
{

	protected $fillable = [
		'country_id',
		'bookmaker_id',
		'dump',
		'result',
		'odds',
		'event_date',
		'category_id',
	];


	protected $table = 'events';


	public function getLabelsAttribute($value = '')
	{
		$labels = ['dump', 'odds', 'result'];
		$show = [];
		foreach ($labels as $key => $label) {
			if ($this->$label == null) {
				continue;
			}
			$show[] = $label;
		}

		// $this->labels = $show;/
		return $show;
	}


	public function scopeExclude($query, $value = [])
	{
		return $query->select(array_diff($this->fillable, (array) $value));
	}


	public function category()
	{
		return $this->belongsTo(SportsCategory::class, 'category_id');
	}



	public function bookmaker()
	{
		return $this->belongsTo(BookMaker::class, 'bookmaker_id');
	}


	public function getMatchList()
	{	
		$class = $this->bookmaker->getRegisteredClass()['fetcher'];
		$bookmaker = new $class;
			
		if (method_exists($bookmaker, 'getMatchList')) {
			return $bookmaker->getMatchList($this->DetailsArray);
		}

		return false;
	}

	public function getDetailsArrayAttribute()
	{
		if ($this->dump == null) {
			return [];
		}

		return json_decode($this->dump, true);
	}



	public function getResultArrayAttribute()
	{
		if ($this->result == null) {
			return [];
		}

		return json_decode($this->result, true);
	}

	public function getOddsArrayAttribute()
	{
		if ($this->odds == null) {
			return [];
		}

		return json_decode($this->odds, true);
	}
}
