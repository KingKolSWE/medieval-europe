<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CfgkingdomprojectDependency extends ORM
{
	protected $has_one = array( 'cfgitem' );		
}
?>
