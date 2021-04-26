<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Religion extends ORM
{	
	protected $has_many = array('church');	
}
