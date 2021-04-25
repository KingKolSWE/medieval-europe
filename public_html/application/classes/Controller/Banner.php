<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Banner extends Controller_Template
{
	// Imposto il nome del template da usare
	
	public $template = 'template/blank';
	
	function display( $char_id )
	{				
		
		$view = View::factory ('user/banner');		
		$img = Model_Utility::create_banner( $char_id );
		$view -> img = $img;
		$this -> template -> content = $view;	
	}
		
}
