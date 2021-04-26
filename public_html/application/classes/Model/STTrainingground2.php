<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_STTrainingground2STTrainingground1 extends Model_Structure_STTrainingground1
{

	const LEVEL = 0;
	const PAPERS_LESSONHOURS = 3;		
	
	public function init()
	{
		parent::init();
		$this -> setCurrentLevel(2);			
	}
		
	// Funzione che costruisce i links relativi
	// @output: stringa contenente i links relativi a questa struttura
	
	public function build_common_links( $structure, $bonus = false )
	{
		$links = parent::build_common_links( $structure );
				
		return $links;
	}
			
}
