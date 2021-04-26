<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Market extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	
	
	/**
	* Gestione azione sell
	* @param int structure_id ID struttura
	* @param string $category categoria degli oggetti da visualizzare
	* @return none
	*/
	
	public function sell( $structure_id = null, $category = 'all' )
	{
	
		$view = View::factory ('/market/sell');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'character' => 'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');

		if ( !$_POST )
		{			
			$structure = Model_StructureFactory::create( null, $structure_id );
			if ($structure -> loaded == false )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('global.operation_not_allowed') . "</div>");
				HTTP::redirect('region/view/');		
			}
			$vat = Model_Region::get_tax( $structure -> region, 'valueaddedtax' );
			//var_dump($vat); exit;
		}		
		else
		{		
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			$vat = Model_Region::get_tax( $structure -> region, 'valueaddedtax' );
			
			// creo un oggetto della classe di item scelta.
			$cfgitem = Model_Item::factory( $this -> request-> post( 'item_id' ), null );
			if ( is_null ( $cfgitem ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('global.operation_not_allowed') . "</div>");
				HTTP::redirect('region/view/');		
			}
			
			$ca = Model_CharacterAction::factory("marketsellitem");
			$par[0] = $structure;
			$par[1] = $character; 
			$par[2] = $cfgitem -> find( $this -> request -> post( 'item_id') );
			$par[3] = $this -> request -> post( 'quantity' );			
			$par[4] = $this -> request -> post( 'sellingprice' );			
			$par[5] = $vat;
			$par[6] = $this -> request -> post( 'recipient'); 
			
			if ( $ca->do_action( $par,  $message ) )
				{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }	
			else	
				{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); }
		
			$view->structure = $par[0];		
		
		}
		
		$lnkmenu = $structure -> get_horizontalmenu( 'sell' );	
		$view -> charitems = Model_Character::inventory( $character -> id );
		$view -> structure = $structure;
		$view -> valueaddedtax = $vat;
		$subm -> submenu = $lnkmenu;		
		$view -> currentcategory = $category;
		$view -> submenu = $subm;
		$view -> char_transportableweight = $character -> get_transportableweight() ; 
		$view -> ownedcoins = Model_Character::get_item_quantity_d( $character -> id, 'silvercoin' );
		$view -> character = $character;
		$this -> template -> content = $view ;
		$this -> template -> sheets = $sheets;
	}
	
	/**
	* Compra un item oppure lo ritira dal mercato.
	* @param none
	* @return none
	*/
	
	public function marketaction()
	{
		

		// creo un oggetto della classe di item scelta.
		
		$cfgitem = Model_Item::factory( $this -> request-> post( 'item_id' ), null );
		if ( is_null ( $cfgitem ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('global.operation_not_allowed') . "</div>");
			HTTP::redirect('region/view/');		
		}
		
		// buy item
		
		if  ( $this -> request -> post('buy') )
		{
			$ca = Model_CharacterAction::factory("marketbuyitem");
			
			$par[0] = ORM::factory("structure",  $this->request->post( 'structure_id' ) );
			$par[1] = Model_Character::get_info( Session::instance()->get('char_id') );
			$par[2] = $cfgitem -> find( $this -> request -> post( 'item_id') );
			$par[3] = $this -> request -> post( 'quantity' );			
		}
		
		// cancel sale
		
		if  ( $this -> request -> post('marketcancelsell') )
		{
			
			$ca = Model_CharacterAction::factory("marketcancellsell");
			
			$par[0] = ORM::factory("structure",  $this->request->post( 'structure_id' ) );
			$par[1] = Model_Character::get_info( Session::instance()->get('char_id') );
			$par[2] = $cfgitem ->find( $this->request->post( 'item_id') );
			$par[3] = $this->request->post( 'quantity' );			
		}
		
		if  ( $this -> request -> post('confiscate') )
		{
			 // Disabled
                        //Session::instance()->set('user_message', "<div class=\"error_msg\">This function is temporary disabled.</div>");
                        ////HTTP::redirect( 'market/buy/' . $this -> request -> post( 'structure_id' ));

			$ca = Model_CharacterAction::factory("confiscateitem");
			
			$char = Model_Character::get_info( Session::instance()->get('char_id') );
			$item = ORM::factory('item', $this -> request -> post('item_id'));
			$seller = ORM::factory('character', $item -> seller_id );			
			$par[0] = $char;
			$par[1] = $seller;
			$par[2] = $item;
			$par[3] = intval($this->request->post( 'quantity' ));			
			$par[4] = $this->request->post( 'confiscatereason' );	
		}		
		
		if ( $ca -> do_action( $par,  $message ) )
		 	{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }	
		else	
			{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); }
		
		HTTP::redirect( 'market/buy/' . $this -> request -> post( 'structure_id' ));
			
	}
		
	
	/**
	* Funzione che mostra gli item in vendita
	* @param int $structure_id ID struttura
	* @param string  $category categoria da visualizzare
	* @return none
	*/
	
	public function buy( $structure_id, $category = 'all' )	
	{
		
		$view = View::factory('market/buy');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'character' => 'screen');
		$subm    = View::factory ('template/submenu');		
		$result = null;
				
		$structure = Model_StructureFactory::create( 'market', $structure_id );
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$role = $character -> get_current_role(); 		
		
		// controllo permessi		
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'public' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}		
		$vat = Model_Region::get_appliable_tax( $structure -> region, 'valueaddedtax', $character );
		
		// Pulisce eventuali vendite private expired
		Model_Item::cleanupexpiredprivatesales();
		
		$message=null;
		$db = Database::instance();
		
		if ( $category == 'all' )
			$where = "parentcategory = parentcategory" ;
		else
			$where = "parentcategory = " . Database::instance() -> escape($category);
			
		$sql = 
			"select i.id item_id, ci.*, i.*, c.name seller_name
				from items i, cfgitems ci, characters c
				where i.cfgitem_id = ci.id 
				and   i.seller_id = c.id 
				and   i.equipped = 'unequipped' 
				and   i.structure_id = " . $structure -> id .  " and " .
				$where . "
				order by ci.tag asc, i.price asc, i.salepostdate asc ";
				
		$db = Database::instance();
		$items = $db -> query ( $sql );		
		$lnkmenu = $structure -> get_horizontalmenu( 'buy' );		
		
		$subm -> submenu = $lnkmenu;		
		$view -> submenu = $subm;
		$view -> role = $role; 
		$view -> structure = $structure;
		$view -> items = Model_Structure::inventory( $structure -> id, true );
		$view -> valueaddedtax = $vat;
		$view -> character = $character;		
		$view -> char_transportableweight = $character -> get_transportableweight() ; 
		$view -> currentcategory = $category;		
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;
		
	}
	
	/** 
	* Statistiche di prezzo per gli item
	* @param id id item
	* @return none
	*/
		
	
	public function stats_items( $structure_id = null, $id = null )
	{
		
		$view = View::factory( 'market/stats_items' ); 
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen' ); 
		$subm    = View::factory ('template/submenu');
		
		if ( request::is_ajax() )
		{
			KO7::$log->add(KO7_Log::DEBUG, 'Received an ajax call.'); 
			KO7::$log->add(KO7_Log::DEBUG, $this -> request -> post() ); 
			$this -> auto_render = false;
			
			$isadmin = Auth::instance()->logged_in('admin');
			$doubloons = ORM::factory('cfgitem') -> where ( 'tag', 'doubloon' ) -> find(); 
			$silvercoins = ORM::factory('cfgitem') -> where ( 'tag', 'silvercoin' ) -> find(); 

			
			if ( !$isadmin and 
			(
				$doubloons -> id == $this -> request -> post('id' ) 
				or 
				$silvercoins -> id == $this -> request -> post('id' ) 
			))
				$id = 1;
			else
				$id = $this -> request -> post( 'id' ); 
			
			$db = Database::instance();
			$data = $db -> query( "
			select ci.name, ci.marketsellable marketsellable, ci.structuresellable structuresellable, si.*
			from stats_items si, cfgitems ci
			where ci.id = si.cfgitem_id
			and   ci.id = " . $id . "
			and timestamp >= ( unix_timestamp() - ( 12 * 30 * 24 * 3600 ) ) order by timestamp asc" ) -> as_array(); 				
			
			$a['data'] = $data;
			$a['name'] = __( $data[0] -> name ); 
			$a['sellable'] = ($data[0] -> marketsellable == true or $data[0] -> structuresellable ) ? __('global.yes') : __('global.no') ; 

			kohana::log( 'debug', kohana::debug( $a ) ); 
			
			echo json_encode( $a );
		}
		else
		{
		
			$structure = Model_StructureFactory::create( null, $structure_id );
			
			$items = ORM::factory('cfgitem') -> find_all();
			$isadmin = Auth::instance()->logged_in('admin');
				
			foreach ( $items as $item )
				if ( $isadmin == false and $item -> tag == 'doubloon' )
					;
				else
					$v_items[$item->id] = __( $item->name ); 			
			
			asort( $v_items ); 
			
			$view -> items = $v_items;
			$lnkmenu = $structure -> get_horizontalmenu( 'stats_items' );
			$subm -> submenu = $lnkmenu;		
			$view -> submenu = $subm;
			$view -> structure = $structure;
			$this -> template -> sheets = $sheets;
			$this -> template -> content = $view;

		}
		

	}
	
	/**
	* carica info della struttura
	*/
	
	function info( $structure_id )
	{
		HTTP::redirect( '/structure/info/' . $structure_id );
	}
	
}
