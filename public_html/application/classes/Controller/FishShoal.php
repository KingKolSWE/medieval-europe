<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_FishShoal extends Controller_Template
{

	function fish( $structure_id, $qty = 1)
	{
		// Carico la struttura "Salina"
		$structure = Model_StructureFactory::create( null, $structure_id );
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		// Controllo che la struttura sia effettivamente un branco di pesci
		if ($structure->structure_type->type <> 'fish_shoal' ) 
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotvalid') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Controllo che il branco di pesci si trovi nello stesso
		// nodo dove si trova il char
		if ($structure->region_id <>  Model_Character::get_info( Session::instance()->get('char_id') ) -> position_id)
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error_structurenotinregion') . "</div>");
			HTTP::redirect( "region/view/" . Session::instance()->get("char_id"));			
		}

		// Se tutti i controlli vengono superati allora
		// inizializzo l'azione fish
		
		$message = "";		
		$par[0] = $structure;
		$par[1] = $char;		
		$par[2] = $qty;
				
		$ca = Model_CharacterAction::factory("fish");
		if ( $ca->do_action( $par,  $message ) )
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");		
		else		
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");		
		HTTP::redirect( "region/view");	
	}
}
