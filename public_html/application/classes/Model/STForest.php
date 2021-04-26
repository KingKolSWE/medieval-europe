<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Structure_STForest extends Model_Structure
{
	public function init()
	{		
		$this -> setIsbuyable(false);
		$this -> setIssellable(false);	
		$this -> setWikilink('The_Forest');				
	}
	
	// Function that builds the common links related to the structure
	// @output: content string i links relative to that structure
	public function build_common_links( $structure, $bonus = false )
	{
		$links = parent::build_common_links( $structure );
		
		$links .= html::anchor( "/structure/info/" . $structure -> id, Kohana::lang('structures_actions.global_info'), 
			array('class' => 'st_common_command')) . "<br/>" ;			

		// Common actions accessible to all chars
		$links .= html::anchor( "/forest/getwood/" . $structure -> id, Kohana::lang('structures_actions.forest_getwood'),
		array('title' => Kohana::lang('structures_actions.forest_getwood_info'), 'class' => 'st_common_command',
		'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')') );
		
		if ( $bonus !== false )
		{
		   	$links .= ' - '.html::anchor( "/forest/getwood/".$structure->id."/2", 'x2',
		    array('title' => Kohana::lang('structures_actions.forest_getwood').' (x2)', 'class' => 'st_common_command',
					'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')') );
		    
		   	$links .= ' - '.html::anchor( "/forest/getwood/".$structure->id."/3", 'x3',
		    array('title' => Kohana::lang('structures_actions.forest_getwood').' (x3)', 'class' => 'st_common_command',
					'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')') );
		}
				
		$links .= "<br/>";

		$links .= html::anchor( "/forest/searchplant/" . $structure -> id, Kohana::lang('structures_actions.forest_searchplant'),
		array('title' => Kohana::lang('structures_actions.forest_searchplant_info'), 'class' => 'st_common_command',
			'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')') );

		if ( $bonus !== false )
		{
		   	$links .= ' - '.html::anchor( "/forest/searchplant/".$structure->id."/2", 'x2',
		    array('title' => Kohana::lang('structures_actions.forest_searchplant').' (x2)', 'class' => 'st_common_command',
					'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')') );
		    
		   	$links .= ' - '.html::anchor( "/forest/searchplant/".$structure->id."/3", 'x3',
		    array('title' => Kohana::lang('structures_actions.forest_searchplant').' (x3)', 'class' => 'st_common_command',
				'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')') );
		}

		return $links;
	}

	// Function that builds the special links related to the structure
	// @output: content string i links relative to that structure
	public function build_special_links( $structure, $bonus = false )
	{
		$links = null;
		return $links;
	}
}