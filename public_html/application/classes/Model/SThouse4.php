
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_4STHouse extends Model_Structure_STHouse
{	
	
	public function init()
	{
		parent::init();
		$this -> setBaseprice( 8400 );	
		$this -> setStorage( 5600000 );
		$this -> setRestFactor( 18 );
	}
	
	
	
}
