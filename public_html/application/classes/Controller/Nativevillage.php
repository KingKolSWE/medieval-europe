<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Nativevillage extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';
	
	
	/**
	* Visualizza una lista di possibili candidati alla nomina a Vassallo.
	*/
	
	function attack( $structure_id = null )
	{			
		
		$view = View::factory ( 'nativevillage/attack' );
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen', 'battlereport' => 'screen');
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$groups = Group_Model::get_char_groups( $character, 'military' ); 
		
		if ( isset($this -> disabledmodules['declarehostileaction']) )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">". __('charactions.error-moduleisdisabled') . "</div>");						
			HTTP::redirect('region/view/' );
		}
		
		if ( ! $_POST )
		{
			$structure = StructureFactory_Model::create( null, $structure_id );
		}
		else
		{
			$structure = StructureFactory_Model::create( null, $this -> request -> post('structure_id') );
			$par[0] = ORM::factory('group', $this->request->post('attackwithgroup') );
			$par[1] = $structure -> region ; 			
			
			$ca = Character_Action_Model::factory("attackir");		

			if ( $ca -> do_action( $par,  $message ) )
			{ 
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>"); 	
				HTTP::redirect('region/view'); 			
				return;
			}	
			else	
			{ 				
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 	
				$view -> structure = $structure ; 
				$this -> template -> content = $view;
			}
			
		}
		
		$view->groups = $groups;
		$view->structure = $structure;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
				
	}

}
