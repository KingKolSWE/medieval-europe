<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Characterstat extends ORM
{
	
	protected $belongs_to = array( 'character' );
	protected $sorting = array('value' => 'desc' );
 
}
