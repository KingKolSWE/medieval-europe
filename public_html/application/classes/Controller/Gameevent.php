<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Gameevent extends Controller_Template
{
	// Imposto il nome del template da usare	
	
	function index(  )
	{				
		
		$view = View::factory ('gameevent/index');				
		$gameevents = ORM::factory("cfggameevent") -> find_all();
		$view -> gameevents = $gameevents;
		$this -> template -> content = $view;	
	}
	
	function view( $gameeventid )
	{
		
		$view = View::factory ('gameevent/view');				
		$gameevent = ORM::factory("cfggameevent", $gameeventid);
		
		$totalsubscriptions = 0;
		$doubloonsjackpot = 0;
		$silvercoinsjackpot = 0;
		
		foreach ( $gameevent -> gameevent_subscription as $subscription )
		{
			$totalsubscriptions ++;
			$doubloonsjackpot += $subscription -> doubloons;
			$silvercoinsjackpot += $subscription -> silvercoins;			
		}
		
		$view -> gameevent = $gameevent;
		$view -> totalsubscriptions = $totalsubscriptions;
		$view -> doubloonsjackpot = round($doubloonsjackpot *80/100);
		$view -> silvercoinsjackpot = round($silvercoinsjackpot*80/100);
		$this -> template -> content = $view;	
		
	}
	
	function subscribe()
	{
		$character = Model_Character::get_info( Session::instance() -> get('char_id') );

		$par[0] = $character;
		$par[1] = $this -> request -> post('cfggameeventid');

		if (null !== $this -> request -> post('subscribedoubloons'))
			$par[2] = 'doubloons';
		else
			$par[2] = 'silvercoins';
		
		$ca = Model_CharacterAction::factory("gameeventsubscribe");
		
		if ( $ca -> do_action( $par,  $message ) )
		{ 				
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			HTTP::redirect ( 'gameevent/view/' .  $this -> request -> post('cfggameeventid'));
		}	
		else	
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
			HTTP::redirect ( 'gameevent/view/' . $this -> request -> post('cfggameeventid'));
		}	
	}
		
	
	
}
