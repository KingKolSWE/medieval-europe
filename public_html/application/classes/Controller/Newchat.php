<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Newchat extends Controller_Template {

	public $template = 'template/gamelayout';	
	
	/**
	* Chat globale
	*/
	
	public function init()
	{
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		if ( is_null( $char ) ) 
			HTTP::redirect( '/page/display/notauthorizedpage' );
				
		if ($this -> request -> param('type') == 'side')
			$this -> template = View::factory('template/sidechat');
		
		$view = View::factory('newchat/freechat');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		
		// is character banned?
		
		$chatban = Model_Character::get_stat_d(	$char -> id, 'chatban', null, null );
		if ( $chatban -> loaded and $chatban -> stat1 > time())
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". 
				__('character.bannedfromchat', date("d-m-y H:i:s", $chatban -> stat1), $chatban -> stat2 ). "</div>");			
			HTTP::redirect('region/view/');
		}		
		
		require_once( "application/libraries/vendors/phpfreechat-1.7/src/phpfreechat.class.php");
		
		$params['title'] = 'ME Chat';
		$params["serverid"] = Kohana::config('medeur.environment') . '-' . md5(  __FILE__); // used to identify the chat
		$params["nick"] = $char -> name; 
				
		if ( $char -> name == 'Guglielmo Di Valenza' )
			$params["isadmin"] = true;
		else
			$params["isadmin"] = false;
		
		$params["frozen_nick"] = true;
		$params["max_nick_len"] = 25;
		$params["max_channels"] = 10;
		$params["timeout"] = 60000; //1 min
		$params["focus_on_connect"] = true;		
		$params["connect_at_startup"] = true;
		$params["data_public_url"] =  url::base() . 'application/libraries/vendors/phpfreechat-1.7/data/public';		
		$params["theme_default_url"] = url::base() . 'application/libraries/vendors/phpfreechat-1.7/themes';
		$params["theme"] = 'medieval';
		$params["shownotice"] = 1;
		$params["theme_url"] = url::base() . 'application/libraries/vendors/phpfreechat-1.7/themes';		
		$params["server_script_url"] = 'init';
		$params["channels"] = array ( 'Newborn', 'Trade', 'Main');
		$params["debug"] = false;
		$params["nickmeta"] = array( 
			'Kingdom' => __($char -> region -> kingdom -> name),
			'Age' => Model_Utility::d2y(time(), $char -> get_age()));
		$params["nickmeta_private"] = array( 'ip' );						
		$params["display_pfc_logo"] = false;
		$params["clock"] = false;
		$params["showwhosonline"] = true;
		$params["nickmarker"] = false;
		
		
		
		$chat = new phpFreeChat($params);
		
		$this -> template->sheets = $sheets;		
		$view -> chat = $chat ;
		$this -> template -> content = $view; 
		
	}
	
	public function void() {}
	
	/**
	* Chat taverna
	* @param none
	* @return none
	*/
	
	public function kingdomchat()
	{	
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory('newchat/structurechat');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		
		$currentregion = Model_Character::get_currentposition_d( $char -> id );
		
		if ( is_null ( $currentregion ) )		
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('global.operation_not_allowed') . "</div>");
			HTTP::redirect('region/view/');
		}		
		
		$chatban = Model_Character::get_stat_d(	$char -> id, 'chatban', null, null );
		if ( $chatban -> loaded and $chatban -> stat1 > time())
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". 
				__('character.bannedfromchat', date("d-m-y H:i:s", $chatban -> stat1), $chatban -> stat2 ). "</div>");			
			HTTP::redirect('region/view/');
		}
		
		$kingdom = ORM::factory('kingdom', $currentregion -> kid );
		
		$params["isadmin"] = false;
		$king = $kingdom -> get_king();
		if ( !is_null($king) and $king -> name == $char -> name )			
			$params["isadmin"] = true;
		
		if ( $char -> name == 'Guglielmo Di Valenza' )
			$params["isadmin"] = true;
		
		require_once( "application/libraries/vendors/phpfreechat-1.7/src/phpfreechat.class.php");
		
		$params["serverid"] = Kohana::config('medeur.environment') . '-' . md5(  __FILE__ ) . $kingdom -> id ; // used to identify the chat
		$params["nick"] = $char -> name; 							
		$params["title"] = __( $kingdom -> name ) . ' Chat' ;
		
		$params["frozen_nick"] = true;
		$params["max_nick_len"] = 25;
		$params["max_channels"] = 1;
		$params["focus_on_connect"] = true;		
		$params["connect_at_startup"] = true;		
		$params["data_public_url"] =  url::base() . 'application/libraries/vendors/phpfreechat-1.7/data/public';		
		$params["theme_default_url"] = url::base() . 'application/libraries/vendors/phpfreechat-1.7/themes';
		$params["theme_url"] = url::base() . 'application/libraries/vendors/phpfreechat-1.7/themes';
		$params["server_script_url"] = 'kingdomchat';
		$params["channels"] = array ('Kingdom' ); 
		$params["theme"] = 'medieval'; 
		$params["debug"] = false;		
		$params["nickmeta"] = array( 'Profile'=> Model_Character::create_publicprofilelink( $char -> id, $char -> name ) );
		$params["nickmeta_private"] = array( 'ip' );		
		$params["nickmarker"] = false;
		
		$chat = new phpFreeChat($params);
		
		$this -> template->sheets = $sheets;
		$view -> chat = $chat ;
		$this -> template -> content = $view; 
		
	}
	
	/**
	* Chat taverna
	* @param group_id: id gruppo
	* @return none
	*/
	
	public function groupchat( $group_id )
	{	
		
		$char = Model_Character::get_info( Session::instance() -> get('char_id') );
		$view = View::factory('newchat/structurechat');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$group = ORM::factory('group', $group_id );
		
		if ( !$group -> loaded or $group -> search_a_member( $char -> id ) == false )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('global.operation_not_allowed') . "</div>");
			HTTP::redirect('region/view/');
		}
		
		$params["isadmin"] = false;
		if ($group -> character_id == $group -> character_id)
			$params["isadmin"] = true;		
		
		require_once( "application/libraries/vendors/phpfreechat-1.7/src/phpfreechat.class.php");
		$params["serverid"] = md5( __FILE__ . $group_id ); // used to identify the chat
		$params["nick"] = $char -> name; 							
		$params["title"] = $group -> name . ' - Group Chat';
		$params["frozen_nick"] = true;		
		$params["nickmeta"] = array();
		$params["max_nick_len"] = 25;
		$params["max_channels"] = 1;
		$params["focus_on_connect"] = true;		
		$params["connect_at_startup"] = true;		
		$params["data_public_url"] =  url::base() . 'application/libraries/vendors/phpfreechat-1.7/data/public';		
		$params["theme_default_url"] = url::base() . 'application/libraries/vendors/phpfreechat-1.7/themes';
		$params["theme_url"] = url::base() . 'application/libraries/vendors/phpfreechat-1.7/themes';
		$params["server_script_url"] = '?group_id='. $group_id;
		$params["channels"] = array ('Group Chat' ); 
		$params["theme"] = 'medieval'; 
		$params["debug"] = false;		
		$params["nickmeta"] = array();
		$params["nickmeta_private"] = array( 'ip' );		
		$params["nickmarker"] = false;
		
		$chat = new phpFreeChat($params);
		
		$this -> template->sheets = $sheets;
		$view -> chat = $chat ;
		$this -> template -> content = $view; 
		
	}
	
}	
