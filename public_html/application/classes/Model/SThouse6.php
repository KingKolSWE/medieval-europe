
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_6STHouse extends Model_Structure_STHouse
{	
	
	public function init()
	{
		parent::init();
		$this -> setBaseprice( 33600 );	
		$this -> setStorage( 22400000 );
		$this -> setRestFactor( 60 );
	}
	
	
	
}
