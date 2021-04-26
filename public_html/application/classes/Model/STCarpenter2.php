
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STCarpenter2STCarpenter1 extends Model_STCarpenter1STShop
{	
	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);
	}

}
