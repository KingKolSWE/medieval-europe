<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Battle extends ORM
{
	protected $has_many = array('battle_participant') ; 
}