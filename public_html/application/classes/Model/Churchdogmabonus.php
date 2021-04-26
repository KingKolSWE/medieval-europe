<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Churchdogmabonus extends ORM
{
	
	protected $belongs_to = array( 'church', 'cfgdogmabonus' );
 
}
