
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STTailor2STTailor1 extends Model_STTailor1STShop
{	

	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);
	}

}