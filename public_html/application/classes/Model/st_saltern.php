<?php defined('SYSPATH') OR die('No direct access allowed.');

class ST_Saltern_Model extends Structure_Model
{
	public function init()
	{		
		$this -> setIsbuyable(false);
		$this -> setIssellable(false);				
		$this -> setWikilink('The_Mine');		
	}
	

	// Funzione che costruisce i links comuni relativi alla struttura
	// @output: stringa contenente i links relativi a questa struttura
	public function build_common_links( $structure, $bonus = false )
	{
		$links = parent::build_common_links( $structure );
		
		$links .= html::anchor( "/structure/info/" . $structure -> id, Kohana::lang('structures_actions.global_info'), 
			array('class' => 'st_common_command')) . "<br/>" ;		
		$links .= '<br/>';

		// Azioni comuni accessibili a tutti i chars
		$links .= html::anchor( "/saltern/dig/" . $structure -> id, Kohana::lang('structures_saltern.saltern_dig'), array( 'class' => 'st_common_command',
			'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')' ));

		if ( $bonus !== false )
		{
		   	$links .= ' - '.html::anchor( "/saltern/dig/".$structure->id."/2", 'x2',
		    array('title' => Kohana::lang('structures_saltern.saltern_dig').' (x2)', 'class' => 'st_common_command',
					'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')' ));
		    
		   	$links .= ' - '.html::anchor( "/saltern/dig/".$structure->id."/3", 'x3',
		    array('title' => Kohana::lang('structures_saltern.saltern_dig').' (x3)', 'class' => 'st_common_command',
					'onclick' => 'return confirm(\''.kohana::lang('global.confirm_operation').'\')' ));
		}
				
		$links .= "<br/>";

		return $links;
	}

	// Funzione che costruisce i links speciali relativi alla struttura
	// @output: stringa contenente i links relativi a questa struttura
	public function build_special_links( $structure, $bonus = false )
	{
		$links = '';
		return $links;
	}
}
