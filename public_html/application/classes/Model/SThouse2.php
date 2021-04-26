
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_2STHouse extends Model_Structure_STHouse
{	
	
	public function init()
	{
		parent::init();
		$this -> setBaseprice( 1800 );	
		$this -> setStorage( 1200000 );
		$this -> setRestFactor( 8 );
	}
	
	
	
}
