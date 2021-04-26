<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Quests extends Controller_Template
{
	
	public $template = 'template/gamelayout';
		
	/*
	* Activate a quest
	*/
	
	function activate( $name )
	{
		if ( isset($this -> disabledmodules['quests']) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('charactions.error-moduleisdisabled') . "</div>");						
			HTTP::redirect('character/details/');
		}
		
		$character = ORM::factory("character", Session::instance()->get('char_id'));
		$quest = Model_QuestFactory::createQuest($name);
		$rc = $quest -> activate( $character, $message );
		
		if ( $rc == false )
		{
			Session::instance()->set('user_message', 	"<div class=\"error_msg\">". __($message) . "</div>");
			HTTP::redirect( 'character/myquests');				
		}
		else
		{
			Session::instance()->set('user_message', 	"<div class=\"info_msg\">". __($message) . "</div>");			
			HTTP::redirect( 'quests/view/' . $name );		
		}
	}
	
	
	
	function activatetutorial()
	{
		$character = ORM::factory("character", Session::instance()->get('char_id'));
		
		if ( Model_Character::has_achievement( $character -> id, 'stat_tutorialcompleted' ) == true )
		{
			Session::instance()->set('user_message', 	"<div class=\"error_msg\">". __('quests.error-tutorialiscompleted') . "</div>");
			HTTP::redirect( 'boardmessage/index/europecrier');		
		}
		
		$quest = Model_QuestFactory::createQuest('accountconfiguration');
		$quest -> activate( $character );
		
		HTTP::redirect('quests/tutorialentry');	
	}
	
	function endtutorial()
	{
		$character = ORM::factory("character", Session::instance()->get('char_id'));
		if ( Model_Character::has_achievement( $character -> id, 'stat_tutorialcompleted' ) == true )
		{
			Session::instance()->set('user_message', 	"<div class=\"error_msg\">". __('quests.error-tutorialiscompleted') . "</div>");
			HTTP::redirect( 'boardmessage/index/europecrier');		
		}
		
		$par = array();
		Model_GameEvent::process_event( $character, 'endtutorial', $par );
		HTTP::redirect('region/view');
	}	
	
	/**
	* Gestisce l' entry point tutorial. 
	* Se il modo tutorial � attivo
	* visualizza il quest attivo.
	* @param none
	* @return none
	
	
	function obs_tutorialentry()
	{
		$character = ORM::factory("character", Session::instance() -> get('char_id'));
		
		if ( Character_Model::has_achievement( $character -> id, 'stat_tutorialcompleted' ) == true )
		{
			Session::instance()->set('user_message', 	"<div class=\"error_msg\">". __('quests.error-tutorialiscompleted') . "</div>");
			HTTP::redirect( 'boardmessage/index/europecrier');		
		}
		
		if ( $character -> user -> tutorialmode == 'Y' )
		{				
			// troviamo il quest finale
			$finaltutorialquest = ORM::factory('cfgquest') -> 
				where ( 
					array( 
						'final' => true,
						'path' => 'tutorial' ) ) -> find();
			
			$finalquest = Character_Model::get_stat_d ( 
				$character -> id,
				'quest',
				$finaltutorialquest -> id ); 
			
			// troviamo se ha iniziato il path tutorial
			
			$c = Database::instance() -> query ( 
			"select cs.id from character_stats cs, cfgquests cq
			where cs.name = 'quest' 
			and   cs.character_id = " . $character -> id . " 
			and   cs.value = cq.id 
			and   cq.path = 'tutorial' " );
						
			// se il quest finale � completato allora informiamo
			// il giocatore che ha terminato il tutorial
			
			if ( $finalquest -> loaded and $finalquest -> param2 == 'completed' )
			{
				$character -> user -> tutorialmode = 'N';
				$character -> user -> save();
				Session::instance()->set('user_message', 
				"<div class=\"error_msg\">". 
					__('quests.error-tutorialiscompleted') . "</div>");
				HTTP::redirect( 'boardmessage/index/europecrier');
			}
			
			// se non ha quest del tutorial attivi, 
			// mostriamo una pagina di benvenuto
			
			if ( $c -> count() == 0 )						
				HTTP::redirect( 'page/display/welcomepage');			
				
			// altrimenti, lo ridirezioniamo al quest attivo
			
			else
			{
				
				$activequest = $character -> get_active_quest();
				
				if ( $activequest -> loaded  )
					HTTP::redirect('quests/view/' . $activequest -> param1 );
				else
				{
					HTTP::redirect( 'boardmessage/index/europecrier');
				}
			}
		}		
		else
			HTTP::redirect( 'boardmessage/index/europecrier');
	
	}
	*/
	
	/**
	* Visualizza pagina informativa del quest
	* @param strig $questname Nome del quest
	* @return none
	*/
	
	function view ( $questname )
	{		
	
	
		$sheets  = array(
			'gamelayout' => 'screen', 
			'character' => 'screen', 
			'submenu' => 'screen');		
		
		$character = ORM::factory("character", Session::instance()->get('char_id'));
		$cfgquest = ORM::factory('cfgquest') ->	where ( 'name', $questname ) -> find();
		
		$info = Model_Cfgquest::get_info( $questname, $character->id );
		
		if ( $info['status'] != 'active' )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". 
					__('quests.error-questisnotactive') . "</div>");
			HTTP::redirect('character/myquests' );
		}
		
		//var_dump( $info['currentstep'] );exit;
		
		$view = View::factory( 'quests/' . $questname .'/' . $info['currentstep'] -> id );
		$header = View::factory( 'quests/header' );
		$header -> info = $info ;		
		$view -> header = $header;
		$view -> questname = $questname;
		
		$view -> info = $info;
		$view -> character = $character;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	
	
	}
	
	/**
	* Accetta un quest
	* @param id quest id
	* @return none
	*/

	function accept( $id )
	{
		
		$cfgquest = ORM::factory('cfgquest', $id );
		if ( !$cfgquest -> loaded )
		{			
			Session::instance()->set('user_message', 
				"<div class=\"error_msg\">". 
					__('quests.error-questnotexists') . "</div>");
			HTTP::redirect('character/myquests' );
		}
	
	}
	
}
