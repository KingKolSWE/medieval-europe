<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Court extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	
	/*
	 * Apre una procedura per un crimine
	 * @param int $structure_id ID struttura
	 * @return none
	*/
	
	public function opencrimeprocedure( $structure_id )	
	{
			
		$form = array(
			'target' => '',
			'summary' => '',  			
			'trialurl' => '',
		);				
		
		$view = View::factory('court/opencrimeprocedure');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');
			
		if ( ! $_POST )
		{
		
			$structure = StructureFactory_Model::create( null, $structure_id );	
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'opencrimeprocedure') )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		
		}
		else
		{					
			//var_dump($_POST); exit; 		
			
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'opencrimeprocedure') )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$ca = Character_Action_Model::factory("opencrimeprocedure");		
			$par[0] = $character;
			$par[1] = ORM::factory("character") -> 
				where ( array ( 'name' => $this -> request -> post ('target') ) ) -> find(); 
			$par[2] = $this -> request -> post('summary' );
			$par[3] = $structure;
			$par[4] = $this -> request -> post('trialurl' );
			
			$form['target'] = $this->request->post('target');
			$form['summary'] = $this->request->post('summary');			
			$form['trialurl'] = $this->request->post('trialurl');			

			if ( $ca->do_action( $par,  $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); 							
				HTTP::redirect('court/listcrimeprocedures/' . $structure -> id);
			}	
			else	
			{ 			
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			}
		
		}
				
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'opencrimeprocedure';
		$view->submenu = $submenu;
		$view -> structure = $structure;
		$view -> form = $form;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	
	}
	
	/*
	 * Elenca le procedure di incriminazione
	 * @param structure_id ID struttura
	 * @return none
	*/
	
	public function listcrimeprocedures( $structure_id )	
	{
		$limit = 10	;
		$orderby = 'p.id';
		$direction = 'desc';
		
		$view = View::factory('court/listcrimeprocedures');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = StructureFactory_Model::create( null, $structure_id );
		
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
			'private', 'listcrimeprocedure') )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		
		$db = Database::instance();
		$sql = "select c.name, p.id, p.text, p.structure_id, p.issuedate, p.trialurl, p.status 
			from characters c, character_sentences p
			where p.character_id = c.id			
			and   p.structure_id = " . $structure_id ;
		
		$crimeprocedures = $db -> query ( $sql )  -> as_array(); 
		
		$this -> pagination = new Pagination(array(
			'base_url'=>'court/listcrimeprocedures/' . $structure -> id ,
			'uri_segment'=> $structure -> id ,			
			'query_string' => 'page',
			'total_items'=> count( $crimeprocedures ),
			'items_per_page'=> $limit ));				
			
		
		$sql .= " order by $orderby $direction ";
		$sql .= " limit $limit offset " . $this->pagination->sql_offset ;
		$crimeprocedures = $db -> query ( $sql ) -> as_array(); 
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'listcrimeprocedures';
		$view->submenu = $submenu;
		$view -> pagination = $this -> pagination;		
		$view -> structure = $structure;
		$view -> crimeprocedures = $crimeprocedures;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		
	}
	
	/*
	 * Modifica una procedure di incriminazione
	 * @param int $structure_id ID struttura
	 * @param int $crimeprocedure_id ID Procedura
	 * @return none
	*/
	
	public function editcrimeprocedure( $structure_id, $crimeprocedure_id )	
	{
	
		$view = View::factory('court/editcrimeprocedure');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');
		
		$form = array(			
			'summary' => '',  			
			'trialurl' => '',
		);		
		
		if ( !$_POST )
		{
		
			$structure = StructureFactory_Model::create( null, $structure_id );
			
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'editcrimeprocedure') )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$crimeprocedure = ORM::factory('character_sentence', $crimeprocedure_id ); 		
			$form['summary'] = $crimeprocedure -> text;
			$form['trialurl'] = $crimeprocedure -> trialurl;			
			
		}
		else
		{
			
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );
			
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'editcrimeprocedure' ) )	
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$crimeprocedure = ORM::factory('character_sentence', $this -> request -> post('crimeprocedure_id')); 		
			
			if ( $crimeprocedure -> status != 'new' )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures_court.procedurenotvalid'). "</div>");
				HTTP::redirect('/court/listcrimeprocedures/' . $structure -> id ); 
			}
			
			
			$crimeprocedure -> text = $this -> request -> post('summary');
			$crimeprocedure -> trialurl = $this -> request -> post('trialurl');
			$crimeprocedure -> save();
			Session::instance()->set('user_message', "<div class=\"info_msg\">". 
				__('structures_court.info-modifiedok') . "</div>");			
			HTTP::redirect('/court/listcrimeprocedures/' . $structure_id ); 
		}
				
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'listcrimeprocedures';
		$view->submenu = $submenu;
		$target = ORM::factory('character', $crimeprocedure -> character_id ); 
		$view -> target = $target;
		$view -> structure = $structure;
		$view -> crimeprocedure = $crimeprocedure;
		$view -> form = $form;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	
	}
	
	/*
	 * Modifica una procedure di incriminazione
	 * @param int $structure_id ID struttura
	 * @oaram int $crimeprocedure_id ID procedura
	 * @return none
	*/
	
	public function cancelcrimeprocedure( $structure_id, $crimeprocedure_id )	
	{		
		
		$form = array(			
			'cancelreason' => '',  						
		);		
		
	
		$view = View::factory('court/cancelcrimeprocedure');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');
		
		if ( !$_POST )
		{
						
			$structure = StructureFactory_Model::create( null, $structure_id );
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'cancelcrimeprocedure' ) )		
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			$crimeprocedure = ORM::factory('character_sentence', $crimeprocedure_id ); 		
			$form['cancelreason'] = $crimeprocedure -> cancelreason;
			
		}
		else
		{					
			
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );

			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'cancelcrimeprocedure' ) )		
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			
			$crimeprocedure = ORM::factory('character_sentence', $this -> request -> post('crimeprocedure_id')); 		
			
			if ( $crimeprocedure -> status != 'new' )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures_court.procedurenotvalid'). "</div>");
				HTTP::redirect('/court/listcrimeprocedures/' . $structure -> id ); 
			}			
			
			$crimeprocedure -> cancelreason = $this -> request -> post('cancelreason' ) ; 
			$crimeprocedure -> status = 'canceled' ; 			
			$crimeprocedure -> save();
			Session::instance()->set('user_message', "<div class=\"info_msg\">". 
				__('structures_court.info-canceledok') . "</div>");	
			HTTP::redirect('/court/listcrimeprocedures/' . $structure_id ); 
		}
		
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'listcrimeprocedures';
		$view->submenu = $submenu;
		$target = ORM::factory('character', $crimeprocedure -> character_id ); 
		$view -> target = $target;
		$view -> form = $form;
		$view -> structure = $structure;
		$view -> crimeprocedure = $crimeprocedure;		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	
	}
	
	/*
	 * Visualizza una procedura di incriminazione
	 * @param structure_id ID struttura
	 * @oaram crimeprocedure_id ID procedura
	 * @return none
	*/
	
	function viewcrimeprocedure( $structure_id, $crimeprocedure_id )
	{
	
		$view = View::factory('court/viewcrimeprocedure');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$structure = StructureFactory_Model::create( null, $structure_id );
		$subm    = View::factory ('template/submenu');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		$crimeprocedure = ORM::factory('character_sentence', $crimeprocedure_id ); 
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'viewcrimeprocedure' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		
		if ( !$crimeprocedure -> character -> loaded )
			$criminal = ORM::factory('character', $crimeprocedure -> character_id );		
		else
			$criminal = $crimeprocedure -> character;
			
		$sheriff = ORM::factory('character', $crimeprocedure -> arrested_by );		
		if ( !$sheriff -> loaded )
			$sheriff = ORM::factory('ar_character', $crimeprocedure -> arrested_by );
		
		$lnkmenu = $structure -> get_horizontalmenu( 'listcrimeprocedures' );
		$subm -> submenu = $lnkmenu;
		$view -> submenu = $subm;	
		$view -> structure = $structure;
		$view -> sheriff = $sheriff;		
		$view -> criminal = $criminal;
		$view -> crimeprocedure = $crimeprocedure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;		
	
	}
	
	
	/*
	 * Modifica una procedure di incriminazione
	 * @param int $structure_id ID struttura
	 * @oaram int $crimeprocedure_id ID procedura
	 * @return none
	*/
	
	function writearrestwarrant( $structure_id, $crimeprocedure_id )
	{
	
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = StructureFactory_Model::create( null, $structure_id );

		
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'writearrestwarrant' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		
		$ca = Character_Action_Model::factory("writearrestwarrant");				
		$par[0] = Model_Character::get_info( Session::instance()->get('char_id') );
		$par[1] = ORM::factory('structure', $structure_id );
		$par[2] = ORM::factory('character_sentence', $crimeprocedure_id );
		
		if ( $ca -> do_action( $par,  $message ) )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); 			
			HTTP::redirect('/court/listcrimeprocedures/' . $structure_id ); 
		}	
		else
		{ 			
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('/court/listcrimeprocedures/' . $structure_id ); 
		}
	
	}	
	
	/*
	 * Imprigiona un criminale
	 * @param int $character_id id del personaggio da imprigionare
	 * @param int $crimeprocedure_id id della procedura di incriminazione
	 * @return none	 
	*/
	
	function imprison( $structure_id = null, $crimeprocedure_id = null )
	{

		$view = View::factory ('/court/imprison');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$form = array( 'hours' => '', 'prison' => '');
		$db = Database::instance();		
		$subm    = View::factory ('template/submenu');
		
		if ( !$_POST)
		{
			$structure = StructureFactory_Model::create( null, $structure_id );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'imprison' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		
			$crimeprocedure = ORM::factory('character_sentence', $crimeprocedure_id );			
		}
		else
		{			
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'imprison' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$crimeprocedure = ORM::factory('character_sentence', $this -> request -> post ('crimeprocedure_id' ));			
			$par[0] = $character;
			$par[1] = $crimeprocedure;
			$par[2] = intval($this -> request -> post('hours')); 
			$par[3] = ORM::factory('structure', $this -> request -> post('prison') ); 
			$par[4] = ORM::factory('structure', $crimeprocedure -> structure_id ); 
		
			$form['hours'] = $this -> request -> post('hours'); 
			$form['prison'] = $this -> request -> post('prison'); 
			
			$ca = Character_Action_Model::factory("imprison");		

			if ( $ca -> do_action( $par,  $message ) )
			{ 				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");				
				HTTP::redirect ( '/court/listcrimeprocedures/' . $par[4] -> id );				
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");												
			}
		
		}
		
		$prisons = $db -> query ( "
			select s.id, r.name
			from structures s, structure_types st, regions r
			where s.structure_type_id = st.id
			and   s.region_id = r.id  
			and   st.supertype = 'barracks'			
			and   s.region_id in ( select id from regions where kingdom_id = " . $character -> region -> kingdom -> id . ")" ) -> as_array();
			
		foreach ( $prisons as $prison )
			$combo_prison[ $prison -> id ] = __( $prison -> name );
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'listcrimeprocedures';
		$view->submenu = $submenu;
		$view -> combo_prisons = $combo_prison;	
		$view -> crimeprocedure = $crimeprocedure;
		$view -> structure = $structure;
		$this -> template -> sheets = $sheets;
		$this -> template -> content = $view;
		$view -> form = $form;
		
	}
	
	/**
	* assign_rolerp
	* Assegna i titoli e gli incarichi reali ai giocatori	
	* @param int $structure_id id struttura
	* @return  none
	**/
	
	function assign_rolerp( $structure_id )
	{
	
		$view   = View::factory ( 'court/assign_rolerp' );
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm   = View::factory ('template/submenu');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		// Inizializzo le form
		$formroles = array
		( 
			'role'        => 'bailiff',		
			'region'      => null,
			'nominated'   => null,
			'place'       => null,
		);

		// Definisco gli incarichi reali assegnabili
		$roles = array
		( 
			'bailiff'   => __('global.bailiff_m')
		);


		if ( !$_POST ) 
		{
			$structure = StructureFactory_Model::create( null, $structure_id );
			
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
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'assign_rolerp' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
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
				HTTP::redirect('court/assign_rolerp/' . $structure->id);
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'court/assign_rolerp/' . $structure->id );
			}
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'assign_rolerp';
		$view -> submenu = $submenu;	
		$view -> structure = $structure;
		$view -> formroles = $formroles;
		$view -> roles = $roles;
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;
				
	}	
}
