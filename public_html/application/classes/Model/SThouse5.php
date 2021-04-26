
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_5STHouse extends Model_Structure_STHouse
{	
	
	public function init()
	{
		parent::init();
		$this -> setBaseprice( 16800 );	
		$this -> setStorage( 11200000 );
		$this -> setRestFactor( 32 );
	}
	
	
	
}
