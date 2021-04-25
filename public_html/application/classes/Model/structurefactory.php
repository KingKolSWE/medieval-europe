<?php defined('SYSPATH') OR die('No direct access allowed.');

class StructureFactory_Model
{
	/**
	* Instance a class structure
	* @param str $type type of structure
	* @param int $id ID structure
	* @return obj $type o null;
	*/
	
	public function create($type = null, $id = null)
	{
		
		kohana::log('debug', '----- STRUCTUREFACTORY -----');
		kohana::log('debug', "type: [{$type}], id: [{$id}]");
		
		// if the id is passed, we load the class directly
		if (!is_null($id))
		{
			$structure = ORM::factory('structure', $id );			
			if ($structure -> loaded == false)
				return null;
			$class = 'ST_' . ucfirst( $structure -> structure_type -> type) . '_Model' ;
		}
		elseif (!is_null($type))
		{
			$class = "ST_" . ucfirst($type) . '_Model';
		}
		else
		{
			throw new Exception("-> Structure Factory: Please specify at least one parameter.");
		}
		
		kohana::log('debug', "-> Structure Factory: Creating class : [{$class}], type [{$type}]");
		 
		if (class_exists($class)) {
			if (!is_null($id))
				$instance = ORM::factory( 'st_' . $structure -> structure_type -> type, $id );
			else
			{
				$instance = new $class();
				$structuretype = ORM::factory('structure_type') 
					-> where ( 'type', $type ) -> find();
				$instance -> structure_type_id = $structuretype -> id;
			}
		}	
		else {
			throw new Exception("-> Structure Factory: Invalid class given [($class)].");
		}		
		
		$instance -> init();
		
		return $instance;
	}
	
}
