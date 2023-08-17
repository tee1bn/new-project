<?php

namespace v2\Traits;

use Exception;
use Redirect;
use Session;
use Config;

use Illuminate\Database\Capsule\Manager as DB;

/**
 * 
 */
trait HasStatus
{


	/*	public  $statuses_config = [
			'use'=> 'name',  //can be name or hierarchy e.g draft or 1
			'column'=> '', 
			'push_url'=> '', 
			'use_hierarchy'=> '', 
			'states'=>[
				[
				'name'=> '', //name of status e.g completed
				'hierarchy'=> '', //the hierarchy  int e.g 1
				'color'=> '',    //the color e.g warning
				'after_set'=> '', // a function that will be called after setting this status
				'before_set'=> '', // a function that will be called before setting this status
				'is_final'=> '', // this status cannot be reversed

			]
		],
	];
*/



	public static function pushStatus($id, string $status)
	{
		$model =  self::find($id);

		if ($model == null) {
			Session::putFlash('danger', "Invalid Request.");
			Redirect::back();
		}

		try {
			$model->markAs($status);
		} catch (\Throwable $th) {
			Session::putFlash("danger", "something went wrong");
		}
	}


	public function DisplayedStatusActions($use_confirmation = true)
	{
		$config = self::$statuses_config;
		$url = $config['push_url'];
		$domain = Config::domain();

		$endpoint = "$domain/$url";

		$text = '';
		foreach ($config['states'] as $key => $status) {
			$name = $status['name'];
			$link = "$endpoint/{$this->{$this->primaryKey}}/$name";


			$confirm_dialog = '$confirm_dialog';

			if ($use_confirmation) {

				$text .= <<<EOL
						  <a class="dropdown-item" href="javascript:void(0);"  
						  onclick="$confirm_dialog = new ConfirmationDialog('$link');" >$name</a>
EOL;
			} else {

				$text .= <<<EOL
						  <a class="dropdown-item" href="$link">$name</a>
EOL;
			}
		}

		return $text;
	}


	public function isAt($status)
	{
		$column = self::getStatusColumn();
		return $this->$column == $status;

		$response = self::HasStatus($status)->where('id', $this->id)->count() > 0;
		return $response;
	}

	public function scopeHasStatus($query, $status)
	{
		$column = self::getStatusColumn();
		return $query->where($column, $status);
	}

	public function usesHierarchy()
	{
		return  self::$statuses_config['use_hierarchy'];
	}

	public static function getStatusColumn()
	{
		return  self::$statuses_config['column'];
	}


	public static function getStatusUse()
	{
		return  self::$statuses_config['use'];
	}



	public function getStatusState()
	{

		$use = self::getStatusUse();
		$states = collect(self::$statuses_config['states'])->keyBy($use);
		$status = $states[$this->status];

		return $status;
	}


	public static function getStatusFilter($sieve)
	{

		$options = '';
		$use = self::getStatusUse();
		foreach (self::$statuses_config['states'] as $key => $state) {
			$shown_text = $state['name'];
			$id = $state[$use];
			$is_selected = isset($sieve['status']) && ($sieve['status'] == $id) ? 'selected' : '';
			$options .= "
			<option value='$id' $is_selected>$shown_text</option>
			";
		}

		$text = <<<EL
		<label>Status</label>
		<select class="form-control" name="status">
		    <option value="">Select</option>
		        $options
		</select>
EL;
		return $text;
	}


	public function markAs($name, array $more = [])
	{
		$column = self::getStatusColumn();
		$use = self::getStatusUse();


		//find the new status to be stored
		$states = collect(self::$statuses_config['states'])->keyBy('name');

		if (!array_key_exists($name, $states->toArray())) {
			$imploded = implode(",", array_keys($states->toArray()));
			throw new Exception("Status does not exist. Allowed status:$imploded", 1);
		}

		$new_state = $states[$name];

		$status = $new_state[$use];
		$new_hierarchy = $new_state['hierarchy'];


		$old_status = $this->$column;
		$states = collect(self::$statuses_config['states'])->keyBy($use);
		$old_hierarchy = $states[$old_status]['hierarchy'];
		$old_status_is_final = $states[$old_status]['is_final'];






		//check finality
		if (($old_status_is_final)) {
			throw new Exception("Current Status is final", 1);
		}


		//check heirachy
		if ($this->usesHierarchy()) {
			if (($new_hierarchy < $old_hierarchy)  && ($new_hierarchy !== null)) {
				throw new Exception("Status cannot be downgraded", 1);
			}
		}


		DB::beginTransaction();

		try {
			//code...

			//call before_set
			$before_set = $new_state['before_set'];

			if (method_exists($this, $before_set)) {
				$this->$before_set();
			}

			//update
			$this->update([$column => $status]);

			$this->update($more);


			//call after_set
			$after_set = $new_state['after_set'];
			if (method_exists($this, $after_set)) {
				$this->$after_set();
			}
			DB::commit();
			Session::putFlash("success", "marked as $name successfully");
		} catch (Exception $e) {
			DB::rollback();
			// print_r($e->getMessage());
		}


		return;
	}


	public function getDisplayableStatusAttribute()
	{
		$status = self::getStatusState();
		$text = $status['name'];
		$class = $status['color'];
		return $this->attributes['displayable_status'] = "<span class='badge badge-$class'>$text</span>";
	}
}
