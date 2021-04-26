<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_PremiumBonusarmorsetAtelierLicense extends Model_AtelierLicensePremiumBonus
{
	
	var $name = '';
	var $info = array();
	var $canbeboughtonce = false;
			
	function __construct()
    {
        $this -> name = 'atelier-license-armor_set';
	}
}
