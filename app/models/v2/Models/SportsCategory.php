<?php


namespace v2\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;


class SportsCategory extends Eloquent 
{
	
	protected $fillable = [
			'key_name',
			'name',
			'details',
			'status',
	];
	

	protected $table = 'sports_categories';


	public function getNameKeyAttribute()
	{
		return strtolower(str_replace(' ', '', $this->name));
	}


	public function scopefindByKeyName($query, $key_name)
	{
		return $query->where('key_name', $key_name);
	}
			

	public function getDetailsArrayAttribute()
	{
		if ($this->details == null) {
		    return [];
		}

		return json_decode($this->details, true);		
	}




}
