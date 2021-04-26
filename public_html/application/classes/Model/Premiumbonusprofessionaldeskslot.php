<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_ProfessionaldeskslotPremiumBonus extends Model_PremiumBonus
{
	
	function __construct()
    {
        $this -> name = 'professionaldeskslot';
	}
	
	function postsaveactions( $char, $cut, $par, &$message )
	{
		$char -> modify_stat(
			'professionaldeskslot', 
			25, 
			null,
			null,
			false,
			null,
			null,
			null,
			null,
			null,
			null );
		parent::postsaveactions($char, $cut, $par, $message);
		return true;
	}

}
