<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CAUnlockcontainer extends Model_CharacterAction
{
	// Parametri
	const DELTA_GLUT = 10;
	const DELTA_ENERGY = 10;

	protected $cancel_flag = true;
	protected $immediate_action = false;
	
	protected $basetime       = 1; 
	protected $attribute      = 'intel';  // attributo forza
	protected $appliedbonuses = array ( 'workerpackage'); // bonuses da applicare

	
	// L'azione richiede che il personaggio indossi
	// un determinato equipaggiamento
	protected $requiresequipment = true;
	
	// Equipaggiamento o vestiario necessario in base al ruolo
	// Consume_rate = percentuale di consumo dell'item
	
	protected $equipment = array
	(
		'all' => array
		(
			'body' => array
			(
				'items' => array('any'),
				'consume_rate' => 'verylow'
			),
			'torso' => array
			(
				'items' => array('any'),
				'consume_rate' => 'verylow'
			),
			'legs' => array
			(
				'items' => array('any'),
				'consume_rate' => 'verylow'
			),
			'right_hand' => array
			(
				'items' => array('knife'),
				'consume_rate' => 'veryhigh',
			),
		),
	);

	
	/*
	* Effettua tutti i controlli relativi alla unlock container e 
	* quelli condivisi con tutte le action 
	* @input:  $par[0] = item id
	* @input:  $par[1] = char
	* @output: TRUE = azione disponibile, FALSE = azione non disponibile
	* @output: $messages contiene gli errori in caso di FALSE
	*/
	protected function check( $par, &$message )
	{ 
		// Check classe madre (compreso il check_equipment)
		if ( ! parent::check_( $par, $message ) )
		{ return false; }
		
		// Istanzio la secret box
		$secretbox = ORM::factory('item', $par[0]);
		
		// Check: l'item non � nell'inventario del char
		if ( $secretbox->character_id != $par[1]->id )
		{ $message = Kohana::lang("ca_unlockcontainer.box-not-inventory"); return false; }
		
		// Check: l'item non � un secret box
		if ( $secretbox->cfgitem->tag != "secretbox" )
		{ $message = Kohana::lang("ca_unlockcontainer.item-not-secretbox"); return false; }
		
		// Check: et� del char � < 30 giorni
		if ( $par[1]->get_age() < 30 )
		{ $message = kohana::lang('character.agerequirementfailed', 30); return false; }
		
		// Check: il char non ha l'energia sufficiente
		// Check: il char non ha la saizet� sufficiente
		if
		(
			$par[1]->energy < (self::DELTA_ENERGY) or
			$par[1]->glut < (self::DELTA_GLUT)
		)
		{  $message = Kohana::lang("charactions.notenoughenergyglut"); return false; }
		
		// Tutti i check sono stati superati
		return true;
	}

	
	/*
	* Funzione per l'inserimento dell'azione nel DB.
	* @input:  $par[0] = item id
	* @input:  $par[1] = char
	* @output: TRUE = azione disponibile, FALSE = azione non disponibile
	* @output: $messages contiene gli errori in caso di FALSE
	*/
	protected function append_action( $par, &$message )
	{
		$this->character_id = $par[1]->id;
		$this->starttime = time();			
		$this->status = "running";			
		$this->endtime = $this -> endtime = $this -> starttime + $this -> get_action_time( $par[1] );

		$this->param1 = $par[0]; // Id del secret box
		$this->save();		
		
		$message = kohana::lang('ca_unlockcontainer.unlock-ok');	
		
		return true;
	}
	
	
	/*
	* Esecuzione dell'azione
	* @input:  $data  dati della char action
	*/
	public function complete_action( $data )
	{
		// Istanzio il character e la secretbox
		$char      = ORM::factory('character', $data->character_id);
		$secretbox = ORM::factory('item', $data -> param1);

		// Consumo degli items/vestiti indossati
		Model_Item::consume_equipment( $this->equipment, $char );
		
		// Sottraggo l'energia e la saziet� al char
		$char->modify_energy( -self::DELTA_ENERGY, false, 'unlockcontainer' );
		$char->modify_glut( -self::DELTA_GLUT );
		$char->save();	

		// Estraggo il contenuto del secretbox
		list($quantity, $item) = explode(";", $secretbox -> param1);
		
		// Analizzo il contenuto del secretbox
		switch ($item)
		{
			// silvercoin
			// doubloon
			case 'silvercoin':
			case 'doubloon':
			{
				$i = Model_Item::factory( null, $item);
				$i -> additem("character", $char->id, $quantity);
				
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-item' . ';' . $quantity . ';__' . $i->cfgitem->name,
					'evidence'
				);
				break;
			}

			// nothing
			case 'nothing':
			{
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-nothing',
					'evidence'
				);
				break;
			}
			
			// resetenergy
			case 'resetenergy':
			{
				$char->modify_energy( 0, true, 'resetenergy' );
				$char->save();
		
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-resetenergy',
					'evidence'
				);
				break;
			}
			
			// resetglut
			case 'resetglut':
			{
				$char->modify_glut( 0, true );
				$char->save();
		
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-resetglut',
					'evidence'
				);
				break;
			}
			
			// addresource
			// increaseresourcecapacity
			// changegeoofregion
			// changeclimate
			// addresource
			// mountainarmorset
			// frencharmorset
			// blackarmorset
			case 'addresource':
			case 'increaseresourcecapacity':
			case 'changegeoofregion':
			case 'changeclimate':
			case 'addresource':
			case 'mountainarmorset':
			case 'frencharmorset':
			case 'blackarmorset':
			{
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-special-admin' . ';addresource',
					'evidence'
				);
				break;
			}			
			
			// -1attribute
			case '-1attribute':
			{
				$attr[0] = 'str';
				$attr[1] = 'dex';
				$attr[2] = 'intel';
				$attr[3] = 'cost';
				$attr[4] = 'car';
				
				$rnd = rand (0,4);
				
				// DA VERIFICARE, NON FUNZIONA
				$char -> set_attribute( $attr[$rnd], -1 );
				$char -> save();
				
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-lostattribute;__character.create_char' . $attr[$rnd],
					'evidence'
				);
				break;
			}
			
			// +1attribute
			case '+1attribute':
			{
				$attr[0] = 'str';
				$attr[1] = 'dex';
				$attr[2] = 'intel';
				$attr[3] = 'cost';
				$attr[4] = 'car';
				
				$rnd = rand (0,4);
				
				// DA VERIFICARE, NON FUNZIONA
				$char -> set_attribute( $attr[$rnd], +1 );
				$char -> save();
				
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-gainattribute;__character.create_char' . $attr[$rnd],
					'evidence'
				);
				break;
			}
			
			// getplague
			case 'getplague':
			{
				$plague = Model_DiseaseFactory::createDisease('plague');
				$plague -> injectdisease($char->id );
				
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-getplague' . ';addresource',
					'evidence'
				);
				break;
			}	
			
			// getdrunk
			case 'getdrunk':
			{
				$drunkness = Model_DiseaseFactory::createDisease('drunkness');
				$drunkness -> injectdisease($char->id );
				
				Model_CharacterEvent::addrecord
				( 
					$char -> id, 
					'normal',
					'__ca_unlockcontainer.prize-getdrunk' . ';addresource',
					'evidence'
				);
				break;
			}	
		}
		
		// Elimino la secretbox
		
		$secretbox -> destroy();
		
		return; 
		
	}
	
	
	protected function execute_action() {}
	
	
	public function cancel_action( )
	{ return true; }
	
	
	/*
	* Questa funzione costruisce un messaggio da visualizzare 
	* in attesa che la azione sia completata.
	* @input: $type  string  tipo di messaggio da restituire (forma estesa o corta)
	*/
	public function get_action_message( $type = 'long') 
	{
		$pending_action = $this->get_pending_action();
		$message = "";				
		
		if ( $pending_action->loaded )
		{
			if ( $type == 'long' )					
			$message = '__regionview.unlockcontainer_longmessage';
			else
			$message = '__regionview.unlockcontainer_shortmessage';
		}
				
		return $message;
	}
	
}
