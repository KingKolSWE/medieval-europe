<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Bonus extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';					
	
	function index( $tabindex = 1)
	{	
		$view = View::factory('bonus/index');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$view -> tabindex = $tabindex;		
		$view -> user_id = $char -> user -> id;
		$view -> char = $char;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		
	}		
	
	public function getdoubloons()
	{

		$view = View::factory('bonus/getdoubloons');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		$view -> sw_apphash = Kohana::config( 'medeur.sw_apphash');
		$view -> char = $char;		
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;
		
	}
	
	public function getdoubloons_crypto() {
		$view = View::factory('bonus/getdoubloons_crypto');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$view -> char = $char;
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;
	}	
	
	function buy( )
	{
		
	//	var_dump($this -> request -> post());exit;
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$pb = PremiumBonus_Factory_Model::create( $this -> request -> post ('name') );
		$message = '';
		$par = array();
		$structure = null;
		
		
		if ( $this -> request -> post('targetchar') != '' )
			$targetchar = ORM::factory('character') -> 
				where ('name', $this -> request -> post('targetchar')) -> find();	
		else
			$targetchar = $char;
		
		/////////////////////////////////////////////
		// for Armory bonus, let's find barracks id.
		/////////////////////////////////////////////
		
		if ( $this -> request -> post( 'name' ) == 'armory' )
		{
			$region = ORM::factory('region') 
				-> where ( 'name', 'regions.' . strtolower($this -> request -> post('region_name')) ) -> find();	
			
			if (!$region -> loaded )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". __('global.error-regionunknown
') . "</div>");	
				HTTP::redirect('bonus/index/?tabindex=2');
			}
			
			$structure = $region -> get_structure( 'barracks');
			
		}
		
		/////////////////////////////////////////////
		// for atelier license, let's pass parameters
		/////////////////////////////////////////////
		
		if (strpos ( $this -> request -> post( 'name' ), 'atelier-license') !== false)
		{
			$par[0] = $targetchar -> sex;
			$par[1] = $this -> request -> post ('cut');	
			$par[2] = $this -> request -> post ('section');			
			$par[3] = $this -> request -> post ('subsection');					
			$par[4] = $this -> request -> post ('itemname');			
		}
		
		$rc = $pb -> add( $targetchar, $structure, $this -> request -> post ('cut'), $par, $message );		
		
		if ( $rc == true )
		{ 				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". __($message) . "</div>");
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __($message) . "</div>");
		}
		
		HTTP::redirect(request::referrer());
		
	}
	
}
