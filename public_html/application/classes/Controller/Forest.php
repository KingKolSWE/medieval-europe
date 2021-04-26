<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Forest extends Controller_Template
{
	// Imposto il nome del template da usare
	

	function getwood( $structure_id, $qta = 1 )
	{
		// Carico la struttura "Foresta"
		$structure = StructureFactory_Model::create( null, $structure_id );

		// Controllo che la struttura sia effettivamente una foresta
		if ($structure->structure_type->type <> 'forest' ) 
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotvalid') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Controllo che la foresta si trovi nello stesso
		// nodo dove si trova il char
		if ($structure->region_id <>  Model_Character::get_info( Session::instance()->get('char_id') ) -> position_id)
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotinregion') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Se tutti i controlli vengono superati allora
		// inizializzo l'azione getwood
		$message = "";
		$char = ORM::factory( "character" )->find( Session::instance()->get("char_id") );
		$ca_getwood = Character_Action_Model::factory("getwood");
		if ( $ca_getwood->do_action( array( $structure, $char, $qta ),  $message ) )
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");		
		else		
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");		
		HTTP::redirect( "region/view");	
	}

	function searchplant( $structure_id, $qta = 1 )
	{
		// Carico la struttura "Foresta"
		$structure = StructureFactory_Model::create( null, $structure_id );

		// Controllo che la struttura sia effettivamente una foresta
		if ($structure->structure_type->type <> 'forest' ) 
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotvalid') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Controllo che la foresta si trovi nello stesso
		// nodo dove si trova il char
		if ($structure->region_id <>  Model_Character::get_info( Session::instance()->get('char_id') ) -> position_id)
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotinregion') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Se tutti i controlli vengono superati allora
		// inizializzo l'azione searchplant
		$message = "";
		$char = ORM::factory( "character" )->find( Session::instance()->get("char_id") );
		$ca_getwood = Character_Action_Model::factory("searchplant");
		if ( $ca_getwood->do_action( array( $structure, $char, $qta ),  $message ) )
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");		
		else		
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");		
		HTTP::redirect( "region/view");	
	}
}
