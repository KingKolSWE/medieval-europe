<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CABattleround extends Model_CharacterAction
{

	public function __construct()
	{		
		parent::__construct();
		// questa azione non � bloccante per altre azioni del char.
		$this -> blocking_flag = false;		
		return $this;
	}
	
	protected $immediate_action = true;

	protected function check( $par, &$message )
	{ }
	
	protected function append_action( $par, &$message )
	{	}

	function complete_action( $data )
	{
		
		// carica la battaglia relativa al round.
		
		$battle = ORM::factory('battle', $data -> param2 );		
		$battlereport = '';
		$battletype = Model_battletypefactoryBattle::create( $battle -> type );
		
		$par[0] = $battle;
		$par[1] = $data;
		$battletype -> run ( $par, $battlereport );
		
	}
	
	public function execute_action ( $par, &$message) 
	{ }

		
}
