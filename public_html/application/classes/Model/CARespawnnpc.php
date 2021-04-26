<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CARespawnnpc extends Model_CharacterAction
{
	// Costanti	
	
	const CYCLE_TIME = 28800; // Tempo ciclo: 8 ore.
	protected $cancel_flag = false;
	protected $immediate_action = false;	
	// Nessun controllo dato che l' azione � chained dal sistema.
	
	protected function check( $par, &$error ) { return true; }

	protected function append_action( $par, &$error ) {}	

	public function complete_action( $data )
	{
		kohana::log('info', '------- START RESPAWN ------');
		
		$npcs = array(
			'smallrat' => array( 'name' => 'Small Rat' ),
			'largerat' => array( 'name' => 'Large Rat' ),
			'chicken' => array( 'name' => 'Chicken' ),
			'largedog' => array( 'name' => 'Large Dog' ),			
		);
		
		$alivechars = ORM::factory('character')
			-> where( array('type' => 'pc' ) )
			-> count_all();
		
		kohana::log('info', "-> N. of PC chars: {$alivechars}");
		
		foreach ($npcs as $npctag => $info)		
		{
			
			// Creo la classe corretta
			$npcclass = Model_NpcFactory::create($npctag);
			$npcclass -> create('dummy');
			
			// Stabilisco quanti NPC posso avere nel mondo.
			
			$maxnpcs = min(
				$npcclass -> getMaxNumber(), 
				round( $alivechars * $npcclass -> getRate())
			);
			
			kohana::log('debug', "-> Creating max {$maxnpcs} of type: {$npctag}.");
			
			// Trovo il numero di NPC presenti.
			$numberofnpc = ORM::factory('character')
			-> where ( 
					array( 
						'type' => 'npc',
						'npctag' => $npctag,
					)) -> count_all();
			
			kohana::log('info', "-> We have {$alivechars}, percentage is {$npcclass -> getRate()} so N. Max NPC of type {$npctag} allowed are: [{$maxnpcs}]. Current NPCs: {$numberofnpc} ");
			
			// Se il numero di NPC presenti � < del massimo
			// Creo fino ad arrivare al massimo...
						
			if ($numberofnpc < $maxnpcs )
			{
				// carica tutti i nomi usati da npc gi� creati.
				$names = array();
				
				$npcs = ORM::factory('character')
					-> where ('npctag', $npctag )
					-> find_all();
					
				foreach ($npcs as $npc)
					$names[$npc->name] = $npc->name;
				
				for ($i=1;$i<=($maxnpcs-$numberofnpc);$i++)
				{
					
					kohana::log('info', "-> Creating NEW NPC {$npctag}, n. {$i}.");
					
					$name = $info['name'] . ' called ' . mt_rand(1, 99999);
					
					if (array_key_exists($name,$names))
						continue;
					
					kohana::log('debug', "-> Creating NEW NPC: {$name}");					
					
					$npc = Model_NpcFactory::create( $npctag );
					$npc -> create( $name );
					$npc -> save();						
					$names[$name] = $name;
					//$npc -> equip();
					
					// Gli aggiungo la funzione di AI							
					
					$action_ai = Model_CharacterAction::factory('npcai');
					$action_ai -> character_id = $npc -> id;
					$action_ai -> save();			
					
				}
			}
			// Se il numero NPC � >= al numero massimo, rivivo quelli morti.
			
			if ($numberofnpc >= $maxnpcs)
			{
				
				$alivenpcs = ORM::factory('character')
				-> where ( 
					array( 
						'type' => 'npc',
						'npctag' => $npctag,
						'status' => null
					)) -> count_all();
				
				kohana::log('info', "-> NPC {$npctag} alive: {$alivenpcs}")	;
				$npcstorevive = ($maxnpcs-$alivenpcs);
				kohana::log('info', "-> DEAD NPC {$npctag} to revive: {$npcstorevive}.");
				
				$deadnpcs = ORM::factory('character')
				-> where ( 
					array( 
						'type' => 'npc',
						'npctag' => $npctag,						
						'status' => 'dead'
					)) -> find_all();
				
				$npcsrevived = 0;
				foreach ($deadnpcs as $deadnpc)
				{
					
					if ($npcsrevived >= $npcstorevive)
						break;
					
					$npcobj = ORM::factory('character_npc_' . $deadnpc -> npctag, $deadnpc -> id );					
					
					
					kohana::log('debug', 
						"-> Respawning NPC {$npcobj->name} Death time: " . date( "d-m-Y H:i:s", $npcobj->deathdate) . 
						", Revive ONLY if date is > than:" . date("d-m-Y H:i:s", ($npcobj->deathdate + $npcobj -> getRespawntime() * 3600 )));
					
					if ( time() > ($npcobj -> deathdate + $npcobj -> getRespawntime() * 3600 ) )						
					{									
						kohana::log('info',"-> Resuscitating NPC: {$npcobj -> name}");
						$npcobj -> respawn();												
						$npcsrevived++;
					}
				}
				kohana::log('info',"-> Resuscitated {$npcsrevived} NPCs.");
			}		
		}
		
		// reschedule action
		
		kohana::log('info', '-> Rescheduling respawn...');
		$a = ORM::factory('character_action', $data -> id );
		$a -> starttime = time() + self::CYCLE_TIME;
		$a -> endtime = $a -> starttime;
		$a -> save();
		kohana::log('info', '------- END RESPAWN ------');
		
	}
	
	public function get_action_message( $type = 'long') {}
	
}
