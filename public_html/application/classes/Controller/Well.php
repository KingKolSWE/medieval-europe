<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Well extends Controller_Template
{
	// Imposto il nome del template da usare
	

	function collect_water( $structure_id, $qta = 1 )
	{
		// Carico la struttura "Pozzo"
		$structure = Model_StructureFactory::create( null, $structure_id );

		// Controllo che la struttura sia effettivamente un pozzo
		if ($structure->structure_type->type <> 'well_1' ) 
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotvalid') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Controllo che il pozzo si trovi nello stesso
		// nodo dove si trova il char
		if ($structure->region_id <>  Model_Character::get_info( Session::instance()->get('char_id') ) -> position_id)
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotinregion') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Se tutti i controlli vengono superati allora
		// inizializzo l'azione collectwater
		$message = "";
		$char = ORM::factory( "character" )->find( Session::instance()->get("char_id") );
		$ca_cwater = Model_CharacterAction::factory("collectwater");
		if ( $ca_cwater->do_action( array( $structure, $char, $qta ),  $message ) )
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");		
		else		
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");		
		HTTP::redirect( "region/view");	
	}

}