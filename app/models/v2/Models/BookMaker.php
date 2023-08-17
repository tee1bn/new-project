<?php

namespace v2\Models;

use v2\Classes\Ilot\Ilot;
use v2\Classes\DbBet\DbBet;
use v2\Classes\Zebet\Zebet;
use v2\Classes\_1xbit\_1xbit;
use v2\Classes\_22Bet\_22Bet;
use v2\Classes\Bet9ja\Bet9ja;
use v2\Classes\Betika\Betika;
use v2\Classes\Betway\Betway;
use v2\Classes\Melbet\Melbet;
use v2\Classes\Msport\Msport;
use v2\Classes\Odibet\Odibet;
use v2\Classes\Bangbet\Bangbet;
use v2\Classes\Betbiga\Betbiga;
use v2\Classes\Betfury\Betfury;
use v2\Classes\Betking\BetKing;
use v2\Classes\Betpawa\Betpawa;
use v2\Classes\Frapapa\Frapapa;
use v2\Classes\Helabet\Helabet;
use v2\Classes\Linebet\Linebet;
use v2\Classes\Spotika\Spotika;
use v2\Classes\Starbet\Starbet;
use v2\Classes\Wazobet\Wazobet;
use v2\Classes\Betafriq\Betafriq;
use v2\Classes\Lionsbet\Lionsbet;
use v2\Classes\Luckybet\Luckybet;
use v2\Classes\MegaPari\MegaPari;
use v2\Classes\Merrybet\Merrybet;
use v2\Classes\Moniebet\Moniebet;
use v2\Classes\Nairabet\Nairabet;
use v2\Classes\One1xbet\One1xbet;
use v2\Classes\Paripesa\Paripesa;
use v2\Classes\Accessbet\Accessbet;
use v2\Classes\Betandyou\Betandyou;
use v2\Classes\Betwinner\Betwinner;
use v2\Classes\Sportybet\Sportybet;
use v2\Classes\YangaSport\YangaSport;
use v2\Classes\Betxperience\Betxperience;
use v2\Classes\Livescorebet\Livescorebet;
use Illuminate\Database\Eloquent\Model as Eloquent;
use v2\Classes\BCgame\BCgame;
use v2\Classes\Betarena\Betarena;
use v2\Classes\Betcoza\Betcoza;
use v2\Classes\Bongobongo\Bongobongo;
use v2\Classes\Captainsbet\Captainsbet;
use v2\Classes\Easywin\Easywin;
use v2\Classes\Parimatch\Parimatch;
use v2\Classes\Pixwin\Pixwin;
use v2\Classes\Sportpesa\Sportpesa;

class BookMaker extends Eloquent
{

	protected $fillable = [
		'name',
		'key_name',
		'country_id',
		'details',
		'status',
	];


	protected $table = 'bookmakers';

	/**
	 * Register all fetchers. The key is the keyname 
	 *
	 * @var array
	 */
	public static $book_register = [
		'betking' => [
			'fetcher' => BetKing::class,
			'group' => 'betking',
		],

		'bet9ja' => [
			'fetcher' => Bet9ja::class,
		],
		'sportybet' => [
			'fetcher' => Sportybet::class,
			'group' => 'sportybet',
		],
		'1xbet' => [
			'fetcher' => One1xbet::class,
			'group' => '1xbet',
		],

		'22bet' => [
			'fetcher' => _22Bet::class,
			'group' => '1xbet',
		],
		'linebet' => [
			'fetcher' => Linebet::class,
			'group' => '1xbet',
		],
		'melbet' => [
			'fetcher' => Melbet::class,
			'group' => '1xbet',
		],
		'paripesa' => [
			'fetcher' => Paripesa::class,
			'group' => '1xbet',
		],
		'msport' => [
			'fetcher' => Msport::class,
			'group' => 'sportybet',
		],
		'bangbet' => [
			'fetcher' => Bangbet::class,
			'group' => 'sportybet',
		],
		'nairabet' => [
			'fetcher' => Nairabet::class,
		],
		'accessbet' => [
			'fetcher' => Accessbet::class,
			'extend' => "nairabet",
		],

		'merrybet' => [
			'fetcher' => Merrybet::class,
			'extend' => "nairabet",
		],

		'lionsbet' => [
			'fetcher' => Lionsbet::class,
			'extend' => "nairabet",
		],
		'betwinner' => [
			'fetcher' => Betwinner::class,
			'group' => '1xbet',
		],
		'megapari' => [
			'fetcher' => MegaPari::class,
			'group' => '1xbet',
		],
		'helabet' => [
			'fetcher' => Helabet::class,
			'group' => '1xbet',
		],

		'1xbit' => [
			'fetcher' => _1xbit::class,
			'group' => '1xbet',
		],

		'betandyou' => [
			'fetcher' => Betandyou::class,
			'group' => '1xbet',
		],

		'double_bet' => [
			'fetcher' => DbBet::class,
			'group' => '1xbet',
		],
		'livescorebet' => [
			'fetcher' => Livescorebet::class,
		],

		'betway' => [
			'fetcher' => Betway::class,
			'group' => 'sportybet',
			'dialect' => [
				// ["ng", "ke", "gh"]
			],
			'same_code' => [
				["ng", "ke", "gh", "zm"]
			],
		],
		'betika' => [
			'fetcher' => Betika::class,
			'group' => 'sportybet',
		],
		'odibet' => [
			'fetcher' => Odibet::class,
			'group' => 'sportybet',
		],
		'ilot' => [
			'fetcher' => Ilot::class,
			'group' => 'sportybet',
		],
		'wazobet' => [
			'fetcher' => Wazobet::class,
			'group' => 'sportybet',
		],
		'spotika' => [
			'fetcher' => Spotika::class,
			'group' => 'sportybet',
		],
		'betafriq' => [
			'fetcher' => Betafriq::class,
			'group' => 'sportybet',
		],
		'moniebet' => [
			'fetcher' => Moniebet::class,
			'group' => 'sportybet',
		],
		'betpawa' => [
			'fetcher' => Betpawa::class,
		],
		'starbet' => [
			'fetcher' => Starbet::class,
			'group' => 'sportybet',
		],
		'betfury' => [
			'fetcher' => Betfury::class,
			'group' => 'sportybet',
		],

		'captainsbet' => [
			'fetcher' => Captainsbet::class,
			'group' => 'sportybet',
		],

		'betbiga' => [
			'fetcher' => Betbiga::class,
			'group' => 'sportybet',
		],

		'yangasport' => [
			'fetcher' => YangaSport::class,
			'group' => 'sportybet',
		],
		'zebet' => [
			'fetcher' => Zebet::class,
			'group' => 'sportybet',
		],


		'frapapa' => [
			'fetcher' => Frapapa::class,
			'group' => 'sportybet',
		],
		'betxperience' => [
			'fetcher' => Betxperience::class,
			'group' => 'sportybet',
		],
		'luckybet' => [
			'fetcher' => Luckybet::class,
			'group' => 'sportybet',
		],

		'betarena' => [
			'fetcher' => Betarena::class,
			'group' => 'sportybet',
		],
		'easywin' => [
			'fetcher' => Easywin::class,
			'group' => 'sportybet',
		],
		'bcgame' => [
			'fetcher' => BCgame::class,
			'group' => 'sportybet',
		],

		'betcoza' => [
			'fetcher' => Betcoza::class,
		],

		'unimarket' => [
			'fetcher' => Bet9ja::class,
		],
		'bongobongo' => [
			'fetcher' => Bongobongo::class,
			'group' => 'sportybet',
		],
		'parimatch' => [
			'fetcher' => Parimatch::class,
		],
		'sportpesa' => [
			'fetcher' => Sportpesa::class,
		],

		'pixwin' => [
			'fetcher' => Pixwin::class,
			'group' => 'sportybet',
		],


	];

	public static $betfury_list = ["betfury", "captainsbet", "bcgame"];


	public function scopefindByKeyName($query, $key_name, $column = 'name')
	{
		return $query->where($column, $key_name);
	}



	public function getRegisteredClass()
	{
		return self::$book_register[$this->NameKey];
	}

	public function getNameKeyAttribute()
	{
		if ($this->key_name == null) {

			return strtolower(str_replace(' ', '', $this->name));
		} else {
			return $this->key_name;
		}
	}


	public function getDetailsArrayAttribute()
	{
		if ($this->details == null) {
			return [];
		}

		return json_decode($this->details, true);
	}
}
