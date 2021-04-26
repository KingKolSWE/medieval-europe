<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_RegionAnnouncement extends ORM
{
	protected $table_name = "regions_announcements";
	protected $sorting = array('id' => 'desc');
	protected $belongs_to = array( 'region' ); 
}
