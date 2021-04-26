<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Dump extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';

	// Cerca
	function search( $structure_id ) 
	{
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = StructureFactory_Model::create( null, $structure_id );
		
		$par[0] = $char;
		$par[1] = $structure;
		$ca = Character_Action_Model::factory("searchdump");
		
		if ( $ca->do_action( $par,  $message ) )
		 	{ Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }	
		else	
			{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); }
		
		HTTP::redirect( 'region/view/' . $char -> position_id );
	}
	
}
