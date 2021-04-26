<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_FightingQuest extends Model_Quest
{
	
	protected $name = 'fighting';
	protected $stepsnumber = 1;
	protected $strinit = '0---------';
	protected $id = 10;	
	protected $author_id = 1;
	protected $path = 'tutorial';
	
	function activate( $character, &$message, $spare3 = null, $spare4 = null )
	{		
	
		$quest = Model_QuestFactory::createQuest('crafting');
		if ($quest -> get_status($character) != 'completed')
		{
			$message = 'quests.error-craftingnotcompleted';
			return false;			
		}
		
		// pugnale
		$item = Model_Item::factory( null, 'knife' );
		$item -> quality = 40;
		$item -> additem( 'character', $character -> id , 1 );
		
		// sposta un topo nella regione del char.
		
		Database::instance() -> query(
		"update characters 
		 set position_id = {$character -> position_id}
		 where type = 'npc' 
		 and npctag = 'smallrat' 		  
		 and status is null 
		 order by id limit 1"
		);
	 
		
		// da un bonus velocità
		
		Model_Character::modify_stat_d(
			$character -> id,
			'speedbonus',
			10,
			null,
			null, 
			true,
			time()+3600);
			
		$rc = parent::activate( $character, $message, $spare3, $spare4 );
		
		if ( $rc == false )
			return false;					
		
		$this -> initialize( $character, $spare3, $spare4);
		
		return true;
	}
	
	function process_event_killrat( $char, $event, $par, $instance )
	{
		kohana::log('debug', '-> Quest: processing event: ' . $event );
		
		$char -> modify_health(100);
		$char -> save();
		
		kohana::log('debug', $char -> health);
		
		$this -> complete_step( $char, $instance, 0 );				
	}
	
	function finalize_quest( $char, $instance ) 
	{
		$char -> modify_health( +100 );
		$char -> modify_coins( +160, 'questreward' );		
	}
}
