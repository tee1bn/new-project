<?php

/**
* 
*/
class Token
{
	
	public static $csrf_prefix = 'csrf_';

	public function __construct()
	{
		
	}


	public  static function set_csrf($csrf_field)
	{
			$project_name = Config::project_name();
			$_SESSION[(self::$csrf_prefix."$project_name")][$csrf_field] = md5(microtime());
			return self::csrf_field($csrf_field);
	}



	public  static function csrf_field($csrf_field)
	{
			$project_name = Config::project_name();
		return	 $_SESSION[(self::$csrf_prefix."$project_name")][$csrf_field];
	}

	




public static function evaluateOTPattempt()
{
			$otp_attempt = Session::get('session_otp_attempt') ;
			$otp_attempt++;

			Session::put('session_otp_attempt', $otp_attempt );

			return $otp_attempt;

}



public static function OTPattempt()
{
			$otp_attempt = Session::get('session_otp_attempt') ;
			

			return $otp_attempt;

}



public static function startNewOTPSession()
{

			Session::put('session_otp_attempt', 0);


}






	public  static function generateOTP()
	{
		Session::put('session_otp_is_fufilled', false );
		$otp = random_int(99999, 999999);
		Session::put('session_otp', $otp );

		}
	
	public  static function requestOTP()
	{
		return	Session::get('session_otp');

		}

	public  static function fufillOTP()
	{
		Session::put('session_otp_is_fufilled', true );

	}

public  static function isOTPFufilled()
	{
		 if(Session::get('session_otp_is_fufilled') === true){

		 		return true;
		 }else{

		 	return false;
		 }

		}

	

}