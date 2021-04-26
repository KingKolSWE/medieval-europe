<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_KingdomHistory extends ORM
{
	protected $table_name = 'kingdoms_history';
	protected $belongs_to = array ( 'Kingdom' );	
}
