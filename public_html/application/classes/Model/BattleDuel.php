<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_BattleDuelBattleType extends Model_BattleType
{
	protected $battletype = 'duel';
	protected $attackersnumber = 0;
	protected $defendersnumber = 0;
	var $battlefield = null;
	
	/** 
	* Performs the entire battle
	* 
	* @param par vector of parameters	
	* par0: obj battle
	* @param test flag of tests
	* @return 
	*/
	
	public function run( $par, &$battlereport, $test=false)
	{
		$this -> par = $par;			
		$this -> sourcechar = ORM::factory('character', $par[0] -> source_character_id );
		$this -> destchar = ORM::factory('character', $par[0] -> dest_character_id );
		$this -> attackingregion = ORM::factory('region', $this -> par[0] -> source_region_id ); 
		$this -> attackedregion = ORM::factory('region', $this -> par[0] -> dest_region_id ); 			
		$this -> be = new Model_BattleEngine();
		$this -> bm = $par[0];
		$this -> test = $test;		
		$this -> loadteams();						
		$this -> fight();						
		$battlereport = $this -> battlereport;		
		return;
	}

	/** 
	* Upload the two teams
	* 
	* @param par vector of parameters
	* @param test flag of tests
	* @return 
	*/
	
	public function loadteams( ) 
	{
		
		$attackers = array();
		$defenders = array();
		
		kohana::log('info', '-> *** Loadteams *** ' );
		
		// We only load chars if they are in the planned region of the duel 
		// and they are not doing any blocking action
		
		$attackerpendingaction = $this -> sourcechar -> get_currentpendingaction( $this -> sourcechar -> id );
		$defenderpendingaction = $this -> destchar -> get_currentpendingaction( $this -> destchar -> id );
		
		if ( $attackerpendingaction == 'NOACTION' and $this -> sourcechar -> position_id == $this -> bm -> source_region_id )
		{
			$attacker = $this -> be -> loadcharbattlecopy( $this -> sourcechar -> id );	
			$attackers[$attacker['char']['key']] = $attacker;			
		}
		else
			kohana::log('info', '-> Char: ' . $this -> sourcechar -> name . ' not loaded, either is not in the duel region or it has a blocking action' );
		
		if ( $defenderpendingaction == 'NOACTION' and $this -> destchar -> position_id == $this -> bm -> source_region_id )
		{
			$defender = $this -> be -> loadcharbattlecopy( $this -> destchar -> id );	
			$defenders[$defender['char']['key']] = $defender;			
		}		
		else
			kohana::log('info', '-> Char: ' . $this -> destchar -> name . ' not loaded, either is not in the duel region or it has a blocking action' );
			
		$this -> attackers = $attackers;
		$this -> defenders = $defenders;
		
		$this -> attackersnumber = count($attackers);
		$this -> defendersnumber = count($defenders);
		
		
	}
	
	/** 
	* Combat
	* 
	* @param none
	* @return none
	*/
	
	public function fight()
	{
		
		$this -> battlereport[]['battleround'] = '__battle.duelintroduction' . 
			';' . $this -> sourcechar -> name . 
			';' . $this -> destchar -> name . 
			';__' . $this -> attackedregion -> name . 
			';' . Model_Utility::format_datetime( time() );

		$this -> compute_bonusmalus();		
		$this -> battlereport[]['newline'] = '';
		
		$this -> be -> runfight( 
			$this -> attackers, 
			$this -> defenders, 
			'duel', 
			$this -> defeated, 
			$winners, 
			$this -> battlereport,			
			$this -> fightstats,
			$this -> test );
		
		//kohana::log('debug', kohana::debug( $this -> battlereport)); exit(); 
		
		$this -> handle_alive( );
		$this -> handle_defeated( ); 
		$this -> do_aftermath( );		
	
	}
	
	function compute_bonusmalue()
	{
		return;
	}
	
	/** 
	* Aftermath of the battle
	* 
	* @param none
	* @return none
	*/
	
	function do_aftermath() 
	{
		
		$attackerwins = $defenderwins = 0; 		
		
		// Determine who won and who lost
		
		if ( count($this -> attackers) == 0 and count($this -> defenders) > 0 )
		{	
			$winner = $this -> destchar;
			$loser  = $this -> sourcechar;
		}
		
		if ( count($this -> attackers) > 0 and count($this -> defenders) == 0 )
		{	
			$winner = $this -> sourcechar;
			$loser  = $this -> destchar;
		}
		
		if ( count($this -> attackers) == 0 and count($this -> defenders) == 0 )
		{	
			$winner = null;
			$loser  = null;
		}
		
		// check who was present and not present, manage honor
		
		kohana::log('info', '-> Attackers: ' . $this -> attackersnumber . ' Defenders: ' . $this -> defendersnumber );
		
		// update stats
		
		$duellocation = ORM::factory('region', $this -> bm -> source_region_id );
					
		// case: one of the two or both did not show up.
		
		if ( $this -> attackersnumber == 0 or $this -> defendersnumber == 0 )
		{
			if ( $this -> attackersnumber == 0 and $this -> defendersnumber > 0) 		
			{
				$this -> sourcechar -> modify_honorpoints( -1, 'duelabsence');
				$this -> destchar -> modify_honorpoints( +1, 'duelpresence');
				
				Model_CharacterEvent::addrecord(
				$this -> destchar -> id, 
				'normal',
				'__events.duelopponentdidntshow;' . $this -> sourcechar -> name,
				'evidence'				
				);
			
				Model_CharacterEvent::addrecord(
				null, 
				'announcement', 
				'__events.duelfinishedtowncriernoshow;' . 
				$this -> sourcechar -> name . ';' . 
				$this -> destchar -> name . ';__' . 
				$duellocation -> name . ';' . 
				$this -> sourcechar -> name,			
				'duel');
			}
			
			if ( $this -> attackersnumber > 0 and $this -> defendersnumber == 0) 		
			{
				$this -> destchar -> modify_honorpoints( -1, 'duelabsence');
				$this -> sourcechar -> modify_honorpoints( +1, 'duelpresence');
					
				Model_CharacterEvent::addrecord(
				$this -> sourcechar -> id, 
				'normal',
				'__events.duelopponentdidntshow;' . $this -> destchar -> name,
				'evidence'				
				);
							
				Model_CharacterEvent::addrecord(
				null, 
				'announcement', 
				'__events.duelfinishedtowncriernoshow;' . 
				$this -> sourcechar -> name . ';' . 
				$this -> destchar -> name . ';__' . 
				$duellocation -> name . ';' . 
				$this -> destchar -> name,				
				'duel');
			
			}
			
			if ( $this -> attackersnumber == 0 and $this -> defendersnumber == 0) 		
			{
				$this -> destchar -> modify_honorpoints( -1, 'duelabsence');
				$this -> sourcechar -> modify_honorpoints( -1, 'duelabsence');
				
				Model_CharacterEvent::addrecord(
				$this -> destchar -> id, 
				'normal',
				'__events.duelopponentdidntshow;' . $this -> sourcechar -> name,
				'evidence'				
				);
				
				Model_CharacterEvent::addrecord(
				$this -> sourcechar -> id, 
				'normal',
				'__events.duelopponentdidntshow;' . $this -> destchar -> name,
				'evidence'				
				);
				
				Model_CharacterEvent::addrecord(
				null, 
				'announcement', 
				'__events.duelfinishedtowncrierbothnoshow;' . 
				$this -> sourcechar -> name . ';' . 
				$this -> destchar -> name . ';__' . 
				$duellocation -> name . ';',							
				'duel');
			}
		}
		else
		{
			
			// the duel took place and there is a loser and a winner.
			
			$this -> destchar -> modify_honorpoints( +1, 'duelpresence');
			$this -> sourcechar -> modify_honorpoints( +1, 'duelpresence');
			
			if ( !is_null( $winner ) and !is_null( $loser ) )
			{
				$winnerscore = 1;
				$loserscore = -1;
		
				$winner -> modify_stat(
					'duelscore',
					$winnerscore, 
					null,
					null,
					false,
					+1,
					+1,
					null,
					null,
					null,
					null
				);
		
				$loser -> modify_stat(
					'duelscore',
					$loserscore, 
					null,
					null,
					false,
					0,
					+1,
					null,
					null,
					null,
					null
				);
		
				// event
			
				Model_CharacterEvent::addrecord(
					$winner -> id, 
					'normal',
					'__events.duelwinner;' . $loser -> name,
					'evidence'				
				);
			
				Model_CharacterEvent::addrecord(
					$loser -> id, 
					'normal',
					'__events.duellooser;' . $winner -> name,
					'evidence'				
				);
			
				Model_CharacterEvent::addrecord(
				null, 
				'announcement', 
				'__events.duelfinishedtowncrier;' . 
				$this -> sourcechar -> name . ';' . 
				$this -> destchar -> name . ';__' . 
				$duellocation -> name . ';' . 
				$winner -> name . ';' .
				html::anchor( 'page/battlereport/' . $this ->  bm -> id, '[Report]'),			
				'duel');
			}
			else
			{
				
				Model_CharacterEvent::addrecord(
					$this -> sourcechar -> id, 
					'normal',
					'__events.dueltie;' . $this -> destchar -> name,
					'evidence'				
				);
			
				Model_CharacterEvent::addrecord(
					$this -> destchar -> id, 
					'normal',
					'__events.dueltie;' . $this -> sourcechar -> name,
					'evidence'				
				);
			
				Model_CharacterEvent::addrecord(
				null, 
				'announcement', 
				'__events.duelfinishedtietowncrier;' . 
				$this -> sourcechar -> name . ';' . 
				$this -> destchar -> name . ';__' . 
				$duellocation -> name . ';' . 
				html::anchor( 'page/battlereport/' . $this ->  bm -> id, '[Report]'),			
				'duel');			
			}
		
		}
		
		//////////////////////
		// save battle entry
		//////////////////////
		
		$this -> completebattle( 1, $attackerwins, $defenderwins );
		
	}
	
}
