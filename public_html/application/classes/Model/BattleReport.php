<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_BattleReport extends ORM
{
	protected $belongs_to = array('battle') ; 
}