<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CourseFactory
{
	/**
	* Istanzia una classe Course
	* @param str $name Nome del corso
	* @return obj $class
	*/
	
	public function create($tag)
	{
		    
		$class = "Course_" . ucfirst($tag) . "_Model";		
		kohana::log('debug', "-> Factory: Creating class : [{$class}]");
        
		if (class_exists($class)) {
      return new $class( $tag );
    }
    else {
			throw new Exception("-> Course Factory: Invalid course class given [{$tag}].");
    }		
		
	}
	
}