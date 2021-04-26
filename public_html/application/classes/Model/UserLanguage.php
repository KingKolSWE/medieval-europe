<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_UserLanguage extends ORM
{
	protected $belongs_to = array( 'user' );		
}