<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_AdminMessage extends ORM
{
  protected $sorting = array('id' => 'desc');

	/** 
	* Upload the last message of the administration
	*/
	
	function get_last_message()
	{
		$cachetag =  '-global_adminmessage' ;

		kohana::log('debug', "-> Getting $cachetag from CACHE..." ); 				
		
		$message = Model_MyCache::get( $cachetag );
				
		if ( is_null( $message ) )
		{
			kohana::log('debug', "-> Getting $cachetag from DB..." ); 
			$message = ORM::factory('admin_message') -> limit ( 1 ) -> find() -> as_array();
			Model_MyCache::set( $cachetag, $message );
		}
		
		return $message;
	}
}
