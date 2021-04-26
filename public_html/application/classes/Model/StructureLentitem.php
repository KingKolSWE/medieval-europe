<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_StructureLentitem extends ORM
{
	
	protected $table_name = 'structure_lentitems';
	protected $belongs_to = array('structure');

	
}