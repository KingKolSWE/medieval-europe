<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_KingdomTopic extends Model_Topic
{			
	
	protected $table_name = 'kingdom_forum_topic';
	
	function add_model()
	{
		
		if ( $char -> id != Model_Kingdom::get_king( $char -> region -> kingdom_id ) )
		{ 
			$message = 'global.operationnotallowed');
			return false;
		}
	}
	
	function edit()
	{
		if ( $char -> id != Model_Kingdom::get_king( $char -> region -> kingdom_id ) )
		{ 
			$message = 'global.operationnotallowed');
			return false;
		}		
	}

}
