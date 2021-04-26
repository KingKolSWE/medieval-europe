<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Groupcharacter extends ORM
{
	protected $has_one = array( 'character' );
}
