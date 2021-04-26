<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Religion3 extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';	
	const MIN_ATHEISTPARAMETER = 1.5;
	const MAX_ATHEISTPARAMETER = 2;		
	
	/**
	* Gestisce la gerarchia
	* @param: structure_id Id struttura
	* @return: none
	*/
	
	function obs_managehierarchy( $structure_id )
	{
		$view = View::factory ( 'religion_3/managehierarchy' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');		
		
		if ( !$_POST )
		{
			$structure = StructureFactory_Model::create( null, $structure_id );

			// controllo permessi
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'managehierarchy' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );
			
			if ( $this -> request -> post('revoke') )
				$ca = Character_Action_Model::factory("revokerole");		
			else
				$ca = Character_Action_Model::factory("assignrole");		
			
			$childstructure = ORM::factory('structure', $this -> request -> post('childstructure_id'));
			$par[0] = $character;
			$par[1] = ORM::factory('character') -> where ( 'name' , $this -> request -> post('owner')) -> find(); 
			$par[2] = 'church_level_4';
			
			if ( $this -> request -> post('revoke') )
			{
				$ca = Character_Action_Model::factory("revokerole");		
				$par[3] = $structure;
				$par[4] = null;
			}
			else
			{
				$ca = Character_Action_Model::factory("assignrole");		
				$par[3] = $childstructure -> region;
				$par[4] = $structure;
			}			
			
		
			if ( $ca -> do_action( $par,  $message ) )
			{ 				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect ( 'religion_3/managehierarchy/' . $structure -> id );
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'religion_3/managehierarchy/' . $structure -> id );
			}	
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'armory';
		$view->submenu = $submenu;

		
		// carica le sottostrutture
		$childstructures = $structure -> get_childstructures();
		
		$view -> childstructures = $childstructures;		
		$view -> structure = $structure;		
		
		$this -> template->sheets = $sheets;
		$this -> template->content = $view;
	}

	/* 
	* Assegna i titoli e gli incarichi roleplay ai giocatori
	* @param   int    $structure_id    id della struttura dove avviene la nomina
	* @output  none
	*/	
	
	function assign_rolerp( $structure_id )
	{
		$view   = View::factory ( 'religion_3/assign_rolerp' );
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm   = View::factory ('template/submenu');
		
		// Inizializzo le form
		$formroles = array
		(
			'role'        => 'monk',		
			'region'      => null,
			'nominated'   => null,
			'place'       => null,
		);

		// Definisco gli incarichi roleplay assegnabili
		$roles = array
		( 
			'monk'   => __('global.monk_m')
		);

		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		if ( !$_POST ) 
		{
			$structure = StructureFactory_Model::create( null, $structure_id );
			// controllo permessi		
			
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'assign_rolerp' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{	
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );

			$ca = Character_Action_Model::factory("assignrolerp");		
			//var_dump( $_POST ); exit;
			// Characther che nomina
			$par[0] = $character;
			// Character nominato
			$par[1] = ORM::factory( 'character' )->where( array('name' => $this->request->post('nominated')) )->find(); 
			// Tag ruolo
			$par[2] = $this->request->post( 'role' );
			// Regione dove avviene la nomina
			$par[3] = ORM::factory( 'region', $this->request->post( 'region_id' ) ); 
			// Struttura da dove avviene la nomina
			$par[4] = ORM::factory( 'structure', $this->request->post( 'structure_id' ) );
			// Nome del feudo da associare al titolo
			$par[5] = $this->request->post( 'place' );
			
			if ( $ca->do_action( $par,  $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect('religion_3/manage/' . $structure->id);
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'religion_3/assign_rolerp/' . $structure->id );
			}
		}

		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'assign_rolerp';
		$view->submenu = $submenu;

		$view -> structure = $structure; 
		
		$view -> formroles = $formroles;
		$view -> roles = $roles;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
				
	}	
	
}
