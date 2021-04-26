<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Breeding extends Controller_Template
{

	public $template = 'template/gamelayout';
	
	/**
	* Compra Animali (Allevamento
	* @return none
	*/
	
	function buyanimals( $structuretype )
	{
		
		$view = View::factory('breeding/buyanimals');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');
			
		$currentregion = ORM::factory('region', $character -> position_id );
		
		if ( !$_POST )
		{		
			// check if the region has such resource
						
			$structure = $currentregion -> get_structure( $structuretype, 'type' );
			$structureinstance = Model_StructureFactory::create( $structuretype );
			$price = $structureinstance -> getPrice( $character, $currentregion );
			
			if ( is_null( $structure ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">" . __('global.operation_not_allowed') . "</div>");
				HTTP::redirect('region/view');
			}
		
		}
		else
		{			
			
			$ca = Model_CharacterAction::factory('buyanimals');
			
			$par[0] = $character;
			$par[1] = $this -> request -> post('type'); 
				
			if ( $ca -> do_action( $par, $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
			}
			
			HTTP::redirect('region/view/');
		
		}
		
		$view -> structureinstance = $structureinstance;		
		$view -> price = $price;		
		$this -> template -> content = $view ;
		$this -> template -> sheets = $sheets; 
	
	}
	
	/**
	* Raccolta materiali
	* @param int $structure_id ID struttura
	* @return none
	*/
	
	public function gather( $structure_id )
	{
		
		$message = "";
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );
		
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'gather' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( 'region/view/' );
		}
		
		// Istanzio l'azione "gather"		
		$ca = Model_CharacterAction::factory("gather");

		$par[0] = $structure;
		$par[1] = $character;

		if ( $ca->do_action( $par,  $message ) )
		{ Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }
		else	
		{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); }
				
		HTTP::redirect('structure/manage/' . $structure -> id );
	
	}	

	/**
	* Butchering
	* @param structure_id ID struttura
	* @return none
	*/
	
	public function butcher( $structure_id )
	{
	
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );
		$message = "";
	
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'butcher' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( 'region/view/' );
		}
		
		// Istanzio l'azione "butcher"		
		$ca = Model_CharacterAction::factory("butcher");
		
		$par[0] = $structure;
		$par[1] = $character;

		// Controllo se la struttura esiste
		if ( $par[0]->loaded )
		{
			if ( $ca->do_action( $par,  $message ) )
			{ Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }
			else	
			{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); }
		}
		else
		{	Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.milking_error1') . "</div>"); } 

		HTTP::redirect('structure/manage/' . $structure -> id );

	}

	/**
	* Alimenta gli animali
	* @param structure_id ID struttura
	* @return none
	*/
	
	public function feed( $structure_id )
	{
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );
		$message = "";
		
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'feed' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( 'region/view/' );
		}
		
		// Istanzio l'azione "feed"		
		$ca = Model_CharacterAction::factory("feed");
		
		$par[0] = $structure;
		$par[1] = $character;

		if ( $ca->do_action( $par,  $message ) )
			{ Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }
		else	
			{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); }
			
		HTTP::redirect('structure/manage/' . $structure -> id );
	}
		
}
