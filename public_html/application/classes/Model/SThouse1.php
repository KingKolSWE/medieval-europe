
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_1STHouse extends Model_Structure_STHouse
{	
	
	public function init()
	{
		parent::init();
		$this -> setBaseprice( 300 );	
		$this -> setStorage( 480000 );
		$this -> setRestFactor( 4 );
	}
	
	
}
