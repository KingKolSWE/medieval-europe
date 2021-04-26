<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CARevokestructuregrant extends Model_CharacterAction
{
	protected $immediate_action = true;
	
	// check
	// @input: parametri
	//  - par[0]: oggetto struttura	
	//  - par[1]: oggetto char a cui togliere il permesso	
	//  - par[2]: profilo
	
	protected function check( $par, &$message )
	{ 
		if ( ! parent::check_( $par, $message ) )					
			return false;		
		// check input				
		
		if ( !$par[0] -> loaded or !$par[1] -> loaded )
		{ $message = kohana::lang( 'global.operation_not_allowed'); return false; }

		return true;
	}
	
	protected function append_action( $par, &$message ){	}

	public function execute_action ( $par, &$message ) 
	{
		
		Model_StructureGrant::revoke( $par[0], $par[1], null, $par[2] );
		$message = kohana::lang('ca_revokestructuregrant.grantrevoked_ok');
					
		return true;		
	}
	
}