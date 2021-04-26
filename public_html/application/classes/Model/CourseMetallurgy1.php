<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Metallurgy1Course extends Model_Course
{
	
	protected $coursetype = 'skill';
	protected $linkedskill = 'recuperateiron';
	
	/**
	* Ritorna il livello a cui può essere studiato il corso
	* @param obj $char Character_Model
	* @return int Livello a cui si può studiare il corso
	*/
	
	public function getLevel( $char )
	{
		return 1;
	}
		
	/**
	* Complete il corso
	* @param obj $char Character_Model
	* @return none
	*/
	
	public function completeCourse( $char ) 
	{
		
		$skill = Model_SkillFactory::create('recuperateiron');
		$skill -> add( $char );
		
		Model_CharacterEvent::addrecord(
			$char -> id,
			'normal',  
			'__events.coursecompletedskill'.';__' . 'structures.course_' . $this -> getTag() . '_name',
			'evidence'
			);
			
	}

	
}
