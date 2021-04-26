<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Trainingground extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	
	/**
	* Permette di allenarsi
	* con uno sparring partner
	* @param none
	* @return none
	*/
	
	public function trainwithsparring( $structure_id = null)
	{
		$maxrepeats = 20;
		$view = View::factory( 'trainingground/trainwithsparring');
		
		$sheets  = array('gamelayout'=>'screen', 'battlereport' => 'screen' );		
		$report = array(); 
		$weapons = array( 'a' => 'a' );
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$data = null;
				
		$form = array(
			'fighter1' => 'Guglielmo di Valenza',
			'faithlevel1' => 50,
			'debugmode' => false,			
			'fightmode1' => 'normal',
			'repeats' => 1,
			'clones1' => 1,
			'health1' => 100,
			'energy1' => 50,
			'staminaboost1' => false,
			'parry1' => 0,
			'str1' => Model_Character::get_attributelimit(),
			'dex1' => Model_Character::get_attributelimit(),
			'intel1' => Model_Character::get_attributelimit(),
			'cost1' => Model_Character::get_attributelimit(),
			'weapon1' => '',
			'armorhead1' => '',
			'armortorso1' => '',
			'armorlegs1' => '',
			'armorfeet1' => '',
			'armorshield1' => '',
			'fighter2' => 'Defender',
			'faithlevel2' => 50,			
			'fightmode2' => 'normal',
			'staminaboost2' => false,
			'parry2' => 0,
			'clones2' => 1,
			'health2' => 100,
			'energy2' => 50,
			'str2' => Model_Character::get_attributelimit(),
			'dex2' => Model_Character::get_attributelimit(),
			'intel2' => Model_Character::get_attributelimit(),
			'cost2' => Model_Character::get_attributelimit(),
			'weapon2' => '',
			'armorhead2' => '',
			'armortorso2' => '',
			'armorlegs2' => '',
			'armorfeet2' => '',
			'armorshield2' => '',
		);
		
		$armors = ORM::factory( 'cfgitem' ) -> 	where ( 'category', '=', 'armor' ) -> find_all();
		
		$listweapons = ORM::factory( 'cfgitem' )
			-> where ( 'category', 'in', array( 'weapon' ) ) ->
			order_by( 'id', 'ASC' ) -> find_all() -> select( 'id', 'name') ->as_array();
		
		$listweapons[0] = 'structures_trainingground.noweapon';
		foreach ( $listweapons as $key => &$value )		
			$listweapons[$key] = __($value);
		
		ksort($listweapons);
		
		foreach ( $armors as $armor )
		{
			$listarmors[$armor -> part][0] = __('structures_trainingground.noarmor');
			$listarmors[$armor -> part][$armor -> id] = __($armor -> name) ;							
		}				
		
		
		if ( ! $_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 'public', 'trainwithsparring' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect( 'region/view/' );
			}
		}
		else
		{	
			//var_dump($_POST);exit;
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure->allowedaccess( $character, $structure -> getParentType(), $message, 'public', 'trainwithsparring' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect( 'region/view/' );
			}
			
			$debug = ($this -> request -> post('debugmode') == 'debug');			
			
			if ( $this -> request -> post('repeats') > $maxrepeats )
			{
				Session::instance()->set( 'user_message', "<div class=\"error_msg\">Please don't use more then {$maxrepeats} repeats.</div>");
				HTTP::redirect('trainingground/trainwithsparring/' . $structure -> id );	
			}
			
			if ( $this -> request -> post('repeats') > 1  and $debug == true )
			{
				Session::instance()->set( 'user_message', "<div class=\"error_msg\">Please don't use more then one repeats when using debug mode.</div>");
				HTTP::redirect('trainingground/trainwithsparring/' . $structure -> id );			
			}		
			
			// check moneys
			
			if ( $this -> request -> post('fightd') )
			{
				if ( $this -> request -> post('debugmode') == true )
					$cost = 5;
				else
					$cost = 2;
					
				if ( $character -> get_item_quantity( 'doubloon' ) < $cost )
				{ 	
					Session::instance()->set( 'user_message', "<div class=\"error_msg\">" . __('bonus.error-notenoughdoubloons') . "</div>");				HTTP::redirect('trainingground/trainwithsparring/' . $structure -> id );
				}
				else
					$character -> modify_doubloons( -$cost, 'sparringpartner' );

			}
			
			if ( $this -> request -> post('fightsc') )
			{
				if ( $this -> request -> post('debugmode') == true )
					$cost = 15;
				else
					$cost = 6;
					
				if ( $character -> check_money( $cost ) == false )
				{ 	
					Session::instance()->set( 'user_message', "<div class=\"error_msg\">" . __('charactions.global_notenoughmoney') . "</div>");		
					HTTP::redirect('trainingground/trainwithsparring/' . $structure -> id );
				}
				else
					$character -> modify_coins( -$cost, 'sparringpartner' );

			}			
			
			$data = Model_Structure_STTrainingground1::trainwithsparring( $this -> request -> post(), $debug );
			
		}
		
				
		$form = arr::overwrite($form, $this -> request -> post()); 		
		$view -> form = $form;		
		$view -> listarmors = $listarmors;
		$view -> structure_id = $structure_id;
		$view -> listweapons = $listweapons;
		$view -> repeats = is_null($data) ? "" : $data['repeats'];
		$view -> character = $character;
		$view -> weapons = $weapons;
		$this -> template -> sheets = $sheets;
		$view -> data = $data;		
		$this -> template -> content = $view;		
		
	}
	
	/**
	* Permette di allenarsi
	* @param structure_id id struttura
	* @return none
	*/
	
	function train( $structure_id )
	{
		$view = View::factory ('structure/train');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		
		if ( !$_POST)
		{
			// carico la struttura da db dopodich� instanzio il corretto modello			
			
			$structure = Model_StructureFactory::create( null, $structure_id );
			
			
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'public', 'train' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}				
					
			$model = Model_StructureFactory::create( $structure->structure_type->type, $structure_id );
			$dummies = $structure->get_item_quantity( 'wooden_dummies' );
			
		}
		else
		{
		
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 
				'public', 'train' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$model = Model_StructureFactory::create( $structure->structure_type->type, $structure_id );
			$dummies = $structure -> get_item_quantity( 'wooden_dummies' );
			
			$o = Model_CharacterAction::factory("study");
			$par[0] = $character;
			$par[1] = $model;				
			$par[2] = $this -> request -> post('hours');			
			$par[3] = $this -> request -> post('course');
			
			$rec = $o -> do_action( $par, $message );			

			if ( $rec )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect( $structure -> getSuperType() . '/train/' . $structure -> id );
				
			}
			else
			{					
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");		
				HTTP::redirect( $structure -> getSuperType() . '/train/' . $structure -> id );
			}					
			
		}
		
		$availablecourses = $structure -> getAvailablecourses();		
		$view -> availablecourses = $availablecourses;		
		$view -> dummies = $dummies;
		$view -> structure = $structure;
		$view -> char = $character;
		$view -> appliabletax = Model_Region::get_appliable_tax(
			$structure -> region, 'valueaddedtax', 
			$character ); 
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	
	}
	
	/**
	* carica info della struttura
	*/
	
	function info( $structure_id )
	{
		HTTP::redirect( '/structure/info/' . $structure_id );
	}

	// assign_rolerp
	// ***********************************************************
	// Assegna i titoli e gli incarichi reali ai giocatori
	//
	// @param   $structure_id    id del castello
	//
	// @output  none
	// ***********************************************************
	
	function assign_rolerp( $structure_id )
	{
	
		$view   = View::factory ( 'trainingground/assign_rolerp' );
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm   = View::factory ('template/submenu');
		
		// Inizializzo le form
		$formroles = array
		( 
		'role'        => 'trainer',		
		'region'      => null,
		'nominated'   => null,
		'place'       => null,
		);

		// Definisco gli incarichi reali
		// assegnabili
		$roles = array
		( 
		'trainer'   => __('global.trainer_m')
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
				HTTP::redirect('trainingground/manage/' . $structure->id);
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				HTTP::redirect ( 'trainingground/assign_rolerp/' . $structure->id );
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
