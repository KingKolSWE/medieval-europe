<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterPermanentevent extends ORM
{
  protected $sorting = array('timestamp' => 'asc');

	public function add_model($character_id, $text)
	{
		
		$a = new Model_CharacterPermanentevent();
		$a -> id = null;
		$a -> type = 'normal';
		$a -> character_id = $character_id;
		$a -> description = $text;
		$a -> timestamp = time();				
		$a -> save();
	
	}
	
}
