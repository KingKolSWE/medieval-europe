<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CAAssignstructuregrant extends Model_CharacterAction
{
	protected $immediate_action = true;
	
	// check
	// @input: parametri
	//  - par[0]: oggetto struttura	
	//  - par[1]: oggetto char a cui assegnare il permesso	
	//  - par[2]: profilo
	
	protected function check( $par, &$message )
	{ 
		if ( ! parent::check_( $par, $message , null, $par[1] -> id) )					
			return false;		

		// Check dati
		if ( $par[1] -> loaded == false )
		{ $message = kohana::lang( 'global.operation_not_allowed'); return false; }
		
		// verifica se il char ha già il permesso
		if ( Model_StructureGrant::get_chargrant( $par[0], $par[1], $par[2] ) == true )
		{ $message = kohana::lang( 'ca_assignstructuregrant.error-grantsalreadyassigned'); return false; }
		
		// Non � possibile assergnare grant a s� stessi
		if ( $par[1] -> id == $par[0] -> character_id )
		{ $message = kohana::lang( 'ca_assignstructuregrant.error-cantgranttoself'); return false; }	
						
		if ( in_array( $par[2], array( 'guard_assistant' ) ) and
			Model_StructureGrant::get_charswithprofile( $par[0], $par[2] ) >= 5 )
		{ $message = kohana::lang( 'ca_assignstructuregrant.error-grantlimitreached'); return false; }	
		
		// se la struttura � di tipo government, il char target deve essere del regno		
		if ( 
			$par[0] -> structure_type -> subtype == 'government' and 
			$par[1] -> region -> kingdom_id != $par[0] -> region -> kingdom_id )
		{ $message = kohana::lang( 'ca_assignstructuregrant.error-charisnotofthesamekingdom', $par[1] -> name); return false; }	
		
		
		return true;
	}
	
	protected function append_action( $par, &$message ){	}

	public function execute_action ( $par, &$message ) 
	{
	
		if ( in_array( $par[2], array (
			'captain_assistant',
			'guard_assistant',
			'chancellor' ) ) )
			Model_StructureGrant::add_model( $par[0], $par[1], null, $par[2], (time() + 3 * 365 * 24 * 3600) );
		else
			Model_StructureGrant::add_model( $par[0], $par[1], null, $par[2], (time() + 7 * 24 * 3600) );
		
		$message = kohana::lang('ca_assignstructuregrant.grantassigned_ok');					
		return true;		
	}
	
}
