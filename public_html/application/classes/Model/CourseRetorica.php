<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_RetoricaCourse extends Model_Course
{
	
	protected $coursetype = 'attribute';
	
	/**
	* Ritorna il livello a cui può essere studiato il corso
	* @param obj $char Character_Model
	* @return int Livello a cui si può studiare il corso
	*/
	
	public function getLevel( $char )
	{
		return $char -> get_attribute( 'car', false) + 1 ;		
	}
	
	/**
	* Completa il corso
	* @param obj $char Character_Model
	* @return none
	*/
	
	public function completeCourse( $char ) 
	{
		
		$oldvalue = $char -> car;
		$newvalue = min (20, $char -> car + 1) ;
		$char -> car = $newvalue;
		$increasedattr = 'create_charcar';				
				
		if ( $char -> car == 20 ) 
			Model_Achievement::compute_achievement ( 'stat_car', 20, $char -> id );
		
		Model_Character::modify_stat_d(
			$char -> id,
			'studiedhours', 
			0,
			$this -> getTag(),
			null, 
			true,
			0);
			
		Model_CharacterEvent::addrecord(
			$char -> id,
			'normal',  
			'__events.coursecompleted'.';__' . 'structures.course_' . $this -> getTag() . '_name' . ';__character.' . $increasedattr . 
			';' . $oldvalue . ';' . $newvalue,
			'evidence'
			);
			
	}
		
}
