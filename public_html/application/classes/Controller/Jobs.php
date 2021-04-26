<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Jobs extends Controller_Template
{
	
	public $template = 'template/gamelayout';
	
	/**
	* Cancella un job
	* @param job_id ID job da cancellare
	* @return none
	*/
	
	function delete( $job_id )
	{
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$job = ORM::factory( 'job', $job_id );
		
		$rc = Model_Job::delete( $char, $job, $message );
		
		if ( $rc )
		{				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");	
			HTTP::redirect( '/character/myjobs'); 
		}
		else		
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( '/character/myjobs'); 
		}
	}
	
	/**
	* Chiude un job
	* @param job_id ID job da cancellare
	* @return none
	*/
	
	function close( $job_id )
	{
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$job = ORM::factory( 'job', $job_id );
		
		$rc = Model_Job::close( $char, $job, $message );
		
		if ( $rc )
		{				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");	
			HTTP::redirect( '/character/myjobs'); 
		}
		else		
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( '/character/myjobs'); 
		}
	}
	
	/**
	* ripubblica un job
	* @param job_id ID job da cancellare
	* @return none
	*/
	
	function republish( $job_id )
	{
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$job = ORM::factory( 'job', $job_id );
		
		$rc = Model_Job::republish( $char, $job, $message );
		
		if ( $rc )
		{				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");	
			HTTP::redirect( '/character/myjobs'); 
		}
		else		
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( '/character/myjobs'); 
		}
	}

	/**
	* accetta un job
	* @param message_id ID job
	* @return none
	*/
	
	function accept( $message_id )
	{
	
		$announce = ORM::factory('boardmessage', $message_id );
		$employee = Model_Character::get_info( Session::instance()->get('char_id') );
		$employer = ORM::factory('character', $announce -> character_id );		
		$structure = is_null( $announce -> spare3 ) ? null : ORM::factory('structure', $announce -> spare3 );
		
		$rc = Model_Job::accept( $employee, $employer, $announce, $structure, $message );
		
		if ( $rc )
		{				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");	
			HTTP::redirect( '/character/myjobs'); 
		}
		else		
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( '/character/myjobs'); 
		}
	}
	
}