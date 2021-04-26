
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STPotter2STPotter1 extends Model_STPotter1STShop
{	

	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);
	}

}