<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_GameEventSubscription extends ORM
{
	
	protected $has_one = array('cfggameevent') ; 
	
}