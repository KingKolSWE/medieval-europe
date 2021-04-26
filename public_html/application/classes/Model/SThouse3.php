
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_3STHouse extends Model_Structure_STHouse
{	
	
	public function init()
	{
		parent::init();
		$this -> setBaseprice( 4200 );	
		$this -> setStorage( 2800000 );
		$this -> setRestFactor( 12 );
	}
	
	
	
}
