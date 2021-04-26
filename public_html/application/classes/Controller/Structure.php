	<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Structure extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';

	/**
	* Dona oggetti alla struttura
	* @param INT $structure_id ID struttura
	* @return none
	*/

	public function donate( $structure_id = null )
	{

		$view = View::factory( 'structure/donate');
		$sheets  = array('gamelayout' => 'screen', 'submenu' => 'screen', 'character'=>'screen' );
		$message = "";
		$char = Model_Character::get_info( Session::instance()->get('char_id') );

		if ( !$_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			if ( ! $structure -> allowedaccess(
				$char, $structure -> getParenttype(), $message,
				'public', 'donate' ) )
			{
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
					HTTP::redirect('region/view/');
			}
		}
		else
		{

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure -> allowedaccess( $char, $structure -> getParenttype(), $message, 'public', 'donate' ) )
			{
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
					HTTP::redirect('region/view/');
			}
			$o = Model_CharacterAction::factory("donate");
			$par[0] = $structure;
			$par[1] = ORM::factory('item', $this->request->post('item_id') );
			$par[2] = $this -> request-> post('quantity');
			$par[3] = $char;

			$rec = $o->do_action( $par, $message );

			if ( $rec )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			}
			else
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			}

		}


		// carica tutti gli item del char
		$view->items = Model_Character::inventory( $char -> id );

		$view->structure_storableweight = $structure->get_storableweight();
					KO7::$log->add(KO7_Log::INFO, 'storable weight: ' . $view->structure_storableweight  );
		$view->char_transportableweight = $char->get_transportableweight();

		//$view->region = ORM::factory("region", $char->position_id);
		$view->structure = $structure;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
	}

	/**
  * Danneggia una struttura
	* @param structure_id id struttura
	* @param qty parametro code
	* @return none
	*/

	public function damage( $structure_id, $qty = 1)
	{

		$message = "";
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );

		$a = Model_CharacterAction::factory("damage");
		$par[0] = $structure;
		$par[1] = $char;
		$par[2] = $qty;

		$rec = $a->do_action( $par, $message );

		if ( $rec )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		}

		HTTP::redirect('region/view');

	}

	/**
  * Ripara una struttura
	* @param structure_id id struttura
	* @param qty parametro code
	* @return none
	*/

	public function repair( $structure_id, $qty = 1)
	{

		$view = View::factory ( 'structure/resign_from_role' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$role = $character->get_current_role();
		$structure = Model_StructureFactory::create( null, $structure_id );


		$a = Model_CharacterAction::factory("repair");
		$par[0] = $structure;
		$par[1] = $character;
		$par[2] = $qty;

		$rec = $a->do_action( $par, $message );

		if ( $rec )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		}

		HTTP::redirect('region/view');

	}

	/**
	* Prega
	*/

	public function pray( $structure_id, $qty = 1)
	{
		$view = View::factory( 'structure/pray');
		$sheets  = array('gamelayout' => 'screen', 'submenu' => 'screen', 'character'=>'screen' );
		$message = "";
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );

		if ( ! $structure->allowedaccess( $char, $structure -> getParenttype(), $message, 'public', null ) )
		{
 				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
    		HTTP::redirect('region/view/');
		}

		$a = Model_CharacterAction::factory("pray");
		$par[0] = $structure;
		$par[1] = $char;
		$par[2] = $qty;
		$rec = $a->do_action( $par, $message );

		if ( $rec )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		}

		HTTP::redirect( 'region/view' );


	}

	/*
	* Inventory di una struttura
	* @param int $structure_id id struttura
	* @return none
	*/

	function inventory( $structure_id )
	{

		$structure = Model_StructureFactory::create( null, $structure_id );

		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory('structure/inventory');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'structure'=>'screen');

		// controllo permessi

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'inventory' ))
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		// carico gli item della struttura e del personaggio

		$structure_items = Model_Structure::inventory( $structure -> id );
		$char_items = Model_Character::inventory( $character -> id );
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'inventory';

		$view->submenu = $submenu;
		$view->char_items = $char_items;
		$view->structure_items = $structure_items;

		$view->structure_maxweightcapacity = $structure -> getStorage();
		$view->structure_weightcapacity = $structure -> get_storableweight();

		$view -> char_maxweightcapacity = $character -> get_maxtransportableweight();
		$view -> char_transportedweight = $character -> get_transportedweight();
		$view->region = ORM::factory("region", $character->position_id);
		$view->structure = $structure;

		$this->template->content = $view;
		$this->template->sheets = $sheets;

	}

	/*
	* Gestione delle tasse legate alla struttura
	*/

	public function taxes( $structure_id )
	{

		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );
		$view = View::factory( $structure -> getSuperType() . '/taxes' );
		$subm = View::factory ('template/submenu');
		$this->template->sheets = $sheets;

		if ( ! $_POST )
		{
			//controllo accesso
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'taxes' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{

			// Scorro l'array delle tasse ed aggiorno
			// i value dentro il database

			foreach ($_POST as $tax_id => $tax_value)
			{
				if (is_int($tax_id))
				{
					// Validazione dell'input
					// Required, numerico e compreso tra 0 e 100
					$post = Validation::factory($this->request->post())
						->add_rules($tax_id,'required', 'numeric');
					if ($post->validate() AND $tax_value <= 100 AND $tax_value >= 0)
					{
						$tax = ORM::factory("tax", $tax_id);
						$tax->value = $tax_value;
						$tax->save();

					}
					else
					{
						// Se non supero la validazione e il numero non � compreso tra 0 e 100
						Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.castle_taxeserror') . "</div>");
						HTTP::redirect('structure/taxes/'.$structure->id);
					}
				}
			}

			Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.castle_taxesupdated') . "</div>");

		}


		if ( $structure -> getSuperType() == 'castle')
		{
			$taxes = $structure -> region -> get_all_taxes();
		}

		if ( $structure -> getSuperType() == 'royalpalace')
		{
			$taxes = $structure -> region -> kingdom -> get_all_taxes();
		}

		$lnkmenu = $structure -> get_horizontalmenu('taxes');

		$view->taxes = $taxes;
		$view->structure = $structure ;
		$subm->submenu = $lnkmenu;
		$view->submenu = $subm;
		$this->template->content = $view;
	}

	/*
	* permette di dismettere il ruolo
	* @param none
	* @return none
	*/

	function resign_from_role()
	{

		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$role = $character->get_current_role();

		$o = Model_CharacterAction::factory("resignfromrole");
		$par[0] = $character;
		$par[1] = $role;
		$rec = $o -> do_action( $par, $message );

		if ( $rec )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		}

		HTTP::redirect( 'character/role' );

	}

	/**
	* Prende oggetti nella struttura
	* @param none
	* @return none
	*/

	public function take()
	{

		$message = null;
		$items = null;
		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'take' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		$o = Model_CharacterAction::factory("take");
		$par[0] = $structure;
		$par[1] = Model_Item::factory( $this->request->post( 'item_id'), null ) ->find( $this->request->post( 'item_id') );
		$par[2] = $this->request->post('quantity');
		$par[3] = $character;

		$rec = $o->do_action( $par, $message );

		if ( $rec )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		}

		HTTP::redirect('structure/inventory/' . $structure -> id );

	}

	/**
	* Deposita oggetti nella struttura
	* @param none
	* @return none
	*/

	public function drop()
	{

		$message = "";
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'drop' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		$o = Model_CharacterAction::factory("drop");
		$par[0] = $structure;
		$par[1] = ORM::factory('item', $this -> request -> post('item_id'));
		$par[2] = $this -> request->post('quantity');
		$par[3] = $character;

		$rec = $o -> do_action( $par, $message );

		if ( $rec )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		}

		HTTP::redirect( 'structure/inventory/' . $structure -> id );

	}

	/**
	* Mostra agli eventi della struttura
	*/

	function events( $structure_id )
	{

		$view = View::factory( 'structure/events' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$limit = 20	;
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );

		$lnkmenu = array(
			'structure/inventory/'.$structure->id =>  __('global.inventory'),
			'structure/events/'.$structure->id => array(
				'name' => __('menu_logged.submenu_structureevents'), 'htmlparams' => array( 'class' => 'selected' )),
			'/structure/manage/' . $structure -> id => __('menu_logged.submenu_manage'));


		if ( ! $structure -> allowedaccess( $char, $structure -> getParenttype(), $message, 'private', 'events' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
    		HTTP::redirect('region/view/');
		}

		$events = ORM::factory("structure_event")->
			where( array( 'structure_id' => $structure_id ) )->find_all();

		$this->pagination = new Pagination(array(
			//'base_url'=>'structure/events' . $structure_id,
			'uri_segment'=> $structure_id,
			'style'=>"extended",
			'total_items'=>$events->count(),
			'items_per_page'=>$limit));

		$events = ORM::factory("structure_event")->
		where( array( 'structure_id' => $structure_id ) )->find_all($limit, $this->pagination->sql_offset);

		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'events';
		$view -> submenu = $submenu;
		$view->structure = $structure;
		$view->pagination = $this->pagination;
		$view->events = $events;
		$this->template->content = $view;
		$this->template->sheets = $sheets;

	}

	/**
	* Permette di riposare nella struttura
	*/

	public function rest( $structure_id = null )
	{

		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$message = "";
		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		if ( Model_Character::is_resting( $character -> id ) )
			$view = View::factory( '/structure/isresting');
		else
			$view = View::factory( '/structure/rest');

		if ( ! $_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );

			// controllo accesso
			if ( !in_array( $structure -> getSuperType(),
				array( 'nativevillage') ))
				if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'rest' ) )
				{
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
					HTTP::redirect('region/view/');
				}

		}
		// post: invoco la azione
		else
		{
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			// controllo accesso
			if ( !in_array( $structure -> getSuperType(),
				array( 'nativevillage') ))
				if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'rest' ) )
				{
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
					HTTP::redirect('region/view/');
				}
			$o = Model_CharacterAction::factory("rest");

			$par[0] = $character;
			$par[1] = $structure;
			$par[2] = false;


			$rec = $o -> do_action( $par, $message );

			if ( $rec )
			{
				HTTP::redirect( '/structure/rest/' . $this->request->post('structure_id'));
			}
			else
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __($message) . "</div>");
				HTTP::redirect( '/structure/rest/' . $this->request->post('structure_id') );
			}
		}

		if ( Model_Character::is_resting( $character -> id ) )
			$view = View::factory ( 'structure/isresting' );

		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'rest';
		$view -> submenu = $submenu;

		$view -> structure = $structure;
		$info = $character -> get_restfactor( $structure, false, false );
		$view -> info = $info ;
		$view -> character = $character;
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;

	}

	/**
	* Carica info della struttura
	* @param int $structure_id id struttura
	* @return array $info
	*/

	function info( $structure_id )
	{

		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'structure'=>'screen');
		$structure = Model_StructureFactory::create( null, $structure_id );

		if (is_null($structure))
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.generic_structurenotfound') . "</div>");
			HTTP::redirect('region/view/');
		}

		$parentstructure = ORM::factory( 'structure', $structure -> parent_structure_id );
		$region = ORM::factory('region', $structure -> region_id );
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory ( '/structure/info' );

		// controllo accesso
		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'public', 'info' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		$info = $structure -> get_info();

		$parentinfo = null;

		if ( $parentstructure -> loaded )
			$parentinfo = $parentstructure -> get_info();

		if ( is_null ($info ))
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.noinfofound') . "</div>");
			HTTP::redirect('region/view');
		}

		$view -> character = $character;
		$view -> region = $region;
		$view -> info = $info;
		$view -> structure = $structure;
		$view -> parentinfo = $parentinfo;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

	}

	/**
	* Configura il costo dell' ora di lezione
	* @param int $structure_id id struttura
	* @return none
	*/

	function sethourlycost( $structure_id )
	{

		$view = View::factory( '/structure/sethourlycost' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$form = array( 'hourlycost' => 0 );

		if ( ! $_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			$region = ORM::factory('region', $structure -> region_id );

			// controllo accesso

			if ( ! $structure->allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'sethourlycost' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			$priceperhourstat = Model_Structure::get_stat_d( $structure -> id, 'courseshourlycost');
			if ($priceperhourstat -> loaded == false )
				$form['hourlycost'] = 3;
			else
				$form['hourlycost'] = $priceperhourstat -> spare1;
		}
		else
		{

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure->allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'sethourlycost' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			$region = ORM::factory('region', $structure -> region_id );
			if ( $this -> request -> post('hourlycost' ) < 0 )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">".
				__('structures.error-hourlycostincorrect') . "</div>");
			}
			else
			{
				Model_Structure::modify_stat_d(
					$structure_id,
					'courseshourlycost',
					0,
					null,
					null,
					$this -> request -> post('hourlycost'),
					null,
					true);

				Session::instance()->set('user_message', "<div class=\"info_msg\">".
					__('structures.info-hourlycostset') . "</div>");
			}

			$form['hourlycost'] = $this -> request -> post('hourlycost' );

		}

		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'sethourlycost';
		$view->submenu = $submenu;
		$view -> form = $form;
		$view -> region = $region ;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		$view -> structure = $structure ;
	}

	/**
	* callback per controllo numero positivo
	*/

	function _checkresidentshourlycosts(Validation $array, $field)
	{
		if ( $array['residentshourlycost'] < 0)
		{ $array->add_error($field, 'hourlycostmin'); }
	}


	/**
	* callback per controllo numero positivo
	*/

	function _checkforeignershourlycosts(Validation $array, $field)
	{
		if ( $array['foreignershourlycost'] < 0)
		{ $array->add_error($field, 'hourlycostmin'); }
	}

	/*
	 * Vendita della struttura
	 * @param int $structure_id id struttura
	 * @return none
	*/

	public function sell( $structure_id = null)
	{

		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory('structure/sell');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'structure'=>'screen');

		if ( !$_POST )
		{

			if ( is_null($structure_id ) )
			{
				Session::instance()->set('user_message', "<div class='error_msg'>". __('global.operation_not_allowed') . "</div>");
				HTTP::redirect( 'region/view');
			}

			$structure = Model_StructureFactory::create( null, $structure_id);

			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'sell' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

		}
		else
		{

			if ( !$this -> request -> post('structure_id') )
			{
				Session::instance()->set('user_message', "<div class='info_msg'>". __('global.operation_not_allowed') . "</div>");
				HTTP::redirect( 'region/view');
			}

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'sell' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			$ca = Model_CharacterAction::factory('sellstructure');
			$par[0] = $structure;
			$par[1] = $character;
			$par[2] = ORM::factory('region', Model_Character::get_info( Session::instance()->get('char_id') ) -> position_id );

			if ( $ca->do_action( $par,  $message ) )
				{ Session::instance()->set('user_message', "<div class='info_msg'>". $message . "</div>"); }
			else
				{ Session::instance()->set('user_message', "<div class='error_msg'>". $message . "</div>"); }

			HTTP::redirect( 'region/view');
		}

		$sellingprice = $structure -> getSellingprice( $character, $structure -> region );

		KO7::$log->add(KO7_Log::DEBUG, "-> Original Structure Price: {$sellingprice}");



		$view -> sellingprice = $sellingprice;
		$view -> structure = $structure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

	}


	/**
	* Funzione che permette ad un giocatore di dare una mano a costruire
	* per portare la struttura al prossimo livello
	* @param int $structure_id ID Struttura che si sta upgradando.
	* @return none
	*/

	public function upgrade( $structure_id = null )
	{

		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory ( 'structure/upgrade' );

		if (!$_POST)
		{

			$structure = Model_StructureFactory::create( null, $structure_id );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'public', 'build' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			if ( $structure -> status != 'upgrading' )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('ca_upgradestructure.error-structureisnotupgrading') . "</div>");
				HTTP::redirect('region/view/');
			}

		}
		else
		{

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'public', 'build' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			$par[0] = $character;
			$par[1] = $structure;
			$par[2] = $this -> request -> post('hours');

			$ca = Model_CharacterAction::factory("upgradestructurebuild");

			if ( $ca -> do_action( $par,  $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			}
			else
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			}

		}

		$view -> workedhours = $structure -> getUpgradeworkedhours();
		$view -> upgradehourlywage = $structure -> hourlywage;
		$view -> upgradeinfo = $structure -> getUpgradeinfo();
		$view -> structure = $structure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

	}


	/**
	* Gestisci corsi
	* @param int $structure_id ID Struttura
	* @return none
	*/

	function managecourses( $structure_id = null )
	{
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory ( 'structure/managecourses' );

		if ( !$_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'managecourses' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

		}
		else
		{
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'managecourses' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			$structure -> add_course( $this -> request -> post('course'));
			Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.info-courseinstalled') . "</div>");
			HTTP::redirect('structure/managecourses/'.$structure -> id);
		}

		$availablecourses = $structure -> getAvailablecourses();
		$allcourses = $structure -> getAllCourses();
		$installablecourses = array_diff( $allcourses, $availablecourses );
		//var_dump($installablecourses);
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'managecourses';
		$view -> availablecourses = $availablecourses;
		$view -> installablecourses = $installablecourses;
		$view -> submenu = $submenu;
		$view -> structure = $structure ;
		$this -> template -> sheets = $sheets;
		$this -> template -> content = $view;

	}


	/**
	* Lancia Progetto del Regno
	* @param int $structure_id ID Struttura
	* @return none
	*/

	function buildproject( $structure_id = null )
	{
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory ( 'structure/buildproject' );

		if ( !$_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'buildproject' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id_' . $this -> request -> post( 'position' )));
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'buildproject' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			$par[0] = $character;
			$par[1] = ORM::factory('cfgkingdomproject', $this->request->post('cfgkingdomproject_id_' .
				$this -> request -> post( 'position' ) ));
			$par[2] = ORM::factory('structure_type', $this -> request -> post('structure_type_id_' .
				$this -> request -> post( 'position' ) ));
			$par[3] = $structure -> region;
			$par[4] = ORM::factory('region', $this -> request -> post('region_id_' . $this -> request -> post( 'position' ) ) );
			$par[5] = $structure;

			$ca = Model_CharacterAction::factory("startkingdomproject");

			if ( $ca -> do_action( $par,  $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect('region/view');
			}
			else
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('structure/buildproject/' . $structure -> id);
			}
		}

		$startableprojects = $structure -> get_startableprojects();
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'buildproject';
		$view -> submenu = $submenu;
		$view -> startableprojects = $startableprojects;
		$view -> structure = $structure ;
		$this -> template->sheets = $sheets;
		$this -> template->content = $view;

	}

	/**
	* Elenca i progetti in progress
	* @param int $structure_id ID Struttura
	* @return none
	* @return none
	*/

	function runningprojects( $structure_id = null )
	{
		$view = View::factory ( 'structure/runningprojects' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );

		if ( $_POST )
		{

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );

			// controllo permessi
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'runningprojects' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			$par[0] = $character;
			$par[1] = ORM::factory('kingdomproject', $this -> request -> post('kingdomproject_id'));
			$ca = Model_CharacterAction::factory("cancelkingdomproject");

			if ( $ca->do_action( $par,  $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			}
			else
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			}

		}
		else
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			// controllo permessi
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'runningprojects' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

			// trova i progetti in corso d'opera legati
			// a questa struttura


		}

		$runningprojects = $structure -> getlinkedrunningprojects();
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'buildproject';
		$view -> submenu = $submenu;
		$view -> runningprojects = $runningprojects;
		$view -> structure = $structure ;
		$this -> template->sheets = $sheets;
		$this -> template->content = $view;

	}

	/**
	* Elenca i progetti completati
	* @param int $structure_id ID Struttura
	* @return none
	*/

	function completedprojects( $structure_id = null )
	{

		$view = View::factory ( 'structure/completedprojects' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$structure = Model_StructureFactory::create( null, $structure_id );
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');

		// controllo permessi
		if ( ! $structure->allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'completedprojects' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		// trova i progetti in corso d'opera legati
		// a questa struttura

		$completedprojects = $structure -> getlinkedcompletedprojects();

		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'buildproject';
		$view->submenu = $submenu;
		$view -> completedprojects = $completedprojects;
		$view -> structure = $structure ;
		$this -> template->sheets = $sheets;
		$this -> template->content = $view;

	}

	/**
	 * Lista gli oggetti craftabili
   * @param int $structure_id id struttura
	 * @return none
	*/

	public function listcraftableitems( $structure_id )
	{

		$db = Database::instance();
		$view = View::factory('structure/listcraftableitems');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );

		if ( ! $structure -> allowedaccess( $char, $structure -> getParenttype(), $message, 'private', 'listcraftableitems' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		// Costruisce l'helper per la struttura
		$ca_craft = new Model_CharacterAction_CACraft();

		$rt = __('items.'.$ca_craft -> get_required_tool( $structure -> getSupertype(),'right_hand').'_name');
		$helper = __('structures.craft_helper', strtoupper($rt));

		$craftableitems = Model_Configuration::get_craftableitems_structuretype();

		$structurecraftableitems = $craftableitems[$structure -> getSupertype().'_'.$structure -> getCurrentLevel()];


		foreach( $structurecraftableitems as &$craftableitem )
		{

			$craftableitem['progress'] = 0;

			// Tempo di crafting originale e reale

			$craftableitem['originalcraftingtime'] =
				Model_Utility::secs2hmstostring( $craftableitem['craftingtime'] * 60, 'hours' );

			$craftaction = new Model_CharacterAction_CACraft();
			$craftaction -> set_basetime($craftableitem['craftingtime']/60);
			KO7::$log->add(KO7_Log::DEBUG, "-> computing realtime for item: {$craftableitem['destination_item_name']}");
			$craftableitem['realcraftingtime'] =
				Model_Utility::secs2hmstostring( $craftaction -> get_action_time( $char ), 'hours');

			// Energia e glut richiesta

			$data = Model_CharacterAction_CACraft::get_required_energyglut($craftableitem['craftingtime'], 1);

			$craftableitem['requiredenergy'] = $data['requiredenergy'];
			$craftableitem['requiredglut'] = $data['requiredglut'];

			KO7::$log->add(KO7_Log::DEBUG, "-> Energy: {$craftableitem['requiredenergy']}, Glut: {$craftableitem['requiredglut']}");
		}


		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'listcraftableitems';
		$view -> submenu = $submenu;

		$view -> helper = $helper;
		$view -> structure = $structure;
		$view -> char = $char;
		$view -> structurecraftableitemslist = $structurecraftableitems;
		$view -> structure_id = $structure_id;
		$view -> hasqueue = Model_Character::get_premiumbonus( $char -> id, 'workerpackage');
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

	}

	/**
	* Crafta un oggetto
	* @param structureid id struttura dove viene eseguito il craft
	* @param cfgitem_id  id oggetto da craftare
	* @param worksession n. sessioni di lavoro
	*/

	public function craft( $structure_id, $cfgitem_id, $worksessions = 1 )
	{

		$view = View::factory('structure/listcraftableitems');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );

		if ( ! $structure -> allowedaccess( $character,
			$structure -> getParenttype(), $message, 'private', 'craft' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		$ca = Model_CharacterAction::factory("craft");
		$par[0] = ORM::factory("cfgitem", $cfgitem_id );
		$par[1] = $character;
		$par[2] = $structure;
		$par[3] = $worksessions;

		if ( $ca->do_action( $par,  $message ) )
			{ Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }
		else
			{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}

		HTTP::redirect('structure/listcraftableitems/' . $structure_id );

	}

	/**
	* Pagina di gestione della struttura
	* @param int $structure_id id struttura dove viene eseguito il craft
	* @return none
	*/

	public function manage( $structure_id = null )
	{

		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$section_description = View::factory('structure/section_description');
		$section_informativemessage = View::factory('structure/section_informativemessage');
		$section_loadpicture = View::factory('structure/section_loadpicture');
		$section_transferpoints = View::factory('structure/section_transferpoints');
		$section_religiousheader = View::factory('structure/section_religiousheader');
		$section_excommunicate = View::factory('structure/section_excommunicate');
		$section_sethourlywage = View::factory('structure/section_sethourlywage');
		$section_setstructurename = View::factory('structure/section_setstructurename');

		$info = Model_Church::get_info( $character -> church_id );
		$form = array(
			'name' => '',
			'points' => 0,
			'targetstructure_id' => null,
			'character' => null,
			'reason' => null,
			'hourlywage' => 0);

		if ( !$_POST and !$_FILES)
		{

			$structure = Model_StructureFactory::create( null, $structure_id);
			if ( ! $structure->allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'manage' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect( 'region/view/' );
			}

		}
		else
		{
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure->allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'manage' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect( 'region/view/' );
			}
			if ( $this -> request -> post( 'edit_description' ) )
			{
				$structure -> description = substr($this -> request -> post ('description' ), 0, 1023);
				$structure -> save();

				Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.configuration_ok') . "</div>");
				HTTP::redirect( '/structure/manage/' . $structure -> id );
			}

			if ( $this -> request -> post('excommunicate' ) )
			{
				$ca = Model_CharacterAction::factory("excommunicateplayer");
				$par[0] = $character;
				$par[1] = ORM::factory('character' ) -> where ( 'name', $this -> request -> post( 'character' ) ) -> find();
				$par[2] = $structure;
				$par[3] = $this -> request -> post('reason');

				if ( $ca -> do_action( $par,  $message ) )
				{
					Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
					HTTP::redirect( 'structure/manage/' . $structure -> id  ) ;
				}
				else
				{
					$form = arr::overwrite( $form, $this -> request -> post() );
						Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
					HTTP::redirect( 'structure/manage/' . $structure -> id  ) ;
				}

			}

			if ( $this -> request -> post( 'setstructurename' ) )
			{
				$structure -> name = substr($this -> request -> post ('name' ), 0, 127);
				$structure -> save();
				Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.configuration_ok') . "</div>");
				HTTP::redirect( '/structure/manage/' . $structure -> id );
			}

			if ( $this -> request -> post( 'edit_informativemessage' ) )
			{
				$structure -> message = substr($this -> request -> post ('informativemessage' ), 0, 1023);
				$structure -> save();
				Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.configuration_ok') . "</div>");
				HTTP::redirect( '/structure/manage/' . $structure -> id );
			}

			if ( $this -> request -> post( 'setstructureimage' ) )
			{

				$structure -> image = $this -> request -> post( 'structureimage' );
				$structure -> save();

				Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.configuration_ok') . "</div>");
				HTTP::redirect( '/structure/manage/' . $this -> request -> post('structure_id') );

			}

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
					HTTP::redirect( 'structure/manage/' . $structure -> id  ) ;
				}
				else
				{
					$form = arr::overwrite( $form, $this -> request -> post() );
						Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
					HTTP::redirect( 'structure/manage/' . $structure -> id  ) ;
				}
			}

			if ( $this -> request -> post('sethourlywage' ) )
			{

				if ( $this -> request -> post('hourlywage') < 0 )
				{

					Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures_buildingsite.hourlywagemustbepositive') . "</div>");
					HTTP::redirect( 'structure/manage/' . $structure -> id  ) ;
				}

				$structure -> hourlywage = $this -> request -> post('hourlywage');
				$structure -> save();

				Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.configuration_ok') . "</div>");
				HTTP::redirect( '/structure/manage/' . $structure -> id );

			}

		}

		$view = View::factory( $structure -> getParenttype() . '/manage');
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'manage';
		$view -> submenu = $submenu;

		$section_excommunicate -> structure = $structure;
		$section_excommunicate -> form = $form;
		$view -> section_excommunicate = $section_excommunicate;

		$form['name'] = $structure -> name;
		$section_setstructurename -> structure = $structure;
		$section_setstructurename -> form = $form;
		$view -> section_setstructurename = $section_setstructurename;

		$form['hourlywage'] = $structure -> hourlywage;
		$section_sethourlywage -> structure = $structure;
		$section_sethourlywage -> form = $form;
		$view -> section_sethourlywage = $section_sethourlywage;

		$section_informativemessage -> structure = $structure;
		$view -> section_informativemessage = $section_informativemessage;

		$section_loadpicture -> structure = $structure;
		$view -> section_loadpicture = $section_loadpicture;

		$section_description -> structure = $structure;
		$view -> section_description = $section_description;



		if ( $structure -> structure_type -> subtype == 'church' )
		{
			$section_transferpoints -> structure =  $structure;
			$section_transferpoints -> form = $form;
			$churchstructures = Model_Church::helper_allchurchstructuresdropdown( $structure->structure_type -> church_id, $structure -> id );
			$section_transferpoints -> churchstructures = $churchstructures;
			$view ->  section_transferpoints = $section_transferpoints;
			$section_religiousheader -> info = $info;
			$section_religiousheader -> structure = $structure;
			$view -> section_religiousheader = $section_religiousheader;
		}

		$view -> structure = $structure;
		$this -> template -> content = $view ;
		$this -> template -> sheets = $sheets;

	}

	/**
	 * Lista gli oggetti vendibili nella struttura, e permette di settarne i prezzi
   * @param structure_id id struttura
	 * @return none
	*/

	public function configureitemprices( $structure_id )
	{

		$view = View::factory('structure/configureitemprices');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');
		$db = Database::instance();

		if ( !$_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
			// controllo permessi
			if ( ! $structure -> allowedaccess( $char, $structure -> getParenttype(), $message, 'private', 'configureitemprices' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{

			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure -> allowedaccess( $char, $structure -> getParenttype(), $message, 'private', 'configureitemprices' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			if ( $this -> request -> post('price') < 0 )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('items.pricelessthanzero') . "</div>");
				HTTP::redirect('structure/configureitemprices/' . $structure -> id);
			}

			$item = ORM::factory('item', $this -> request -> post('item_id'));
			$item -> price = $this -> request -> post('price');
			$item -> save();

			Session::instance()->set('user_message', "<div class=\"info_msg\">". __('items.setprice-ok') . "</div>");

			HTTP::redirect('structure/configureitemprices/' . $structure -> id );
		}

		// trovo gli item contenuti nella struttura
		// e filtro in modo che risultino solo quelli
		// che la struttura pu� craftare

		$craftableitems = Model_Configuration::get_craftableitems_structuretype();
		$structurecraftableitems = $craftableitems[$structure -> getSuperType()];

		$itemsinstructure = $structure -> get_items();
		foreach ($itemsinstructure as $key => $iteminstructure)
		{
			if (!isset($structurecraftableitems[$iteminstructure->tag]) )
				unset($itemsinstructure[$key]);
		}

		//var_dump($itemsinstructure);exit;

		$lnkmenu = $structure -> get_horizontalmenu( 'configureitem3s' );

		$subm -> submenu = $lnkmenu;
		$view -> submenu = $subm;
		$view -> sellableitems = $itemsinstructure;
		$view -> structure = $structure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

	}

	/**
	 * Lista gli oggetti comprabili dalla struttura
	 * @param structure_id id struttura
	 * @return none
	*/

	public function obs_buyitems( $structure_id )
	{

		$view = View::factory('structure/buyitems');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');
		$db = Database::instance();

		if ( !$_POST )
		{
			$structure = Model_StructureFactory::create( null, $structure_id );
		}
		else
		{
			//var_dump( $this -> request -> post() ); exit;


			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			$ca = Model_CharacterAction::factory('buyitem');
			$par[0] = ORM::factory("item", $this -> request -> post('item_id'));
			$par[1] = $char;
			$par[2] = ORM::factory('structure', $structure_id);
			$par[3] = $this -> request -> post('quantity');

			if ( $ca->do_action( $par,  $message ) )
				{ Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }
			else
				{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}

			HTTP::redirect('structure/buyitems/' . $structure_id );

		}

		if ( ! $structure -> allowedaccess( $char, $structure -> getParenttype(), $message, 'public', 'buyitems' ) )
		{
 				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
    		HTTP::redirect('region/view/');
		}

		// trovo gli item contenuti nella struttura
		// e filtro in modo che risultino solo quelli
		// che la struttura pu� craftare

		$craftableitems = Model_Configuration::get_craftableitems_structuretype();
		$structurecraftableitems = $craftableitems[$structure -> getSuperType()];

		$itemsinstructure = $structure -> get_items();
		foreach ($itemsinstructure as $key => $iteminstructure)
		{
			if (!isset($structurecraftableitems[$iteminstructure->tag]) )
				unset($itemsinstructure[$key]);
		}

		// get kingdom vat

		$vat = Model_Region::get_appliable_tax(
			$structure -> region,
			'valueaddedtax',
			$char );

		$view -> buyableitems = $itemsinstructure;
		$view -> vat = $vat;
		$view -> char = $char;
		$view -> structure = $structure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

	}


/**
* Gestisce gli accessi alla struttura
* @param int $structure_id ID struttura
* @return none
*/

function manageaccess( $structure_id = null )
{

	$view = View::factory ('/structure/manageaccess');
	$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
	$character = Model_Character::get_info( Session::instance()->get('char_id') );
	$structure = Model_StructureFactory::create( null, $structure_id);
	$grants = null;

	if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(),
		$message, 'private', 'manageaccess' ) )
	{
		Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		HTTP::redirect('region/view/');
	}

	$grants = $structure -> get_assignable_grants();

	$view -> grants = $grants;
	$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
	$submenu -> id = $structure -> id;
	$submenu -> action = 'manageaccess';
	$view -> submenu = $submenu;
	$view -> structure = $structure ;
	$this -> template -> content = $view ;
	$this -> template->sheets = $sheets;

}

/**
* Revoca i permessi su una struttura
* @param structure_id ID grant
* @param target_id ID target
* @param profile profilo da revocare
* @return none
*/

function assigngrant( )
{

	$character = Model_Character::get_info( Session::instance()->get('char_id') );
	$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );

	if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(),
		$message, 'private', 'assigngrant' ) )
	{
		Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		HTTP::redirect('region/view/');
	}

	$target = ORM::factory( 'character' ) -> where ( 'name', $this -> request -> post('character') ) -> find();
	$par[0] = $structure;
	$par[1] = $target;
	$par[2] = $this -> request -> post('grant');

	$ca = Model_CharacterAction::factory("assignstructuregrant");

	if ( $ca -> do_action( $par,  $message ) )
	{
		Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		HTTP::redirect( '/structure/manageaccess/' . $structure -> id );
	}
	else
	{
		Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		HTTP::redirect( '/structure/manageaccess/' . $structure -> id );
	}

}

/**
* Revoca i permessi su una struttura
* @param structure_id ID grant
* @param target_id ID target
* @param profile profilo da revocare
* @return none
*/

function revokegrant( $structure_id, $target_id, $profile)
{

	$character = Model_Character::get_info( Session::instance()->get('char_id') );
	$structure = Model_StructureFactory::create( null, $structure_id );

	if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'revokegrant' ) )
	{
		Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		HTTP::redirect('region/view/');
	}

	$target = ORM::factory( 'character', $target_id );

	$par[0] = $structure;
	$par[1] = $target;
	$par[2] = $profile;

	$ca = Model_CharacterAction::factory("revokestructuregrant");

	if ( $ca -> do_action( $par,  $message ) )
	{
		Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
		HTTP::redirect( '/structure/manageaccess/' . $structure -> id );
	}
	else
	{
		Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
		HTTP::redirect( '/structure/manageaccess/' . $structure -> id );
	}

}

/**
 * Permette di upgradare il livello di una struttura
 * @param structure_id id struttura
 * @return none
*/

function upgradelevel( $structure_id = null)
{

	$view = View::factory ( '/structure/upgradelevel' );
	$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
	$character = Model_Character::get_info( Session::instance()->get('char_id') );

	if ( ! $_POST )
	{
		$structure = Model_StructureFactory::create( null, $structure_id);

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'upgradelevel' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

	}
	else
	{

		$structure = Model_StructureFactory::create( null, $this->request->post('structure_id'));
		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'upgradelevel' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		$message = "";
		$ca = Model_CharacterAction::factory("upgradestructurelevel");
		$par[0] = $character;
		$par[1] = $structure;

		if ( $ca->do_action( $par,  $message ) )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			HTTP::redirect( '/structure/upgradelevel/' . $structure -> id );
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( '/structure/upgradelevel/' . $structure -> id );
		}

	}


	$upgradeinfo = $structure -> getUpgradeInfo();

	$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
	$submenu -> id = $structure -> id;
	$submenu -> action = 'upgradelevel';
	$view -> submenu = $submenu;

	$view -> upgradeinfo = $upgradeinfo;


	$view -> structure= $structure;
	$this -> template -> content = $view ;
	$this -> template -> sheets = $sheets;

}

/**
 * Permette di upgradare il magazzino
 * @param int $structure_id id struttura
 * @return none
*/

function upgradeinventory( $structure_id = null)
{

	$view = View::factory ( '/structure/upgradeinventory');
	$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
	$character = Model_Character::get_info( Session::instance()->get('char_id') );

	if ( ! $_POST )
	{
		$structure = Model_StructureFactory::create( null, $structure_id);

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'upgradeinventory' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

	}
	else
	{
		$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));
		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', 'upgradeinventory' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		$message = "";
		$ca = Model_CharacterAction::factory("upgradestructureinventory");
		$par[0] = $structure;
		$par[1] = $character;

		if ( $ca->do_action( $par,  $message ) )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			HTTP::redirect( '/structure/upgradeinventory/' . $structure -> id );
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( '/structure/upgradeinventory/' . $structure -> id );
		}
	}

	$inventoryupgradeworkerhours = Model_Structure::get_stat_d( $structure -> id, 'inventoryupgradeworkerhours');
	$view -> inventoryupgradeworkerhours = is_null ( $inventoryupgradeworkerhours ) ? 0 : $inventoryupgradeworkerhours -> value;
	$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
	$submenu -> id = $structure -> id;
	$submenu -> action = 'upgradeinventory';
	$view -> submenu = $submenu;
	$view -> structure = $structure ;
	$this -> template -> content = $view ;
	$this -> template->sheets = $sheets;

}


	/**
	 * Permette di cambiare la descrizione della struttura
     * @param structure_id id struttura
	 * @return none
	*/

	function setDescription()
	{

		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id'));

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype() , $message, 'private', 'manage' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( 'region/view/' );
		}

		if ( strlen( $this -> request -> post ('description') ) > 1024 )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('structures.error-descriptiontoolong', 1024) . "</div>");
			HTTP::redirect( 'structure/manage/' . $structure -> id );
		}
		else
		{
			$structure -> description = $this -> request -> post ('description' );
			$structure -> save();

			Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.configuration_ok') . "</div>");
			HTTP::redirect( $structure -> getParenttype() . '/manage/' . $structure -> id );
		}

	}

	/**
	* Permette di cambiare il messaggio informativo
	* @param structure_id id struttura
	* @return none
	*/

	function change_infomessage ( )
	{

		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
		$subm    = View::factory ('template/submenu');
		$lnkmenu = $structure -> get_horizontalmenu( 'manage' );
		$view = View::factory( $structure -> getParenttype() . '/manage');
		$structureheader = View::factory('template/structureheader');

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype() , $message, 'private', 'change_infomessage' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( 'region/view/' );
		}

		$structure -> message = substr($this -> request -> post ('message' ), 0, 1023);
		$structure -> save();

		Session::instance()->set('user_message', "<div class=\"info_msg\">". __('structures.configuration_ok') . "</div>");

		$structureheader -> structure = $structure;
		$view -> structureheader = $structureheader;
		$subm -> submenu = $lnkmenu;
		$view -> submenu = $subm;
		$view -> structure = $structure;
		$this -> template -> content = $view ;
		$this -> template -> sheets = $sheets;

	}

	/**
	 * Elenca i titoli rp assegnati
	 * @param int $structure_id id struttura
	 * @return none
	*/

	function list_roletitles( $structure_id )
	{
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = Model_StructureFactory::create( null, $structure_id );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$view = View::factory( '/structure/list_roletitles');

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(),
			$message, 'private', 'list_roletitles' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		$allroles = ORM::factory('character_role') ->
			where(
				array(
					'structure_id' => $structure_id,
					'current' => true,
					'gdr' => true
					))->find_all();

		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'assign_rolerp';
		$view -> submenu = $submenu;
		$view -> structure = $structure;
		$view -> roles = $allroles;
		$this -> template -> content = $view ;
		$this -> template -> sheets = $sheets;
	}

	/**
	 * Revoca un titolo RP o un incarico Reale
	 * @param structure_id id struttura
	 * @param role_id id ruolo
	 * @return none
	*/

	function revokerolerp( $structure_id, $role_id )
	{
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$role = ORM::factory("character_role", $role_id );
		$structure = Model_StructureFactory::create( null, $structure_id );

		if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(),
			$message, 'private', 'revokerolerp' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}

		$ca = Model_CharacterAction::factory("revokerolerp");

		$par[0] = $role;
		$par[1] = $structure;

		$rec = $ca -> do_action( $par, $message );

		if ( $rec )
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			HTTP::redirect( 'structure/list_roletitles/' . $structure_id );
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( 'structure/list_roletitles/' . $structure_id );
		}

	}

	/**
	* Compra una struttura dal governo
	* @param str $structure_type Tipo di struttura
	* @return none
	*/

	public function buy( $structure_type )
	{

		$character = Model_Character::get_info( Session::instance() -> get('char_id') );
		$ca = Model_CharacterAction::factory('buystructure');

		$par[0] = $structure_type;
		$par[1] = $character;
		$par[2] = ORM::factory('region', $character -> position_id );

		if ( $ca -> do_action( $par,  $message ) )
		 	{ Session::instance()->set('user_message', "<div class='info_msg'>". $message . "</div>"); }
		else
			{ Session::instance()->set('user_message', "<div class='error_msg'>". $message . "</div>"); }

		HTTP::redirect('region/view/');

	}

	/**
	* Trasferisce in massa gli item da una directory personale ad una struttura e viceversa
	* @param none
	* @return none
	*/

	public function massitemtransfer( )
	{

		$character = Model_Character::get_info( Session::instance() -> get('char_id') );
		$post = json_decode($this -> request -> post('itemstotransfer'), false);

		KO7::$log->add(KO7_Log::DEBUG, ' -> Mass Depositing item Action is: ' . $post -> action );
		KO7::$log->add(KO7_Log::DEBUG, kohana::debug($post));
		//KO7::$log->add(KO7_Log::DEBUG, $post->structureid[0]);

		$structure = Model_StructureFactory::create( null, $post->structureid[0] );

		// se non ci sono elementi, torno senza fare niente.

		if ( count( $post -> items ) == 0 )
			HTTP::redirect('/structure/inventory/' . $structure -> id );

		// per le azioni drop e take, il char deve avere il permesso.

		if ( $post -> action == 'drop' or $post -> action == 'take' )
			if ( ! $structure -> allowedaccess( $character, $structure -> getParenttype(), $message, 'private', $post -> action ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}

		// Calcolo peso totale. Non calcoliamo il peso
		// del cart SOLO in caso di take.

		$totalweight = 0;

		foreach ( $post -> items as $itemtodeposit )
		{
			if (  !( $post -> action == 'take' and $itemtodeposit -> subcategory == 'cart' ) )
			{
				$itemweight = ( $itemtodeposit -> weight * $itemtodeposit -> quantity );
				KO7::$log->add(KO7_Log::DEBUG, 'Item: ' . $itemtodeposit -> subcategory . ', Itemweight: ' . $itemweight );
				$totalweight += $itemweight;
			}
		}

		//KO7::$log->add(KO7_Log::DEBUG, $totalweight );

		if ( $post -> action == 'drop' or $post -> action == 'donate' )
		{

			$storableweight = $structure -> get_storableweight( );
			if ( $totalweight > $storableweight )
			{
				KO7::$log->add(KO7_Log::INFO, 'Total weight, Storable Wright: ' .
					$totalweight .'-' . $storableweight );
				Session::instance()->set('user_message', "<div class='error_msg'>". __('charactions.drop_storablecapacityfinished'). "</div>");
				if ( $post -> action == 'donate' )
					HTTP::redirect('/structure/donate/' . $structure -> id );
				else
					HTTP::redirect('/structure/inventory/' . $structure -> id );

			}

			$o = Model_CharacterAction::factory( $post -> action );
			foreach ( $post -> items as $itemtodeposit )
			{

				KO7::$log->add(KO7_Log::DEBUG, ' -> Mass Depositing item: ' . $itemtodeposit -> id );

				$item = ORM::factory( 'item', $itemtodeposit -> id );

				$par[0] = $structure;
				$par[1] = $item;
				$par[2] = $itemtodeposit -> quantity;
				$par[3] = $character;

				$rc = $o -> do_action( $par, $message );

				if ( $rc == false )
					break;

			}
		}

		if ( $post -> action == 'take' )
		{

			$storableweight = $character -> get_transportableweight();
			if ( $totalweight > $storableweight )
			{
				//KO7::$log->add(KO7_Log::INFO, $totalweight .'-' . $storableweight );
				Session::instance()->set('user_message', "<div class='error_msg'>". __('structures.maxtransportableweightreached'). "</div>");
				if ( $post -> action == 'donate' )
					HTTP::redirect('/structure/donate/' . $structure -> id );
				else
					HTTP::redirect('/structure/inventory/' . $structure -> id );

			}

			$o = Model_CharacterAction::factory( $post -> action );

			KO7::$log->add(KO7_Log::DEBUG, kohana::debug($post -> items));

			foreach ( $post -> items as $itemtowithdraw )
			{
				//KO7::$log->add(KO7_Log::INFO, ' -> Mass Withdrawing item: ' . $itemtowithdraw -> cfgitem -> tag );

				$item = ORM::factory( 'item', $itemtowithdraw -> id );

				$par[0] = $structure;
				$par[1] = $item;
				$par[2] = $itemtowithdraw -> quantity;
				$par[3] = $character;

				$rc = $o -> do_action( $par, $message );

				if ( $rc == false )
					break;

			}
		}

		if ( $rc == false )
		{
			$m = __('structures.error-notallitemstransfered',
				__($item -> cfgitem -> name)
			) .
				$message;
		 	Session::instance()->set('user_message', "<div class='error_msg'>". $m . "</div>");
		}
		else
			Session::instance()->set('user_message', "<div class='info_msg'>". $message . "</div>");


		if ( $post -> action == 'donate' )
			HTTP::redirect('/structure/donate/' . $structure -> id );
		else
			HTTP::redirect('/structure/inventory/' . $structure -> id );

	}

}

?>
