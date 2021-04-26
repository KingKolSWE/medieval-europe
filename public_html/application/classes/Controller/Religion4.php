<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Religion4 extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	
	
	/*
	* religion_4 offices
	*/
	
	function manage( $structure_id )
	{
	
		$view = View::factory ( 'religion_4/manage' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$structure = Model_StructureFactory::create( null, $structure_id );
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');		
		$religiousstructureheader = View::factory('template/religiousstructureheader');
		$form = array (
			'description' => '',
			'points' => 0, 
			'targetstructure_id' => null,
			);
		
		// controllo permessi
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
			'private', 'manage') )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}	
		
		if ( !$_POST )
			;
		else
		{
			
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			
			// trasferisci FP
			
			if ( $this -> request -> post('transfer' ) )
			{
				$ca = Model_CharacterAction::factory("transferfppoints");
				$par[0] = $character;		
				$par[1] = $structure;
				$par[2] = ORM::factory('structure', $this -> request -> post('targetstructure_id' ) );
				$par[3] = $this -> request -> post('points');
				
				if ( $ca -> do_action( $par,  $message ) )
				{ 				
					Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");					
				}	
				else	
				{ 
					$form = arr::overwrite( $form, $this -> request -> post() ); 				
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 									
				}	
			
			}
			
			// cambia descrizione
			
			if ( $this -> request -> post( 'submit_description' ) ) 
			{
				$structure -> description = substr($this -> request -> post ('description' ), 0, 1023);
				$structure -> save();
				Session::instance()->set('user_message', "<div class=\"info_msg\">" . __('structures.configuration_ok') . "</div>");
				
			}
			
			if ( $this -> request -> post( 'submit_message' ) ) 
			{
				$structure -> message = substr($this -> request -> post ('message' ), 0, 1023); 				
				$structure -> save();
				Session::instance()->set('user_message', "<div class=\"info_msg\">" . __('structures.configuration_ok') . "</div>");
			}
			
			HTTP::redirect('religion_4/manage/' . $structure -> id ) ;
		
		}
		
		// carichiamo tutte le strutture della chiesa
		// e costruiamo il dropdown
		
		$churchstructures = Model_Church::helper_allchurchstructuresdropdown( $structure->structure_type->church_id, $structure->id);
		
		$lnkmenu = $structure -> get_horizontalmenu( 'manage' );
		
		$info = Model_Church::get_info($structure -> structure_type -> church_id);
		$structureinfo = $structure -> get_info();
		$rfavailability = $structure -> get_option('rfavailability');		
		$view -> info = $info;
		$view -> structureinfo = $structureinfo;
		$view -> churchstructures = $churchstructures;
		$view -> rfavailability = $rfavailability;
		$religiousstructureheader -> info = $info;		
		$religiousstructureheader -> structure = $structure;		
		$view -> religiousstructureheader = $religiousstructureheader;
		$view -> structure = $structure;		
		$subm -> submenu = $lnkmenu;		
		$view -> submenu = $subm;						
		$view -> form = $form;
		$this -> template->sheets = $sheets;
		$this -> template->content = $view;
		
	}
	
	
	function celebratemarriage( $structure_id )
	{
		
		$view = View::factory ( 'religion_4/celebratemarriage' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		
		if (!$_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'celebratemarriage' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}	
			
			$view -> annulmentchar = '';
			$view -> celebratehusband = '';
			$view -> celebratewife = '';
		}
		else
		{
		
			
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'celebratemarriage' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			
			if ( $this -> request -> post('startmarriage' ) )
			{
				$par[0] = $character;
				$par[1] = $structure;
				$par[2] = ORM::factory('character') -> where ('name', $this -> request -> post('celebratehusband')) -> find();
				$par[3] = ORM::factory('character') -> where ('name', $this -> request -> post('celebratewife')) -> find();
				
				$ca = Model_CharacterAction::factory("celebratemarriage");
				$view -> celebratewife = $this -> request -> post('celebratewife');
				$view -> celebratehusband = $this -> request -> post('celebratehusband');
				$view -> annulmentchar = '';
			}
			elseif ( $this -> request -> post('cancelmarriage' ) )
			{
				$par[0] = $character;
				$par[1] = $structure;
				$par[2] = ORM::factory('character') -> where ('name', $this -> request -> post('annulmentchar')) -> find();				
				$ca = Model_CharacterAction::factory("cancelmarriage");
				$view -> annulmentchar = $this -> request -> post('annulmentchar');
				$view -> celebratehusband = '';
				$view -> celebratewife = '';
			}
			else
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $__('global.operation_not_allowed') . "</div>");
				HTTP::redirect ( 'religion_4/celebratemarriage/' . $structure -> id );
			}			
			
			if ( $ca -> do_action( $par,  $message ) )
			{ 				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
				//HTTP::redirect ( 'religion_4/celebratemarriage/' . $structure -> id );
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				//HTTP::redirect ( 'religion_4/celebratemarriage/' . $structure -> id );
			}
		
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'celebratemarriage';
		$view->submenu = $submenu;

		
		$view -> structure = $structure;		
		$this -> template -> sheets = $sheets;
		$this -> template -> content = $view;	
	}
	
	/**
	* Donazione denari
	*/
	
	function donatecoins( $structure_id )
	{
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );
		
		// controllo permessi
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'public', 'donatecoins' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}	
		
		$par[0] = $character;
		$par[1] = $structure;
		
		$ca = Model_CharacterAction::factory("donatecoins");
		if ( $ca -> do_action( $par,  $message ) )
		{ 				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			HTTP::redirect ( 'region/view/' );
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
			HTTP::redirect ( 'region/view/' );
		}	
	}
	
	
	/* 
	* Assegna i titoli e gli incarichi roleplay ai giocatori
	* @param   int    $structure_id    id della struttura dove avviene la nomina
	* @output  none
	*/	
	function assign_rolerp( $structure_id )
	{
		$view   = View::factory ( 'religion_4/assign_rolerp' );
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm   = View::factory ('template/submenu');
		
		// Inizializzo le form
		$formroles = array
		(
			'role'        => 'acolyte',		
			'region'      => null,
			'nominated'   => null,
			'place'       => null,
		);

		// Definisco gli incarichi roleplay assegnabili
		$roles = array
		( 
			'acolyte'   => __('global.acolyte_m')
		);

		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		if ( !$_POST ) 
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
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
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );

			$ca = Model_CharacterAction::factory("assignrolerp");
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
		
		$lnkmenu = $structure -> get_horizontalmenu ('assign_rolerp');
		$view -> structure = $structure; 
		$subm -> submenu = $lnkmenu;		
		$view -> submenu = $subm;		
		$view -> formroles = $formroles;
		$view -> roles = $roles;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
				
	}	
	
}
