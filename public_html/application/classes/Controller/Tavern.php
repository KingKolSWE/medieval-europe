<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Tavern extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	const TAVERNRESTBASICPRICE = 0.28;

	/*
	
	// Disabilitato
	
	function wheeloffortune()
	{
		// Ruota della fortuna disabilitata
		HTTP::redirect('region/view/');
		
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
		
		$char = Character_Model::get_info( Session::instance()->get('char_id') ); 
		
		if ($char -> get_age() > 14 )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". 
				__('structures_tavern.error-toooldtoplay') . "</div>");
				HTTP::redirect('region/view/');			
		}
		
		// Check: il char non ha 10 sc
		if ( ! $char -> check_money( 10 ) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". 
				__('charactions.global_notenoughmoney') . "</div>");
				HTTP::redirect('region/view/');
		}
		
		$view = View::factory ( 'tavern/wheeloffortune' );
		$this -> template -> sheets = $sheets;
		$this -> template -> content = $view; 
	}
	
	*/	
	
	// dà reward per wheeloffortune.
	// Disabilitato
	
	/*
	function givereward()	
	{		
		$rc = array(
			'message' => '',
			'code' => false);
			
		$prizes = array(
			'doubloon' => 10,
			'silvercoin' => 10,
			'beerbottle' => 1,
			'winebottle' => 1,			
			'cheese' => 1,
			'bread' => 2,
			'handaxe' => 1,
			'sickle' => 5,
			'shovel' => 1,
			'coppercoin' => 1,			
			'pickaxe' => 1,
			'hoe' => 1
		);

		if ( request::is_ajax() )
		{
			
			$this -> auto_render = false;
			$char = Character_Model::get_info( Session::instance()->get('char_id') ); 

			$tag = $this -> request -> post('tag');
			
			KO7::$log->add(KO7_Log::DEBUG, "-> Tag: {$tag}");

		
			// if prize is not in the list, fail silently.
			if (!isset($prizes[$tag]))
			{
				$rc['message'] = __('structures_tavern.error-prizenotinlist');
				$rc['code'] = false;					
				echo json_encode($rc);
				return;
			}
							
			// check if the char did already get his daily reward.
			$dr = Character_Model::get_stat_d(
				$char -> id,
				'dailyreward'
			);			
			
			if (!is_null($dr) and date("dmY", $dr -> stat1) == date("dmY", time()))
			{
				$rc['message'] = __('structures_tavern.error-dailyrewardalreadyclaimed');
				$rc['code'] = false;				
				echo json_encode($rc);
				return;
			}
			
			// controllo età
			
			if ($char -> get_age() > 14 )
			{
				$rc['message'] = __('structures_tavern.error-toooldtoplay');
				$rc['code'] = false;				
				echo json_encode($rc);
				return;				
			}
			
			KO7::$log->add(KO7_Log::DEBUG, "-> Rewarding {$prizes[$tag]} $tag to: {$char -> name} ");
			
			$par[0] = $char;
			$par[1] = ORM::factory('cfgitem') 
				-> where( 'tag', $tag ) -> find();
				
			//KO7::$log->add(KO7_Log::DEBUG, kohana::debug($par[1]));
			$par[2] = $prizes[$tag];
			$par[3] = 'dailyreward';
			$par[4] = ORM::factory('character', 1);
			
			$ca = Character_Action_Model::factory("giveitem");							
			if ( $ca -> do_action( $par, $message ) )
			{ 				
				
				KO7::$log->add(KO7_Log::DEBUG, "-> Reward Given.");
				
				Character_Model::modify_stat_d(
					$char -> id, 
					'dailyreward',
					0,
					null,
					null,
					true,
					time()
				);
				
				// Toglie i soldi al char
				$char -> modify_coins( -10, 'wheeloffortune' );
						
				KO7::$log->add(KO7_Log::DEBUG, "-> Returning OK");
				
				$rc['message'] = __('structures_tavern.info-itemclaimed');
				$rc['code'] = true;				
				echo json_encode( $rc );				
				return;
			}	
			else	
			{ 
				$rc['message'] = "Error from Procedure.";
				$rc['code'] = false;				
					echo json_encode( $rc );				
					return;
			}	
						
		}		
		
	}
	*/
	
	
	/**
	* Funzione: rest (permette al char di riposare 4/8 ore)
	* @param: int $structure_id id struttura	
	* @return none
	*/
	
	function rest( $structure_id = null )
	{
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm = View::factory ('template/submenu');
		$view = View::factory ('tavern/rest');		
		$currentregion = ORM::factory('region', $character -> position_id);
		
		if ( !$_POST )
		{
			
			$structure = StructureFactory_Model::create( null, $structure_id);
		
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'public' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			
			$data = ST_Tavern_1_Model::get_price( $character, $structure );
			
		}		
		else
		{	
			
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id'));
			
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message, 'public' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}	
			
			$ca = Character_Action_Model::factory("resttavern");		
			$data = ST_Tavern_1_Model::get_price( $character, $structure );
			
			$par[0] = $character;
			$par[1] = $this -> request -> post('percentage');	
			$par[2] = $structure;
			
			if ( $this -> request -> post('mode') == 'free' )
				$par[3] = true;
			else
				$par[3] = false;
			
			$par[4] = $data['price'];			
			$par[5] = $this -> request -> post('percentage');
			$par[6] = $data['baseprice'];
			
			
			if ( $ca -> do_action( $par, $message ) )
			{ Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); }
			else	
			{ Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
		}	
		
		if ( Model_Character::is_resting( $character -> id ) )
			$view = View::factory ( 'structure/isresting' );
		
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'rest';
		$view->submenu = $submenu;
		$view -> price = $data['price'];
		$view -> freerestinfo = $character -> get_restfactor( $structure, true, false );
		$view -> paidrestinfo = $character -> get_restfactor( $structure, false, false );
		$view -> structure = $structure;
		$view -> character = $character;
		$this -> template -> sheets = $sheets;
		$this -> template -> content = $view; 
	
	}
	
	/*
	* Gioco dadi
	* @param int $structure_id id struttura
	* @param str $type tipo gioco	
	* @return none
	*/
	
	function game_dice( $structure_id, $type = 'simple' )
	{
		$view = View::factory ( 'tavern/game_dice' . $type );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$subm = View::factory ('template/submenu');

		if ( !$_POST )
		{
			$structure = StructureFactory_Model::create( null, $structure_id );
			if ( ! $structure->allowedaccess( $char, $structure -> getParentType(), $message, 'public' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		}
		else
		{
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure->allowedaccess( $char, $structure -> getParentType(), $message, 'public' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			$type = $this -> request -> post('type');

			$ca = Character_Action_Model::factory("game_dice" . $type );		

			$par[0] = $char;							
			$par[1] = $structure;
			
			if ( $ca -> do_action( $par,  $message ) )
				{ 
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect( 'tavern/game_dice/' . $structure_id . '/' . $type ); 
				}
			else	
				{ 
					Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
					HTTP::redirect( 'tavern/game_dice/' . $structure_id . '/' . $type ); 
				}
		}
		
		$game = ORM::factory('game' ) -> where ( array( 'name' => 'dice' . $type ) ) -> find();
		$submenu = View::factory( 'structure/' . $structure -> getSubmenu() );
		$submenu -> id = $structure -> id;
		$submenu -> action = 'game_dice';
		$view->submenu = $submenu;
		$view -> jackpot = $game -> param1;
		$view -> structure = $structure;
		$this -> template -> sheets = $sheets;
		$this -> template -> content = $view; 	
	
	}	
	
	/*
	* Carica la lista dei vincitori
	* @param structure_id id struttura
	* @param type typo gioco
	* @return none
	*/
	
	function show_winners( $structure_id, $type = 'dicesimple' )
	{
		
		$view = View::factory ( 'tavern/show_winners' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$structure = StructureFactory_Model::create( null, $structure_id );
		
		$winners = Database::instance() -> query( 
			"select gw.*, r.name region_name 
			from gamewinners gw, regions r
			where gw.region_id = r.id 
			and game = ? 
			order by amount desc", $type);
		
		$view -> type = $type;
		$view -> structure = $structure;		
		$view -> winners = $winners;		
		$this -> template -> sheets = $sheets;
		$this -> template -> content = $view; 	
	
	}
	
	
	
	
}
