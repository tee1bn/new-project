<?php


use Illuminate\Database\Eloquent\Model as Eloquent;
use Carbon\Carbon;

class User extends Eloquent 
{
	
	protected $fillable = [
					'admin_id',
					'company_id', //this is the currently accessed business id
					'admin_code',
					'email',
					'pass_salt',
					'password',
					'first_name',
					'last_name',
					'middle_name',
					'last_login',
					'status',
					'created',
					'updated',
	];
	
	protected $table = 'admin';
	protected $primaryKey = 'admin_id';
	public $timestamps = false;
  	protected $dates = [
        'created_at',
        'updated_at',
        'lastseen_at'

    ];
    protected $hidden = ['password'];




	public function businesses()
	{

		return $this->belongsToMany('Company', 'ac_accountants_businesses', 'user_id','business_id');
	}


    public function getidAttribute()
    {
    	return $this->admin_id;
    }


    //this is like the organisatioin
    public function company()
    {
    	return $this->belongsTo('Company', 'company_id');
    }





    public function getfullnameAttribute()
    {
    	return "$this->last_name $this->first_name";
    }




















	/**
	 * [getFirstNameAttribute eloquent accessor for firstname column]
	 * @param  [type] $value [description]
	 * @return [string]        [description]
	 */
	public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }




	/**
	 * [getFirstNameAttribute eloquent accessor for firstname column]
	 * @param  [type] $value [description]
	 * @return [string]        [description]
	 */
	public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }


	/**
	 * eloquent mutators for password hashing
	 * hashes user password on insert or update
	 *@return 
	 */
	 public function setPasswordAttribute($value)
	    {
	        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
	    }


	/**
	 * eloquent mutators for username 
	 * hashes user username on insert or update
	 *@return 
	 */
	 public function setUsernameAttribute($value)
	    {
	        $this->attributes['username'] = trim($value);
	    }




}

















?>