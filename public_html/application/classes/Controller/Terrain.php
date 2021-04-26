<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Terrain extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';

	public function index()
	{
		HTTP::redirect('region/view');
	}

	/**
	* Compra un terreno
	* @param none
	* @return none
	*/
	
	public function buy()
	{
		
		
		$view = View::factory ('terrain/buy');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$region = ORM::factory('region', $character -> position_id ); 
		
		$structureinstance = StructureFactory_Model::create('terrain_1');		
		$view -> price = $structureinstance -> getPrice( $character, $region);
				
		$terrains_info = Region_Model::get_terrains_info( $region );

		$view -> region = $region;
		$view -> terrains_info = $terrains_info;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	
	}		

	/**
	* Permette di gestire la struttura
	* @param structure_id ID struttura
	* @return none
	*/
	
	public function manage( $structure_id ) 
	{
		
		$view = View::factory('terrain/manage');
		$section_description = View::factory('structure/section_description');		
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = StructureFactory_Model::create( null, $structure_id);
		$info = '';
		
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType() , $message, 'private', 'manage' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		
		if ( $structure -> attribute1 == 0 )
			$info = __( 'structures_terrain.terrainisuncultivated');
		
		if ( $structure -> attribute1 == 1 )
		{
			$growaction = ORM::factory('character_action') ->
				where ( array( 
					'action' => 'growfield', 
					'param1' => $structure -> id,
					'status' => 'running' ) ) -> find();
					
			$info = __( 'structures_terrain.terrainisgrowing', 	Model_Utility::countdown($growaction -> endtime ) );
		}
		
		if ( $structure -> attribute1 == 2 )
		{
			$info = __( 'structures_terrain.terrainisripe' );
		}
		
		$view -> info = $info;
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'manage';
				
		$section_description -> structure = $structure;
		$view -> section_description = $section_description;
		$view -> submenu = $submenu;
		$view -> structure = $structure ;
		$this -> template->content = $view ;
		$this -> template->sheets = $sheets; 
	
	}

	/**
	* Funzione che mi genera una vista intermedia per la selezione dell'item
	* da coltivare nel terreno
	* @param: terrain_id: id del terreno, viene usato per costruire i link azione del char
	* @return none
	*/
	
	public function seed( $structure_id = null  )
	{
		
		$view = View::factory('terrain/seed');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		
		if ( !$_POST )
		{
			$structure = StructureFactory_Model::create( null, $structure_id);

			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType() , $message, 'private', 'seed' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{
			$structure = StructureFactory_Model::create( null, $this -> request -> post( 'structure_id' ) ); 

			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType() , $message, 'private', 'seed' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$ca = Character_Action_Model::factory("seed");
			
			$par[0] = $structure;
			$par[1] = ORM::factory( "item", $this -> request -> post('item_id' )); 
			$par[2] = $character;
			
			if ( $ca->do_action( $par,  $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect('/terrain/seed/' . $structure -> id );
			}
			else	
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('/terrain/seed/' . $structure -> id );
			}
					
		}
		
		/////////////////////////////////////////////////////////////////////////////////////////////////
		// Seleziono tutti i semi che si trovano nell'inventario del terreno
		/////////////////////////////////////////////////////////////////////////////////////////////////
					
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'seed';
		$view -> submenu = $submenu;
		$view -> structure = $structure;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		
	}

	/**
  * Funzione che preleva il raccolto
	* @param terrain_id del terreno da raccogliere
	* @return none
	*/
	
	public function harvest( $structure_id )
	{
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = StructureFactory_Model::create( null, $structure_id);
		
		if ( ! $structure -> allowedaccess( $character, $structure -> getParentType() , $message, 'private', 'harvest' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect('region/view/');
		}
		
		$ca = Character_Action_Model::factory("harvest");
		
		$par[0] = $character;
		$par[1] = $structure; 
		
		if ( $ca -> do_action( $par,  $message ) )
		{ 
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); 
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
		}
		
		HTTP::redirect( "terrain/manage/" . $structure -> id );			
	}
}
