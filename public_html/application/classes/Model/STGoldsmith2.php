
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STGoldsmith2STGoldsmith1 extends Model_STGoldsmith1STShop
{	

	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);
	}

}
