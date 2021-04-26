<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CACreatecdb extends Model_CharacterAction
{

	protected $immediate_action = true;

	protected function check( $par, &$message )
	{ }
	
	protected function append_action( $par, &$message )
	{	}

	function complete_action( $data )
	{
	
		// crea la struttura battlefield		
		kohana::log('debug', '-> Completing createcdb');		
		
		$wd = ORM::factory( 'battle', $data -> param1 );		
		$attackedregion = ORM::factory('region', $wd -> dest_region_id );
		
		
		$cdb = Model_StructureFactory::create('battlefield_1', null);
		$cdb -> region_id = $wd -> dest_region_id;
		$cdb -> attribute1 = $wd -> id;
		$cdb -> attribute2 = 0;
		$cdb -> save();
		
		// Refresh cache
		$cachetag = '-cfg-regions-structures';        
		Model_MyCache::delete($cachetag);
		$cachetag = '-regionstructures_' . $wd -> dest_region_id;		
		Model_MyCache::delete($cachetag);
		
		$wd -> battlefield_id = $cdb -> id;
		$wd -> save();
				
		// Schedula azione battleround
		
		if ( $wd -> type == 'revolt' )
			$delta = kohana::config('medeur.revolt_firstbattleroundtime') * 3600 ;
		elseif ( $wd -> type == 'nativerevolt' )
			$delta = 48 * 3600 ;
		else
			$delta = 12 * 3600 ;
		
		$a = new Model_CharacterAction();
		$a -> action = 'battleround';
		$a -> character_id = $wd -> source_character_id;
		$a -> status = 'running';
		$a -> blocking_flag = false;
		$a -> starttime = $a -> endtime = $data -> starttime + $delta ;
		$a -> param1 = 1;
		$a -> param2 = $wd -> id;
		$a -> save();
		
		$king_def = ORM::factory('character', $wd -> dest_character_id );
		if ( $king_def -> loaded )
			Model_CharacterEvent::addrecord(
				$king_def -> id, 
				'normal', 
				'__events.cdbcreated_def' . ';' . 
				'__' . $attackedregion -> name,
				'evidence' 
			);
		
		// invio evento al Re attaccante
				
		$king_att = ORM::factory('character', $wd -> source_character_id );
		if ( $king_att -> loaded )
			Model_CharacterEvent::addrecord(
				$king_att -> id, 
				'normal', 
				'__events.cdbcreated_att' . ';' . 
				'__' . $attackedregion -> name,
				'evidence' 
		);
		
		// solo per raid, aggiungi evento town crier
		if ( $wd -> type == 'raid' )
			Model_CharacterEvent::addrecord(
				null, 
				'announcement', 
				'__events.wardeclaration_announcement2' .			
				';__' . $king_att -> region -> kingdom -> get_article() .
				';__' . $king_att -> region -> kingdom -> get_name()  .
				';__' . $king_def -> region -> kingdom -> get_article3() .			
				';__' . $king_def -> region -> kingdom -> get_name()  .						
				';__battle.' . $wd -> type .
				';__' . $attackedregion -> name,
				'evidence'
			);		
	}
	
	public function execute_action ( $par, &$message) 
	{ }
		
		
}
