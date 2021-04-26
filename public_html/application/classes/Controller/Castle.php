<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Castle extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';	
	const TAXCHANGECOOLDOWN = 7;
	
	/**
	* Gestisce la pagina per la configurazione della valueaddedtax
	* @param int $structure_id id struttura
	* @return none
	**/
	
	public function valueaddedtax( $structure_id )
	{
		
		$view = View::factory('castle/valueaddedtax');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id);
		$this -> template -> sheets = $sheets;
		
		if ( ! $_POST )
		{			
			//controllo accesso					
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'valueaddedtax' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}								
		}
		else
		{
		
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			
			//controllo accesso			
		
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'valueaddedtax' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}								
			
			$tax = ORM::factory('region_tax', $this -> request -> post('tax_id') ); 			
			$hostile = $this -> request -> post('hostile');
			$neutral = $this -> request -> post('neutral');
			$friendly = $this -> request -> post('friendly');
			$allied = $this -> request -> post('allied');
			$citizen = $this -> request -> post('citizen');
			
			if (
				($hostile < 0 or $hostile > 100) or 
				($neutral < 0 or $neutral > 100) or 
				($friendly < 0 or $friendly > 100) or 
				($allied < 0 or $allied > 100) or
				($citizen < 0 or $citizen > 100)
			)
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('taxes.error-taxvaluesnotcorrect') . "</div>");				
			}
			elseif ( $tax -> timestamp > time() - ( self::TAXCHANGECOOLDOWN * 24 * 3600 ) )
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('taxes.error-taxchangecooldown', self::TAXCHANGECOOLDOWN) . "</div>");
			}
			else
			{
				
				$tax -> neutral = $neutral;
				$tax -> friendly = $friendly;
				$tax -> allied = $allied;
				$tax -> citizen = $citizen;
				$tax -> timestamp = time();
				$tax -> save ();
				
				
				
				Model_CharacterEvent::addrecord(
					$structure -> character_id, 
					'normal', 
					'__events.valueaddedtaxchange' . 				
					';' . $character -> name .
					';__' . $tax -> region -> name . 
					';__diplomacy.neutral' . 					
					';' . $neutral .
					';__diplomacy.friendly' . 
					';' . $friendly .
					';__diplomacy.allied' . 
					';' . $allied .
					';__diplomacy.citizen' . 
					';' . $citizen );
					
				Model_CharacterEvent::addrecord(
					null, 
					'announcement', 
					'__events.valueaddedtaxchange' . 				
					';' . $character -> name .
					';__' . $tax -> region -> name . 
					';__diplomacy.neutral' . 					
					';' . $neutral .
					';__diplomacy.friendly' . 
					';' . $friendly .
					';__diplomacy.allied' . 
					';' . $allied .
					';__diplomacy.citizen' . 
					';' . $citizen,					
					'evidence' );

					
				Session::instance()->set('user_message', "<div class=\"info_msg\">". __('taxes.info-taxesupdated') . "</div>");
			
			}
			
						
		}
		
		//////////////////////////////////////////
		// Carica le tasse per ogni regione che 
		// � controllata dal castello
		//////////////////////////////////////////
				
		$controlledregions = $character -> get_controlledregions();		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'valueaddedtax';
		$view -> submenu = $submenu;
		$view -> structure = $structure ;			
		$view -> controlledregions = $controlledregions;
		$this -> template -> content = $view;
	}
	
	/**
	* permette di revocare un ruolo
	* @param structure_id ID struttura controllante
	* @param character_id ID char che si vuole dismettere
	*/
	
	function revoke_role( $structure_id, $character_id )
	{
		
		$roleowner = ORM::factory("character", $character_id );		
		$role = $roleowner -> get_current_role();
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$ca = Model_CharacterAction::factory("revokerole");
		$structure = Model_StructureFactory::create( null, $structure_id );
		
		
		//controllo accesso			
	
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'private', 'revoke_role' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}								
		
		$par[0] = $character;
		$par[1] = $roleowner;
		$par[2] = $role -> tag;
		$par[3] = $structure;
			
		$rec = $ca->do_action( $par, $message );			
		
		if ( $rec )
		{		
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");								
			HTTP::redirect( 'castle/list_subordinates/' . $structure -> id );
		}
		else
		{					
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");				
			HTTP::redirect( 'castle/list_subordinates/' . $structure -> id );
		}			
		
		$view -> role = $role;
		$view -> roleowner = $roleowner;
		$this -> template->content = $view ;
		$this -> template->sheets = $sheets;
	}
	
	/**
	* Elenca i candidati ad un ruolo
	* @param int $structure_id struttura da dove si nomina
	* @return none
	*/
	
	function assignrole( $structure_id = null )
	{
	
		$limit = 25 ;// numero record per pagina
		$view = View::factory ( 'castle/assignrole' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$db = Database::instance();
			
		$form = array ( 
			'role' => 'judge', 
			'region' => null	,
			'nominated' => null );
		
		$roles = array ( 
			'judge' => __('global.judge'),
			'sheriff' => __('global.sheriff'),
			'towerguardian' => __('global.towerguardian'),
			'academydirector' => __('global.academydirector'),
			'drillmaster' => __('global.drillmaster') );
		
		$controlledregions = $character -> get_controlledregions();
		
		foreach ( $controlledregions as $controlledregion) 
			$controlledregions_cb[ $controlledregion -> id ] = __( $controlledregion -> name ) ; 
		
		if ( !$_POST ) 
		{
			$structure = Model_StructureFactory::create( null, $structure_id);
			
			// controllo permessi		
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'assignrole' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}			
		
		}		
		else
		{
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));
			
			// controllo permessi		
			
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
				'private', 'assignrole' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
				
			$ca = Model_CharacterAction::factory("assignrole");
			$par[0] = $character;
			$par[1] = ORM::factory('character' ) -> where ( array( 
				'name' => $this -> request -> post('nominated')) ) -> find(); 
			$par[2] = $this -> request -> post('role');
			$par[3] = ORM::factory('region',  $this -> request -> post('region') );		
			$par[4] = ORM::factory('structure', $this -> request -> post( 'structure_id' ) ); 
			
			$rc = $ca -> do_action( $par,  $message );				
			KO7::$log->add(KO7_Log::INFO, '-> assignrole ***commit***.');
			
			if ( $rc )
			{ 				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");					
				HTTP::redirect ( 'castle/assignrole/' . $structure -> id );
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'castle/assignrole/' . $structure -> id );
			}
				
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'list_subordinates';
		$view -> submenu = $submenu;	
		$view -> structure = $structure; 
		$view -> form = $form;
		$view -> region = $structure -> region ;
		$view -> roles = $roles; 
		$view -> controlledregions_cb = $controlledregions_cb;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
				
	}	
			
	/**
	* Elenca i subordinati
	* @param int $structure_id id struttura da dove si nomina
	* @return none
	*/
	
	function list_subordinates( $structure_id, $region_id = null) 
	{

		$view = View::factory ( 'castle/list_subordinates' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$structure = Model_StructureFactory::create( null, $structure_id);
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
	
		// controllo permessi				
		
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
			'private', 'list_subordinates' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}			
	
		$subordinates = null;
		
		/////////////////////////////////////////////		
		// trovo le regioni controllate dal vassallo
		/////////////////////////////////////////////
		
		$controlledregions_cb = array();
		$controlledregions = $character -> get_controlledregions();
		
		// carico la regione scelta nella combobox, o la prima.
		
		if ( is_null( $region_id ) )
			$region = ORM::factory('region',$controlledregions[0] -> id);
		else
			$region = ORM::factory("region", $region_id );
				
		foreach ( $controlledregions as $controlledregion) 
			$controlledregions_cb[ $controlledregion -> id ] = __( $controlledregion -> name ) ; 
		
		$judge = $region -> get_roledetails( 'judge');		
		if ( !is_null( $judge) )
			$subordinates['judge'] = $judge;
				
		$sheriff = $region -> get_roledetails( 'sheriff');
		if ( !is_null( $sheriff) )
			$subordinates['sheriff'] = $sheriff;
		
		$towerguardian = $region -> get_roledetails( 'towerguardian');
		if ( !is_null( $towerguardian) )
			$subordinates['towerguardian'] = $towerguardian;		
		
		$academydirector = $region->get_roledetails( 'academydirector');		
		if ( !is_null( $academydirector) )
			$subordinates['academydirector'] = $academydirector;
						
		$drillmaster = $region->get_roledetails( 'drillmaster');		
		if ( !is_null( $drillmaster) )
			$subordinates['drillmaster'] = $drillmaster;
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'list_subordinates';
		$view -> submenu = $submenu;
		$view -> region = $region; 
		$view -> controlledregions_cb = $controlledregions_cb;
		$view -> structure = $structure; 
		$view -> subordinates = $subordinates;
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;
		
	}
	
	/*
	* Report delle propriet� possedute
	* @param int $structure_id ID Struttura
	* @return none
	*/
	
	function propertyreport( $structure_id )
	{
		
		$view = View::factory('castle/propertyreport' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$structure = Model_StructureFactory::create( null, $structure_id);$structure = Model_StructureFactory::create( null, $structure_id);
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$role = $character -> get_current_role();
		
		// controllo permessi		
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
			'private', 'propertyreport' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}	

		$controlledregions = $character -> get_controlledregions();		
		foreach ( $controlledregions as $region )
		{
			$sql = "
				select c.name charname, c.id character_id, n2.name residence, k.name kingdomname, st.name structurename, n1.name regionname
				from structures s, regions n1, characters c, regions n2, kingdoms_v k, structure_types st
				where s.region_id = " . $region -> id . " 
				and s.region_id = n1.id
				and st.supertype not in ( 'royalpalace', 'castle', 'court', 'barracks', 'holysee', 'cardinalpalace', 'bishoppalace', 'cathedral')
				and s.structure_type_id = st.id
				and s.character_id = c.id
				and c.region_id = n2.id
				and n2.kingdom_id = k.id
				order by c.name, structurename";
		
			$db = Database::instance();
			$res = $db -> query( $sql ) -> as_array();
			
			$report[$region->name] = $res ;
		}
		
		// build the megaarray
		
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'propertyreport';
		$view -> submenu = $submenu;		
		$view->structure = $structure;
		$view->report = $report;
		$view->role = $role;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
}

/*
* Genera report risorse
* @param int $structure_id ID struttura castello
* @return none
*/

function basicresourcereport( $structure_id )
{
	
	$view = View::factory('castle/basicresourcereport' );
	$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
	$subm    = View::factory ('template/submenu');
	$structure = Model_StructureFactory::create( null, $structure_id);
	$character = Model_Character::get_info( Session::instance()->get('char_id') );
	$role = $character -> get_current_role();
		
	// controllo permessi		
	
	if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message,
		'private', 'basicresourcereport' ) )
	{
		Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		HTTP::redirect('region/view/');
	}	

	$controlledregions = $character -> get_controlledregions();
	
	foreach ( $controlledregions as $region )
		$regions[] = $region -> id;
	
	$where = 'in ( ' . implode ( ',', $regions ) . ' )' ;
	
	$sql = "select st.name structure_name, r.name region_name, sr.resource, sr.max, sr.current 
	from structures s, structure_types st, regions r, structure_resources sr
	where s.structure_type_id = st.id
	and   s.region_id = r.id 
	and   sr.structure_id = s.id 	
	and   s.region_id " . $where ;
	
	$db = Database::instance();
	$res = $db -> query( $sql ) -> as_array();
	
	foreach ( $res as $r )
		$report[$r->region_name][$r->structure_name][$r->resource] = round(($r->current/$r->max)*100,0);		
	
	$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
	$submenu -> id = $structure -> id;
	$submenu -> action = 'propertyreport';
	$view -> submenu = $submenu;
	$view -> structure = $structure;
	$view -> report = $report;
	$view -> role = $role;
	$this -> template->content = $view;
	$this -> template->sheets = $sheets;
	
}


	/**
	* Assegna Ruoli RP
	* @param  int $structure_id id del castello
	* @return 
	*/
	
	function assign_rolerp( $structure_id )
	{
	
		$view   = View::factory ( 'castle/assign_rolerp' );
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		
		// Inizializzo le form
		$formroles = array
		( 
		'role'        => 'prefect',		
		'region'      => null,
		'nominated'   => null,
		'place'       => null,
		);

		$formtitles = array
		( 
		'title'       => 'lord',		
		'region'      => null,
		'nominated'   => null,
		'place'       => null,
		);
		
		// Definisco gli incarichi reali
		// assegnabili
		$roles = array
		( 
		'prefect'         => __('global.prefect_m'),
		'customsofficer'  => __('global.customsofficer_m')
		);

		// Definisco i titoli reali
		$titles = array
		( 
		'lord'     => __('global.lord_m'),
		'knight'   => __('global.knight_m')
		);
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		if ( !$_POST ) 
		{
			$structure = Model_StructureFactory::create( null, $structure_id);
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
			
			$structure = Model_StructureFactory::create( null, $this->request->post('structure_id'));
			
			// controllo permessi		
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 
				'private', 'assign_rolerp' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
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
				HTTP::redirect('castle/assign_rolerp/' . $structure->id);
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'castle/assign_rolerp/' . $structure->id );
			}
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'assign_rolerp';
		$view -> submenu = $submenu;
		$view -> structure = $structure; 
		$view -> formroles = $formroles;
		$view -> formtitles = $formtitles;
		$view -> roles = $roles;
		$view -> titles = $titles;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
	
	}

}
