	<?php defined('SYSPATH') OR die('No direct access allowed.');

	
class Model_Smallrat_CharacterNPCLargerat extends Model_SmallratCharacterNPC
{
	
	// i parametri = ai campi delle tabelle
	// vanno settati con get e set.
	
	protected $maxhealth = 20;	
	protected $respawntime = 120;
	protected $rate = 0.025;
	protected $maxnumber = 1000;
	
		
	function create( $name )
	{		
		parent::create( $name );
		$this -> setName ( $name );	
		$this -> setNpctag( 'largerat' );
		$this -> setStr(4);		
		$this -> setDex(4);
		$this -> setSex('M');		
		$this -> setIntel(10);		
		$this -> setCost(2);
		$this -> setCar(1);		
		$this -> setGlut(50);
		$this -> setEnergy(50);
		$this -> setHealth(20);		
		$this -> setName ( $name );			
	}
	
}
?>