<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Shop extends Controller_Template
{

	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	
	/**
	* Elenca i diversi tipi di bottega
	* @param none
	* @return none	
	**/
	
	public function index()	
	{
	
		$view = View::factory('shop/index');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'structure'=>'screen');				
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$region = ORM::factory('region', $character -> position_id );
		$shops = ORM::factory("structure_type") 
			-> where ( array( 
				'parenttype' => 'shop' , 
				'level' => 1 ) ) -> find_all ();
		
		$view -> char = $character;
		$view -> region = $region;
		$view -> shops = $shops;
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
	
		$view = View::factory('shop/manage');
		$section_upgradehourlywage = View::factory('structure/section_upgradehourlywage');
		$section_description = View::factory('structure/section_description');		

		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$structure = StructureFactory_Model::create( null, $structure_id);
		
		if ( ! $structure->allowedaccess( $character, $structure -> getParentType() , $message, 'private', 'manage' ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
			HTTP::redirect( 'region/view/' );
		}
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'manage';
		$view -> submenu = $submenu;
		$section_upgradehourlywage -> structure = $structure;
		$section_upgradehourlywage -> upgradehourlywage = $structure -> getUpgradehourlywage();		
		$view -> section_upgradehourlywage = $section_upgradehourlywage;		
		$section_description -> structure = $structure;
		$view -> section_description = $section_description;
		$view -> structure = $structure;
		$this -> template -> content = $view ;
		$this -> template -> sheets = $sheets; 
	
	}
	
	
	/**
	 * Permette di upgradare il negozio
	 * @param type tipo di upgrade
     * @param structure_id id struttura	 
	 * @return none
	*/
	
	function upgrade( $type = 'level', $structure_id = null) 
	{
		$view = View::factory ( '/shop/upgrade'. $type ); 
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm    = View::factory ('template/submenu');
		
		if ( ! $_POST ) 
		{
			$structure = StructureFactory_Model::create( null, $structure_id );
			
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}	
		
		}	
		else
		{
			
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );
			if ( $this -> request -> post('upgradeinventory' ) )
			{
				
				$message = "";			
				$ca = Character_Action_Model::factory("upgradestructureinventory");				
				$par[0] = $structure;
				$par[1] = $character; 
		
				if ( $ca->do_action( $par,  $message ) )
				{
					Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); 
					HTTP::redirect( '/shop/upgrade/inventory/' . $structure -> id ); 
				}	
				else	
				{ 
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
					HTTP::redirect( '/shop/upgrade/inventory/' . $structure -> id ); 
				}
			}
			
			if ( $this -> request -> post('upgradelevel' ) )
			{
				$message = "";			
				$ca = Character_Action_Model::factory("upgradestructurelevel");								
				$par[0] = $structure;
				$par[1] = $character; 
				$par[2] = $this -> request -> post('hours'); 
		
				if ( $ca->do_action( $par,  $message ) )
				{
					Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); 
					HTTP::redirect( '/shop/upgrade/level/' . $structure -> id ); 
				}	
				else	
				{ 
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
					HTTP::redirect( '/shop/upgrade/level/' . $structure -> id ); 
				}
			}
			
			$structure_id = $this -> request -> post( 'structure_id' ) ; 
	
	}	
	
	$levelupgradeworkerhours = Structure_Model::get_stat_d( $structure -> id, 'levelupgradeworkerhours' );
	$view -> levelupgradeworkerhours = is_null ( $levelupgradeworkerhours ) ? 0 : $levelupgradeworkerhours -> value;
	$lnkmenu = $structure -> get_horizontalmenu( 'upgradeinventory' );
	$subm -> submenu = $lnkmenu;
	$view -> submenu = $subm;
	$view -> structure = $structure ; 
	$this -> template -> content = $view ; 
	$this -> template->sheets = $sheets;

}

}
