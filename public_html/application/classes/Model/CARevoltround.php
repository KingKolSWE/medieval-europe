<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CARevoltround extends Model_CharacterAction
{

	public function __construct()
	{		
		parent::__construct();
		// questa azione non � bloccante per altre azioni del char.
		$this->blocking_flag = false;		
		return $this;
	}
	
	protected $immediate_action = true;

	protected function check( $par, &$message )
	{ }
	
	protected function append_action( $par, &$message )
	{	}

	function complete_action( $data )
	{
		// esegue il combattimento del round.
		$battle = ORM::factory('battle', $data->param2 );
		$battle->runbattle( $data -> param1 );
		$battle->finishbattle();
		
	}
	
	public function execute_action ( $par, &$message) 
	{ }
	

		
}
