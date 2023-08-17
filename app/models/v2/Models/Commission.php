<?php

namespace v2\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use  v2\Traits\Wallet;
use  v2\Models\PayoutWallet;
use SiteSettings, Config;
use Illuminate\Database\Capsule\Manager as DB;
use  Filters\Traits\Filterable;


class Commission extends Eloquent 
{
	
	use Wallet,Filterable;

	protected $fillable = [
		'user_id',
		'order_id',
		'admin_id',
		'upon_user_id',
		'amount',
		'type',
		'paid_at',
		'earning_category',
		'binary_leg',
		'binary_points',
		'status',
		'identifier',
		'comment',
		'extra_detail'	,
		'transferred_from'	,
	];


	protected $table = 'wallet_for_commissions';




	

	public static function SplitCommission($user_id, array $date_range)
	{

		$rules_settings =  SiteSettings::find_criteria('rules_settings');
		$setting = $rules_settings->settingsArray;


		$heldcoin_percent =  $setting['income_split_percent']['trucash_percent'];  //put in held coin
		$payout_wallet_percent =  $setting['income_split_percent']['cash_percent'];  //put in held coin

	
 


		//because we keep accumulating autobonus
		$auto_bonus =  self::availableBalanceOnUser($user_id, 'auto_bonus', $date_range['end_date']);


	    $book_balance = self::availableBalanceOnUser($user_id, null, $date_range['end_date'] ) - $auto_bonus;

	    $book_balance = round($book_balance, 2);

	    
		if ($book_balance <= 0) {
			return;
		}


		$amount_going_to_heldcoin = $heldcoin_percent * 0.01 * $book_balance;
		$amount_going_to_payout = $payout_wallet_percent * 0.01 * $book_balance;


		$today = $date_range['end_date'];
		$month = date("Y-m", strtotime($today));
		$end_date = $date_range['end_date'];
		$identifier = "1_split_commission_{$user_id}_$end_date";

		$extra_detail = json_encode([
			'reason' => 'split_commission',
			'iteration' => '1',
		]);

		$comment = "Move $book_balance from Commission,  $amount_going_to_heldcoin($heldcoin_percent%)  to Heldcoin,
		$amount_going_to_payout($payout_wallet_percent%) to Payout Wallet ";


		DB::beginTransaction();
		try {

				
			$debit =self::createTransaction(
				'debit',
				$user_id,
				null,
				$book_balance,
				'completed',
				'commission',
				$comment,
				$identifier, 
				null, 
				null,
				$extra_detail,
				$today,
				null,
				false//dont check balance just debit, already checked at the top
			);

			if ($debit == false) {
				throw new \Exception("Could not debit commission", 1);
			}

			// $comment = "Move $payout_wallet_percent% from Commission to Payout Wallet";
				
			$credit =PayoutWallet::createTransaction(
				'credit',
				$user_id,
				null,
				$amount_going_to_payout,
				'completed',
				'commission',
				$comment,
				$identifier, 
				null, 
				null,
				$extra_detail,
				$today
			);

			if ($credit == false) {
				throw new \Exception("Could not credit ", 1);
			}

			// $comment = "Move $amount_going_to_heldcoin% from Commission to Heldcoin";
			$credit =HeldCoin::createTransaction(
				'credit',
				$user_id,
				null,
				$amount_going_to_heldcoin,
				'completed',
				'commission',
				$comment,
				$identifier, 
				null, 
				null,
				$extra_detail,
				$today
				
			);

			if ($credit == false) {

				throw new \Exception("Could not credit", 1);
			}


			DB::commit();
		} catch (Exception $e) {

			print_r($e->getMessage());

			DB::rollback();
			
		}
	}


}


















?>