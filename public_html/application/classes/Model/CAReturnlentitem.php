<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CAReturnlentitem extends Model_CharacterAction
{
	protected $immediate_action = true;
	protected $lend = null;
	
	// check
	// @input: parametri
	//  - par[0]: oggetto char che torna l' item
	//  - par[1]: oggetto item
	
	protected function check( $par, &$message )
	{ 
		if ( ! parent::check_( $par, $message ) )					
			return false;		
		
		// check input		
		
		if ( !$par[0] -> loaded or !$par[1] -> loaded )
		{ $message = kohana::lang( 'global.operation_not_allowed'); return false; }
		
		// controllo se l' item � prestato
		$this -> lend = ORM::factory('structure_lentitem', $par[1] -> lend_id );
		
		//var_dump($this->lend); exit;
		
		if ( ! $this -> lend -> loaded )
		{ $message = kohana::lang( 'ca_returnlentitem.error-itemisnotlent'); return false; }				
		
		// controllo se il char � nella stessa locazione della armeria (se l' armeria non � potenziata)
		$bonus = $this -> lend -> structure -> get_premiumbonus('armory');
		if ( is_null( $bonus ) and $par[0] -> position_id != $this -> lend -> structure -> region_id )
		{ $message = kohana::lang( 'ca_returnlentitem.error-charisnotinarmoryregion'); return false; }		
		
		return true;
	}
	
	protected function append_action( $par, &$message ){	}

	public function execute_action ( $par, &$message ) 
	{
		
		
		// eventi		
		Model_StructureEvent::newadd(
			$this -> lend -> structure_id,
			'__events.structure_returnedlentarmoryitem;' . $par[0] -> name . ';__' . $par[1] -> cfgitem -> name );
		
		Model_CharacterEvent::addrecord(
			$par[0] -> id,
			'normal',
			'__events.returnedlentarmoryitem;__' . 
			$par[1] -> cfgitem -> name .			
			';__' . $this -> lend -> structure -> region -> name );
				
		$par[1] -> lend_id = null;
		$par[1] -> locked = false;
		$par[1] -> additem ( 'structure', $this -> lend -> structure_id, 1 );
		$par[1] -> removeitem( 'character', $par[0] -> id, 1 );
		
		// aggiorna il lend
		
		$this -> lend -> returnedtime = time();
		$this -> lend -> save();
				
		$message = kohana::lang('ca_returnlentitem.itemreturned_ok'); 
					
		return true;		
	}
	
}
