<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Buildingsite extends Controller_Template
{
	// Imposto il nome del template da usare
	public $template = 'template/gamelayout';

	
	/*
	* Costruisce una struttura
	* @param int $structure_id ID Struttura
	*/
	
	function build( $structure_id )
	{
		
		$view = View::factory ('structure/build');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		
		if ( !$_POST )
		{
			// trovo il progetto del nodo che ha come target la struttura corrente
			
			$structure = Model_StructureFactory::create( null, $structure_id );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message,
				'public', 'manage' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
		
			$project = ORM::factory('kingdomproject') ->where ( array( 'structure_id' => $structure_id ) ) -> find() ;
		
		
		}
		else
		{
		
			$structure = Model_StructureFactory::create( null, $this -> request -> post('structure_id') );
			if ( ! $structure -> allowedaccess( $character, $structure -> getParentType(), $message,
				'public', 'manage' ) )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");
				HTTP::redirect('region/view/');
			}
			$project = ORM::factory('kingdomproject') ->where ( array( 'structure_id' => $structure->id ) ) -> find() ;
			$info = $project -> get_info();	

			$o = Model_CharacterAction::factory("workonproject");
			$par[0] = $character;
			$par[1] = $structure;				
			$par[2] = $this->request->post('hours');
			$par[3] = $project;
			$par[4] = $this->request->post('workingtype');
		
			$rec = $o->do_action( $par, $message );			

			if ( $rec )
			{
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				HTTP::redirect('/region/view/' . $character -> position_id );
				return;
				
			}
			else
			{					
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");		
			}			
		}
		
		$info = $project -> get_info();	
		
		$view -> workedhours = $info['workedhours'];
		$view -> totalhours = $info['totalhours'];
		$view -> workedhours_percentage = $info['workedhours_percentage'];
		$view -> hourlywage = $structure -> hourlywage;
		$view -> structure = $structure;
		$view -> character = $character;
		// appena un giocatore apre il popup si determina se la struttura è costruibile.
		$view -> isbuildable = $project -> is_buildable();
		$view -> project = $project ;
		$view -> info = $info;
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;

	}

}