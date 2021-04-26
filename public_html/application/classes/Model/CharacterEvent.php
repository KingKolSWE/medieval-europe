


<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterEvent extends ORM
{
	protected $sorting = array('id' => 'desc');
	
	/**
	* Adds a character event or a boardmessage
	* @param int characterid
	* @param string eventtype ('announcement' => towncrier event 'normal' => character event)
	* @param string text event text
	* @param class css class
	*/
	
	public static function addrecord( $character_id, $eventtype, $text, $eventclass = null)
	{		
	
		if ( $eventtype == 'announcement' )
			Model_Boardmessage::systemadd( 1, 'europecrier', $text, $eventclass);
		else
		{
			$a = new Model_CharacterEvent();
			$a -> id = null;
			$a -> character_id = $character_id;
			$a -> type = $eventtype;
			$a -> description = $text;
			$a -> timestamp = time();		
			$a -> eventclass = $eventclass;						
			$a -> save();
			
			Model_MyCache::delete(  '-charinfo_' . $character_id . '_unreadevents' );
			
			
		
		}
	}
	
}

