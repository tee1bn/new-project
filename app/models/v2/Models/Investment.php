<?php

namespace v2\Models;

use SiteSettings;
use  v2\Models\Wallet;
use Exception, Config;
use  Filters\Traits\Filterable;

use  v2\Models\InvestmentPackage;
use function GuzzleHttp\json_decode;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Investment extends Eloquent 
{
	
	use Filterable;

	protected $fillable = [
		'user_id',
		'admin_id',
		'pack_id',
		'capital',
        'worth_after_maturity',
		'currency_id',
		'matures_at',
		'status',
		'comment',
		'paused_at',
		'extra_detail',	
	];


	protected $table = 'investments';

    public $statuses = [
        1=>'unsettled',
        2=>'settled',
    ];

    public function settle()
    {

        if ((! $this->isMatured())) {
            return;
        }

        if ($this->isSettled()) {
            return;
        }

        //credit the owner with return

        DB::beginTransaction();
             
        try {
            //debit this user

            //debit user
            $comment = "ROI on #$this->id investment";
            $credit = Wallet::createTransaction(
                'credit',
                $this->user_id,
                null,
                $this->worth_after_maturity,
                'completed',
                'investment',
                $comment,
                null,
                $this->id,
                null
            );


            $this->markAsSettled();

            if ($credit == false) {
                throw new Exception("Could not debit", 1);
            }

            DB::commit();

            return true;
         } catch (Exception $e) {
            DB::rollback();
             
            return false;
         }
        


    }

    public function markAsSettled()
    {
        return $this->update([
            'status' => 2
        ]);
    }


    public function scopeRipeForSettlement($query)
    {
        return $query->Matured()->where('status', '!=', 2);

    }

    public function scopeMatured($query)
    {
        $now = date("Y-m-d H:i:s");
        return $query->addSelect('*',DB::raw("TIME_TO_SEC(TIMEDIFF('$now',matures_at)) secs_after_maturity"))
        ->having('secs_after_maturity', '>', 0);

    }

	public function isFirstInvestmentForUser()
	{
		return  self::where('user_id', $this->user_id)->orderBy('id')->first()->id == $this->id;
	}
	

    public function give_referral_commission()
    {   
		$buyer = $this->user;
		$sponsor = $this->user->referred_members_uplines(1)[1] ?? false;

		if (!$sponsor || !$this->isFirstInvestmentForUser()) {
			return;
		}

		
		$referral_percent = (int) SiteSettings::find_criteria('rules_settings')->settingsArray['referral_percent'];
		$commission = $referral_percent * 0.01 * $this->capital;

		//credit the upline
		//debit user
		$comment = "{$referral_percent}% Referral Commission on {$this->pack->name} at {$this->capital}";

		$credit = Wallet::createTransaction(
			'credit',
			$sponsor->id,
			$buyer->id,
			$commission,
			'completed',
			'commission',
			$comment,
			null,
			null,
			null
		);

		return ;

    }

    public function pack()
    {
        return $this->belongsTo(InvestmentPackage::class, 'pack_id');
    }


    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }


	public function scopeNotRunning($query)
	{
		return $query->where('paused_at', '!=', null);
	}

	public function scopeRunning($query)
	{
		return $query->where('paused_at', null);
	}

	public function is_completed()
	{
		$response = $this->status == 2;
		return $response;
	}




	public function scopeSettledInvestment($query)
	{
		return	$query->where('status', 2);
	}


	public function scopeUnSettledInvestment($query)
	{
        return    $query->where('status','!=', 2);

	}

    public function getPauseUrlAttribute()
    {
    		$href =  Config::domain()."/package_crud/pause_package/".$this->id;
    		return $href ;
    }

    public function pause()
    {

    	if ($this->is_completed()) {

    		Session::putFlash("danger","unable to pause completed pack");
    		return;
    	}

    	DB::beginTransaction();

    	try {
    		
    		$this->update(['paused_at'=> date("Y-m-d H:i:s")]);

    		DB::commit();
    		Session::putFlash("success","Pack paused successfully");

    		return true;
    	} catch (Exception $e) {
    		DB::rollback();
    		Session::putFlash("danger","unable to pause the pack");
    		return false;
    	}


    }


    public function play()
    {

    	if ($this->is_completed()) {

    		Session::putFlash("danger","unable to pause completed pack");
    		return;
    	}


    	DB::beginTransaction();

        $delay = time() - strtotime($this->paused_at);
        $maturity_date =  strtotime(date("Y-m-d H:i:s","$this->matures_at + $delay"));        

    	try {
    		
    		$this->update([
    						'pasued_at'=> null , 
                            'matures_at' => $maturity_date
    					]);

    		DB::commit();
    		Session::putFlash("success","Investment resumed successfully");

    		return true;
    	} catch (Exception $e) {
    		DB::rollback();
    		Session::putFlash("danger","unable to resume the Investment");
    		return false;
    	}
    }


	public function getPlayStatusAttribute()
	{

		if ($this->paused_at == null) {

			$return = "<span class='badge badge-success'>Running</span>";
		}else{

			$return = "<span class='badge badge-info'>Paused</span>";
		}

		return $return;
	}


	public function getRoiStatusAttribute()
	{
		if ($this->isMatured()) {

			$return = "<span class='badge badge-success'>completed</span>";
		}else{

			$return = "<span class='badge badge-info'>ongoing</span>";
		}

		return $return;
	}



	public function getByAdminStatusAttribute()
	{

		if ($this->admin_id != null) {

			$return = "<span class='badge badge-info'>admin</span>";
		}else{

			$return = "<span class='badge badge-info'></span>";
		}

		return $return;
	}




    public function getMaturityGrowth()
    {
        if ($this->isMatured()) {
            return 100;
        }

        $start_time = strtotime($this->created_at);
        
        $maturity_time = strtotime($this->matures_at);

        $growth_period = time() - $start_time;
        $maturity_period = $maturity_time - $start_time;
        
        
        $maturity_growth =  ($growth_period/$maturity_period) * 100;

        return  round($maturity_growth, 2);
    }



    public function spread()
    {
        $package_details = json_decode($this->DetailsArray['details'], true);
        
        $daily = round( ($this->worth_after_maturity / $package_details['maturity_in_days']), 2); 
        $daily_percent_roi = round(($package_details['roi_percent'] / $package_details['maturity_in_days']), 2);
        $maturity_in_days = $package_details['maturity_in_days']; 

        $response = compact('daily', 'daily_percent_roi', 'maturity_in_days');
        return $response;
    }
    
    
    public function isMatured()
    {
        return (time() > strtotime($this->matures_at));
    }

    public function isSettled()
    {
        return ($this->status == 2 );
    }



    public function getDetailsArrayAttribute()
    {
        if ($this->extra_detail == null) {
            return [];
        }

        return json_decode($this->extra_detail, true);
    }




}
