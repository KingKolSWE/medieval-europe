<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_IpcheckshieldPremiumBonus extends Model_PremiumBonus
{
	
	var $name = '';
	var $info = array();
	var $canbeboughtonce = false;
			
	function __construct()
    {
        $this -> name = 'ipcheckshield';
	}
}
