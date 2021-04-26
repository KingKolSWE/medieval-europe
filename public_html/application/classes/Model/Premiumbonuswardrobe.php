<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_WardrobePremiumBonus extends Model_PremiumBonus
{
	
	var $name = '';
	var $info = array();
	var $canbeboughtonce = true;
			
	function __construct()
    {
        $this -> name = 'wardrobe';
	}
	
	function get_tutorial_html()
	{
		
		$html = 
		"<div class='center'>" . 
			html::anchor('https://wiki.medieval-europe.eu/index.php?title=Wardrobe_Bonus', kohana::lang('global.tutorial'), 	array('target' => 'new')) . 
		"</div>";
		
		return $html;
	}
}