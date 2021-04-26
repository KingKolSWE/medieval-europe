<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_BattleconstCourse extends Model_Course
{
	protected $coursetype = 'attribute';
	/**
	* Ritorna il livello a cui può essere studiato il corso
	* @param obj $char Character_Model
	* @return int Livello a cui si può studiare il corso
	*/
	
	public function getLevel( $char )
	{
		return $char -> get_attribute( 'cost', false) + 1 ;		
	}
	
	
	/**
	* Complete il corso
	* @param obj $char Character_Model
	* @return none
	*/
	
	public function completeCourse( $char ) 
	{
		
		$oldvalue = $char -> cost;
		$newvalue = min (20, $char -> cost + 1) ;
		$char -> cost = $newvalue;
		$increasedattr = 'create_charcost';				
				
		if ( $char -> cost == 20 ) 
			Model_Achievement::compute_achievement ( 'stat_cost', 20, $char -> id );
		
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
