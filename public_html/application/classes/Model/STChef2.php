
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STChef2STChef1 extends Model_STChef1STShop
{	
	
	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);
	}
}
