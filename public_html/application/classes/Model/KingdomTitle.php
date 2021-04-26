<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_KingdomTitle extends ORM
{
	protected $belongs_to = array('kingdom', 'cfgachievement' );
    
	
}
