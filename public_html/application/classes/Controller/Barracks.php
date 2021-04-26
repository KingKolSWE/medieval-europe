<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Barracks extends Controller_Template
{
	
	public $template = 'template/gamelayout';			
	
	/**
	* Fornisce accesso all' armeria
	* @param int $structure_id ID Struttura
	* @return none
	*/
	
	function armory( $structure_id )
	{
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory ('barracks/armory');
		$sheets  = array('gamelayout' => 'screen', 'submenu' => 'screen', 'character'=>'screen' );		
		
		if ( !$_POST ) 
		{			
			$structure = Model_StructureFactory::create( null, $structure_id );

			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'armory' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}	
		}
		else
		{
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'armory' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}	
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'armory';
		$view->submenu = $submenu;
		
		$view -> bonus = $structure -> get_premiumbonus( 'armory' );
		$items = Model_Structure::inventory( $structure -> id );
		$view -> items = $items;
		$view -> structure = $structure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;	
	
	}

	function manageprisoners( $structure_id ) 
	{
	
		$structure = Model_StructureFactory::create( null, $structure_id);
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout' => 'screen', 'submenu' => 'screen', 'character'=>'screen' );
		$view = View::factory ('barracks/manageprisoners');
		
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'manageprisoners') )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}			
		
		$prisoners = ORM::factory("character_sentence")
			-> where( array (
				'prison_id' => $structure_id,
				'imprisonment_start is not' => null,
				'status' => 'executing' ) )-> find_all();		
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'manageprisoners';
		
		$view -> submenu = $submenu;
		$view -> prisoners = $prisoners;
		$view -> structure = $structure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;	
	
	}
	
	/*
	* Libera un prigioniero
	* @param none
	* @return none
	*/
	
	function freeprisoner()
	{
				
		$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
				
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message
		, 'private', 'freeprisoner') )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		
		$ca = Model_CharacterAction::factory("freeprisoner");
		$par[0] = ORM::factory("character", Session::instance()->get('char_id')); 
		$par[1] = ORM::factory("character", $this->request->post('imprisoned_id') );
		$par[2] = $this->request->post('reason');
		$par[3] = $structure;
		$par[4] = ORM::factory("character_sentence", $this->request->post('sentence_id'));

	
		if ( $ca->do_action( $par,  $message ) )
		{ 
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); 
			HTTP::redirect( '/barracks/manageprisoners/' . $this->request->post('structure_id'));
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( '/barracks/manageprisoners/' . $this->request->post('structure_id'));
		}		
		
		return;
		
	}
	/**
	* Pulisce le prigioni
	* @param qta parametro code
	* @return none
	*/
	
	function clean($qta = 1)
	{
		$ca = Model_CharacterAction::factory("cleanprisons");
		$par[0] = Model_Character::get_info( Session::instance()->get('char_id') );
		$par[1] = ORM::factory("region", $par[0]->position_id );
		$par[2] = $qta;
		
		if ( $ca->do_action( $par,  $message ) )
		 	Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");		
		else			
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");					
		
		HTTP::redirect('region/view/');
		
	}
	
	/**
	* blocca un char per un massimo di 48 hr.
	* @param none
	* @return none
	*/
	
	public function restrain( $structure_id = null )
	{
	
		$view = View::factory ('/barracks/restrain');
		$sheets  = array('gamelayout'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		$form = array(
			'target' => '',
			'reason' => '',  			
			'hours' => '',			
			);	
				
		if  (!$_POST)
		{		
			$structure = Model_StructureFactory::create( null, $structure_id);
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'restrain') )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{
		
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));
			
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'restrain') )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$par[0] = $character;
			$par[1] = ORM::factory('character') -> where ( array( 'name' => $this->request->post('target') ) ) -> find(); 
			$par[2] = $this->request->post('hours');
			$par[3] = $this->request->post('reason');
			
			$form['target'] = $this->request->post('target');
			$form['reason'] = $this->request->post('reason');			
			$form['hours'] = $this->request->post('hours');		
			
			$ca = Model_CharacterAction::factory("restrain");
			if ( $ca->do_action( $par,  $message ) )
			{ 				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
				HTTP::redirect ( 'barracks/managerestrained/' . $structure -> id);
			}	
			else	
			{ 
				$view -> hours =  $this -> request -> post('hours');
				$view -> reason = $this -> request -> post('reason');
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			}
		
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'managerestrained';
		
		$view -> submenu = $submenu;
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;	
		$view -> structure = $structure;
		$view -> form = $form;
	}
	
	/*
	* Lista ordini di restrizione
	* @param $structure_id id struttura
	* @return none
	*/
	
	function managerestrained( $structure_id = null )
	{
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory ( '/barracks/managerestrained');		
		$sheets  = array('gamelayout' => 'screen', 'submenu' => 'screen', 'character'=>'screen' );
		
		if ( ! $_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id);
		
			// controllo permessi		
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'managerestrained') )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$db = Database::instance();
			
			$sql = "select c.name, c.id character_id, ca.* 
			from character_actions ca, characters c 
			where ca.action = 'restrain' 
			and    ca.character_id = c.id 
			and    ca.status = 'running' 
			and    ca.param1 = " . $character -> region_id ; 
								
			$rset = $db -> query( $sql ); 
			
			$view -> rset = $rset ; 
			$view -> structure = $structure; 
		
		}
		else
		{
			
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );

			// controllo permessi		
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'managerestrained') )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$par[0] = $character;
			$par[1] = ORM::factory ( 'character', $this -> request -> post('character_id' ) ); 
			$par[2] = ORM::factory ( 'character_action', $this -> request -> post( 'action_id' ) ); 
			$par[3] = $this -> request -> post( 'reason' ); 
			
			$ca = Model_CharacterAction::factory("cancelrestrain");
			
			if ( $ca->do_action( $par,  $message ) )
			{ 				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
				HTTP::redirect ( 'barracks/managerestrained/' . $structure -> id );
				return;
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");				
				HTTP::redirect ( 'barracks/managerestrained/' . $structure -> id );
				return;
			}
		}
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'managerestrained';
		$view -> submenu = $submenu;
		$this -> template -> content = $view; 
		$this -> template -> sheets = $sheets;	
	
	}	
	
	/*
	* Arresta un criminale
	* @param structure_id ID struttura
	* @return none
	*/
	
	function arrest( $criminal_id )
	{	
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$criminal = ORM::factory('character', $criminal_id ); 
		
		$par[0] = $char;
		$par[1] = $criminal;
		
		$ca = Model_CharacterAction::factory("arrest");

		if ( $ca->do_action( $par,  $message ) )
		{ 				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
			HTTP::redirect ( 'region/view' );				
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");	
			HTTP::redirect ( 'region/view');
			
		}
		
	}
	
	/**
	* Lend selected items
	* @param none
	* @return none
	**/
	
	function lend ( )
	{
	
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));
		
		// controllo permessi		
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
			'private', 'lend') )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		
		$par[0] = $character;
		$par[1] = $structure;
		$par[2] = ORM::factory('character') -> where ( 'name' , $this -> request -> post('target' ) ) -> find();
		$par[3] = $this -> request -> post( 'armoryitems' );
		
		$ca = Model_CharacterAction::factory("lendarmoryitem");

		if ( $ca->do_action( $par,  $message ) )
		{ 				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
			HTTP::redirect ( 'barracks/armory/' . $structure -> id );				
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");	
			HTTP::redirect ( 'barracks/armory/' . $structure -> id );				
			
		}
		
	}
	
	/**
	* Visualizza il report prestiti
	* @param structure_id ID struttura
	* @return none
	**/
	
	function viewlends ( $structure_id )	
	{
	
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );
		$view = View::factory ( '/barracks/viewlends');		
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout' => 'screen', 'submenu' => 'screen', 'character'=>'screen' );
		$limit = 25;
	
		// controllo permessi		
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 
		'private', 'viewlends' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		// find lent items (excluding the cloned 1 for send)
		
		$sql = 'select sl.id, c.name char_name, ci.name item_name, sl.lendtime, sl.deliverytime, sl.lender, sl.returnedtime 
		from structure_lentitems sl, characters c, structures s, items i, cfgitems ci
		where sl.structure_id = s.id
		and   sl.target_id = c.id 
		and   sl.id = i.lend_id 
		and   i.character_id != -1 
		and   i.cfgitem_id = ci.id 
		and   s.id = ' . $structure_id ; 
		
		$lends = Database::instance() -> query( $sql );
		
		$this -> pagination = new Pagination(array(
		'base_url'=>'barracks/viewlends/' . $structure -> id ,
		'uri_segment'=>'viewlends',
		'query_string' => 'page',
		'total_items'=> $lends -> count(),
		'items_per_page' => $limit ));		
	
		//var_dump( $lends ); exit; 
		
		$sql .= ' order by sl.id desc ';
		$sql .= " limit $limit offset " . $this -> pagination -> sql_offset ;
		
		$lends = Database::instance() -> query( $sql );		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'armory';
		$view->submenu = $submenu;
		$view -> bonus = $structure -> get_premiumbonus( 'armory' );	
		$view -> pagination = $this -> pagination;
		$view -> lends = $lends ;
		$view -> structure = $structure;
		$this -> template -> content = $view; 
		$this -> template -> sheets = $sheets;	
		
	}
	
	/**
	* Delegate access to armory
	* @param structure_id ID struttura
	* @return none
	**/
	
	function givearmoryaccess( $structure_id = null )
	{
	
		$character = Model_Character::get_info( Session::instance() -> get('char_id') );
		$view = View::factory ( '/barracks/givearmoryaccess');		
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout' => 'screen', 'submenu' => 'screen', 'character'=>'screen' );
		$delegated = array();
		
		if ( !$_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );

			// controllo permessi		
			
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'armory' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}			
		}
		
		else
		{
			
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));
			// controllo permessi		
			
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'armory' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			
			$target = ORM::factory('character') -> where ( 'name', $this -> request -> post( 'target' ) ) -> find();
			
			$par[0] = $character;
			$par[1] = $structure;
			$par[2] = $target;	
			
			$ca = Model_CharacterAction::factory("givearmoryaccess");

			if ( $ca -> do_action( $par,  $message ) )
			{ 				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
				HTTP::redirect ( 'barracks/givearmoryaccess/' . $structure -> id );				
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");	
				HTTP::redirect ( 'barracks/givearmoryaccess/' . $structure -> id );				
				
			}			

		}		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'armory';
		$view->submenu = $submenu;
		$view -> bonus = $structure -> get_premiumbonus( 'armory' );					
		$view -> structure = $structure;
	
		$this -> template -> content = $view; 
		$this -> template -> sheets = $sheets;	
	
	}
	
	/**
	* Delegate access to armory
	* @param structure_id ID struttura
	* @return none
	**/
	
	function revokearmoryaccess( $structure_id, $target )
	{
	
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id);
		$target = ORM::factory('character') -> where( 'name', $target ) -> find(); 
		
		// controllo permessi		
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'armory' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}			
						
		$par[0] = $structure;
		$par[1] = $target;	
		$par[2] = 'captain_assistant';
		
		$ca = Model_CharacterAction::factory("revokestructuregrant");

		if ( $ca->do_action( $par,  $message ) )
		{ 				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
			HTTP::redirect ( 'barracks/givearmoryaccess/' . $structure -> id );				
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");	
			HTTP::redirect ( 'barracks/givearmoryaccess/' . $structure -> id );				
			
		}
		
	}

	function assign_rolerp( $structure_id )
	{
	
		$view   = View::factory ( 'barracks/assign_rolerp' );
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm   = View::factory ('template/submenu');
		
		// Inizializzo le form
		$formroles = array
		( 
			'role'        => 'lieutenant',		
			'region'      => null,
			'nominated'   => null,
			'place'       => null,
			);

		$roles = array
		( 
			'lieutenant'   => __('global.lieutenant_m')
		);

		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		if ( !$_POST ) 
		{
			$structure = Model_StructureFactory::create( null, $structure_id );

			// controllo permessi		
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'assign_rolerp' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}			
		}
		else
		{				
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			
			// controllo permessi		
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
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
				HTTP::redirect('barracks/manage/' . $structure->id);
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'barracks/assign_rolerp/' . $structure->id );
			}
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'assign_rolerp';
		$view -> submenu = $submenu;
		$view -> structure = $structure; 
		$view -> formroles = $formroles;
		$view -> roles = $roles;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
				
	}	
}
