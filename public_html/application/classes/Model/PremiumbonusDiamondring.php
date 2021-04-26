<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_DiamondringPremiumBonus extends Model_PremiumBonus
{
	
	function __construct()
    {
        $this -> name = 'diamondring';
	}
	
	function postsaveactions( $char, $cut, $par, &$message )
	{
		$item = Model_Item::factory( null, 'ringdiamond' );
		$item -> additem( 'character', $char -> id, 1 );
		parent::postsaveactions($char, $cut, $par, $message );
		return true;
	}
}
