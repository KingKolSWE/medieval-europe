
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STBlacksmith2STBlacksmith1 extends Model_STBlacksmith1STShop
{	

	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);
	}

}
