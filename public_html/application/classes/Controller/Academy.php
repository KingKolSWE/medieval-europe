<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Academy extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	
	/**
	* Permette di studiare un argomento
	* @param int $structure_id id struttura
	* @return none
	*/
	
	function study( $structure_id )
	{
		
		$view = View::factory ('/structure/study');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'structure'=>'screen');
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		
		if ( !$_POST)
		{
			// carico la struttura da db dopodich� instanzio il corretto modello
			// (structure -> st_academy -> st_academy_level_x)
			
			$structure = Model_StructureFactory::create( null, $structure_id );
			
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'public', 'study' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}					
			
		}
		else
		{

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'public', 'study' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}	
			
			$o = Model_CharacterAction::factory("study");
			$par[0] = $character;
			$par[1] = $structure;				
			$par[2] = $this->request->post('hours');			
			$par[3] = $this->request->post('course');
			
			$rec = $o->do_action( $par, $message );			

			if ( $rec )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect('/region/view/' . $character -> position_id );
				return;
				
			}
			else
			{					
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");		
			}			
		
		}
		
		$availablecourses = $structure -> getAvailablecourses();		
		$view -> availablecourses = $availablecourses;
		$view -> structure = $structure ;
		$view -> char = $character ;			
		$view -> appliabletax = Model_Region::get_appliable_tax( $structure -> region, 'valueaddedtax', $character );
		$this -> template -> content = $view ;
		$this -> template -> sheets = $sheets;
	
	}

	/**
	* Assegna i titoli e gli incarichi reali ai giocatori
	* @param  int $structure_id id del castello
	* @return none
	*/
	
	function assign_rolerp( $structure_id = null )
	{
	
		$view   = View::factory ( 'academy/assign_rolerp' );
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm   = View::factory ('template/submenu');
		
		// Inizializzo le form
		$formroles = array
		( 
		'role'        => 'assistant',		
		'region'      => null,
		'nominated'   => null,
		'place'       => null,
		);

		// Definisco gli incarichi reali
		// assegnabili
		$roles = array
		( 
		'assistant'   => __('global.assistant_m')
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
			
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'assign_rolerp' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$ca = Model_CharacterAction::factory("assignrolerp");
			$par[0] = $character;
			$par[1] = ORM::factory( 'character' )->where( array('name' => $this->request->post('nominated')) )->find(); 
			$par[2] = $this->request->post( 'role' );
			$par[3] = ORM::factory( 'region', $this->request->post( 'region_id' ) ); 
			$par[4] = ORM::factory( 'structure', $this->request->post( 'structure_id' ) );
			$par[5] = $this->request->post( 'place' );
			
			if ( $ca->do_action( $par,  $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect('academy/manage/' . $structure->id);
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'academy/assign_rolerp/' . $structure->id );
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
