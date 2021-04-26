<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_TravelerpackagePremiumBonus extends Model_PremiumBonus
{
	
	var $name = '';
	var $info = array();
	var $canbeboughtonce = false;
			
	function __construct()
    {
        $this -> name = 'travelerpackage';
	}
	
	function postsaveactions( $char, $cut, $par, &$message )
	{
		// event for quest
		$_par = array();
		Model_GameEvent::process_event( $char, 'acquiretravelbonus', $_par );
	
		parent::postsaveactions($char, $cut, $par, $message );
		return true;
	}
	
	
}