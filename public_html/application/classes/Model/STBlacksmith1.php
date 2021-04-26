
<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STBlacksmith1STShop extends Model_Structure_STShop
{	

	public function init()
	{
		parent::init();
		$this -> setParenttype('shop');
		$this -> setSupertype('blacksmith');
		$this -> setCurrentLevel(1);
		$this -> setIsupgradable(true);
		$this -> setMaxlevel(2);
		$this -> setHoursfornextlevel(40);			
		$this -> setNeededmaterialfornextlevel(
			array(
				'iron_piece' => 15,
				'wood_piece' => 45,					
			)
		);
	}

}
