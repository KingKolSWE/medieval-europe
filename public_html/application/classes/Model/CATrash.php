<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterAction_CATrash extends Model_CharacterAction
{
	protected $immediate_action = true;
	
	// check
	// @input: parametri
	//  - par[0]: id struttura a cui si vuole buttare gli item
	//  - par[1]: id item da buttare
	//  - par[2]: quantit�
	
	protected function check( $par, &$message )
	{ 
		if ( ! parent::check_( $par, $message ) )					
			return false;
		
		// check input
		if ( intval( $par[2] ) <= 0 )
		{
			$message = kohana::lang( 'charactions.negative_quantity');
			return false;
		}
		
		// check: esiste la struttura nel nodo in cui � l' utente?
		$structure = Model_StructureFactory::create( null, $par[0]);
		
		if ( ! $structure->loaded or $structure -> structure_type -> type != 'dump' )	
		{
			$message = kohana::lang( 'structures.generic_structurenotfound');
			return false;
		}
		
		//check: il char effettivamente ha gli item nella quantit� specificata?
		$o = ORM::factory( "item" )
			->where( array ( 'character_id' => Session::instance()->get('char_id'), 
											 'id' => $par[1],
											 'quantity>=' => $par[2]) )->find();
											 
		if ( ! $o->loaded )		
		{				
			//print kohana::debug(  ORM::factory( "item", $item )->character_id ); exit();
			$message = kohana::lang('structures.generic_itemsnotowned'); 
			return false;
		}
		
		// si pu� buttare l' oggetto?
		if ( $o -> cfgitem -> trashable == false )
		{
			//print kohana::debug(  ORM::factory( "item", $item )->character_id ); exit();
			$message = kohana::lang('structures.generic_itemsnotdroppable'); 
			return false;
		}
				
		return true;
	}
	
	protected function append_action( $par, &$message ){	}

	public function execute_action ( $par, &$message ) 
	{
	
		$i = Model_Item::factory( $par[1], null );
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		///////////////////////////////////////////////////////////////////
		// aggiunge l' item sulla struttura.
		// se l' oggetto ha il flag destroy on trash, 
		// non aggiungerlo e distruggilo definitivamente
		//////////////////////////////////////////////////////////////////////
				
		if  ( $i->cfgitem->destroyontrash == true )	
			;
		else
			$ret_1 = $i->additem( "structure", $par[0], $par[2] );
		
		// toglie l' item dal char
		$ret_2 = $i->removeitem( "character", $char->id, $par[2] );
		
		kohana::log('debug', $i->cfgitem->tag );
		
		$text = '__events.itemtrashed;'. $par[2] . ';' . '__' . $i->cfgitem->name  ;
		 Model_CharacterEvent::addrecord( $char->id, 'normal', $text );
		
		$message = kohana::lang('charactions.itemtrashed_ok'); 
					
		return true;
	}
	
}
