<?php defined('SYSPATH') OR die('No direct access allowed.');
class CA_Disease_Model extends Character_Action_Model
{
	
	protected $immediate_action = true;
	protected $cancel_flag = false;
	
	
	/** 
	* Perform all checks 
	* @param: par: array of parameters
	* par[0]: char object
	* par[1]: name disease
	*/
	
	
	protected function check( $par, &$message )
	{ 
		
		if ( ! parent::check_( $par, $message ) )					
		{ return false; }
		
		/////////////////////////////////////////////////////
		// data check
		/////////////////////////////////////////////////////
		
		if ( !$par[0]->loaded )
		{ $message = kohana::lang('global.operation_not_allowed'); return FALSE; }
		
		
		return true;
	}

	protected function append_action( $par, &$message ) {}	

	public function complete_action( $data )
	{ 
		
		$char = ORM::factory('character', $data -> character_id );
		
		// instantiate the correct class
		
		$disease = "Disease_" . ucfirst( $data -> param1 ) . "_Model";
		$class = new $disease();
		$class -> apply( $char );
		
		$a = ORM::factory('character_action', $data -> id );
		$nexttime = $class -> get_nextapplytime();
		$a -> starttime = $nexttime;
		$a -> endtime = $nexttime;
		$a -> save();
		
	}
	
}
