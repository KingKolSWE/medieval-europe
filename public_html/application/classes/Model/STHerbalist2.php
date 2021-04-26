
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STHerbalist2STHerbalist1 extends Model_STHerbalist1STShop
{	

	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);
	}

}