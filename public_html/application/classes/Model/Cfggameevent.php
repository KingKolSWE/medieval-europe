<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CfgGameEvent extends ORM
{
	
	protected $has_many = array('gameevent_subscription') ; 
	
}