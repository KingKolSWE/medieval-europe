<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_JQCallback extends Controller_Template
{		
	
	public $template = 'template/blank';	
	
	public function savefacebookinvite()
	{
		
		if ( request::is_ajax() )
		{
				$this -> auto_render = false;	
				$char = Model_Character::get_info( Session::instance()->get('char_id') );
				//KO7::$log->add(KO7_Log::DEBUG, "-> Request {$this -> request -> post('request')}");
				//KO7::$log->add(KO7_Log::DEBUG, kohana::debug($this -> request -> post('to')));
				$targets = $this -> request -> post('to');
				foreach ($targets as $key => $value)
				{
					$f = new Model_FacebookInviterequest();
					$f -> request_id = $this -> request -> post('request');
					$f -> user_id = $char -> user_id;
					$f -> friend_id = $value;
					$f -> status = '_new';
					$f -> timestamp = time();
					$f -> save();
				}
				
				echo "OK";
		}
		
	}
	
	
	public function disableregion()
	{
		
		if ( request::is_ajax() )
		{
			$this -> auto_render = false;	
			KO7::$log->add(KO7_Log::DEBUG, "-> Disabling {$this -> request -> post('region_id')}");
			$region = ORM::factory('region', $this -> request -> post('region_id')) ;
			KO7::$log->add(KO7_Log::DEBUG, $region -> status);
			if ( $region -> status == 'disabled')
				$newstatus = 'enabled';
			else
				$newstatus = 'disabled';
			
			Database::instance()->query("update regions set status='{$newstatus}' where id = {$this -> request -> post('region_id')}");			
			Model_MyCache::delete('-cfg-regions-byid');
			echo $newstatus;
		}
	}
	
	
	/*
	* 
	*/
	
	public function loadtranslation()
	{
		
		if ( request::is_ajax() )
		{
			
			$this -> auto_render = false;
			
			$excluded_files = array ( 
				'core.php', 
				'database.php',
				'form_errors.php', 
				'tos.php',
			); 
			
			KO7::$log->add(KO7_Log::DEBUG, '-> Language: ' . $this -> request -> post('language'));				
			KO7::$log->add(KO7_Log::DEBUG, '-> Search Term: ' . $this -> request -> post('searchterm'));			
			$i18n_directory = $_SERVER['DOCUMENT_ROOT'] . url::base() . "/application/i18n/{$this -> request -> post('language')}" ;
			KO7::$log->add(KO7_Log::DEBUG, "-> Directory: {$i18n_directory}");
			$handle = dir( $i18n_directory ) ;
			$x = array();
			while (false !== ($entry = $handle -> read()) and !in_array( $entry, $excluded_files)) 
			{		
				// parserizzo solo il file .php
				if ( strstr( $entry, '.php' ) != false)
				{
						KO7::$log->add(KO7_Log::DEBUG, "-> File: " . $i18n_directory. "/" . $entry);
						include( $i18n_directory . '/' . $entry );
						
						foreach ( $lang as $key => $translation )
						{							
							KO7::$log->add(KO7_Log::DEBUG, "-> Searching for [{$this -> request -> post('searchterm')}] in {$translation}");
							if ( strstr( $translation, $this -> request -> post('searchterm') ) )							
							{
								$x[$entry][$key] = $translation;
								break;
							}
						}
		
				}
							
				echo json_encode( $x );
			}		
		}
	}
	
	/*
	 * Genera un nome in base alla cultura
	 * @param  none
	 * @return none
	 */
	
	public function generatename( )
	{		
		kohana::log( 'debug', '-> Called generatename' ); 
		require_once( "application/libraries/vendors/NGP/NGP.php" ); 

		if ( request::is_ajax() )
		{
			$this -> auto_render = false;
			$ngp = new NGP();
			
			kohana::log( 'debug', '-> generating random names...' . 
				$this -> request -> post('charculture') . 
				$this -> request -> post('charsex')); 
			
			$rndname = $ngp -> generate_name( 
				$this -> request -> post('charculture'), $this -> request -> post('charsex' )) ;									

			echo json_encode( $rndname );
		}		
		
	}
	
	/*
	* Carica le informazioni del Regno
	* @param kingdom_id ID regno
	* @return none
	*/

	public function get_kingdominfo( )
	{
		KO7::$log->add(KO7_Log::DEBUG, 'Kingdom id: ' . $this -> request -> post('id') ); 
		if ( request::is_ajax() )
		{
			$this -> auto_render = false;
			$kingdom = ORM::factory('kingdom', $this -> request -> post('id') );
			$info = $kingdom -> get_info();
			$info['kingmessage'] = Model_Utility::bbcode($info['kingmessage']);
			echo json_encode( $info );
      	}		
	}
	
	/*
	 * Trova tutti i giocatori
	*/
	
	public function listallchars( $parameter = null )
	{
		
		$this -> auto_render = false;		
		$name = $this -> input -> get('term');
		$data = array();
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		KO7::$log->add(KO7_Log::DEBUG, "Parameter: $parameter");
		KO7::$log->add(KO7_Log::DEBUG, "Name: $name");
		
		if ( is_null($parameter) )
		{
			$sql = "
			select name 
			from characters 
			where name like ?
      and type != 'npc' 			
			";	
			
			$res = Database::instance() -> query( $sql, '%'. $name . '%' );
		}
		elseif ($parameter == 'M' or $parameter == 'F' )
		{
			$sql = "
			select name 
			from characters 
			where name 
			like ? 
			and sex = ?
			and type != 'npc' ";	
			
			$res = Database::instance() -> query( $sql, '%'. $name . '%', $parameter );
		}
		elseif ($parameter == 'captains')
		{
			$sql = "select c.* from characters c, character_roles r
				where c.name like ?
				and   c.id = r.character_id
				and   r.tag = 'sheriff'
				and   r.current = 1 
				and   c.type != 'npc' 
				and   r.region_id in
				( select id from regions where kingdom_id = " . $char -> region -> kingdom -> id . ") limit 150 "; 
				
			$res = Database::instance() -> query( $sql, '%'. $name . '%' );	
			
		}
		elseif ($parameter == 'inregion' )
		{
			$sql = "
			select name 
			from characters where name like ?
			and  type != 'npc' 
			and  position_id = " . $char -> position_id ;	
			$res = Database::instance() -> query( $sql, '%'. $name . '%' );	
		}
		elseif ($parameter == 'appointable' )
		{
			
			$sql = "
			select c.name, c.id from 
			characters c, regions r
			where c.region_id in ( select id from regions where kingdom_id =  " . $char -> region -> kingdom -> id .  "
			) 
			and c.name like ?
			and c.region_id = r.id 
			and c.type != 'npc' 
			and c.id not in
			(
				select character_id from character_roles where current = 1
				and gdr = false 
			)			
		";		
			$res = Database::instance() -> query( $sql, '%'. $name . '%' );
		}
		
		foreach ( $res as $row )
			$data[] = $row -> name;			
		
		echo json_encode( $data );
					
	}
	
	public function get_servertime()
	{
		$this -> auto_render = false;		
		$now = new DateTime(); 
		echo $now -> format("M j, Y H:i:s O")."\n"; 
	}
	
	/**
	* Trasforma il testo in bbcode
	*/
	
	public function bbcodepreview()
	{		
		$this -> auto_render = false;				
		$preview = Model_Utility::bbcode( $this -> request -> post('text' ) );
		echo $preview;
	}
	
	/*
	* Trova tutte le regioni	
	*/
	
	public function listallregions( $category = 'all', $returnid = false )
	{
		
		$this -> auto_render = false;					
		$criteria = $this -> input -> get('term');				
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		$data = array();
		
		if ($category == 'all')
			$sql = "
			SELECT *
			FROM regions
			WHERE name like ?
			AND   status != 'disabled' 
			";
		
		if ($category == 'attackable')
			$sql = "
			SELECT *
			FROM regions
			where name like ?
			and   kingdom_id !=
			( select id from kingdoms_v where name = 'kingdoms.kingdom-independent' )
			and kingdom_id != " . $char -> region -> kingdom -> id . "
			and type != 'sea' 
			AND   status != 'disabled' 
			";
		
		if ($category == 'independent')
			$sql = "
			SELECT *
			FROM regions
			where name like ?
			and   kingdom_id =
			( select id from kingdoms_v where name = 'kingdoms.kingdom-independent' )			
			AND   status != 'disabled' 
			and type != 'sea' ";

		if ($category == 'kingdom')
			$sql = "
			SELECT *
			FROM regions
			where name like ?
			and   kingdom_id = {$char -> region -> kingdom -> id}
			AND   status != 'disabled' 
			and type != 'sea' ";		
				
		
		$res = Database::instance() -> query( $sql, '%'. 'regions.' . $criteria . '%' );
		foreach ( $res as $row )
		{
			$data[$row -> id]['label']	= __($row -> name);
			$data[$row -> id]['value']	= __($row -> name);
			$data[$row -> id]['id']	= $row -> id;
		}			
		
		echo json_encode( $data );		
		
	}
  
	/**
	* Invoca il parser bbcode
	* @param text text da parsare
	* @return html
	*/
	
	public function callbbcodeparser ()
	{
		$text = $this -> input -> get( 'bbcode' ); 
		$this -> auto_render = false;		
		$convesion = Model_Utility::bbcode( $text );
		echo Model_Utility::bbcode( $text );
	}
	
	/*
	* Carica le informazioni di un char
	* @param none
	* @return html
	*/
	
	public function loadcharacterinfo( )
	{
		
		$html="";
		
		$this -> auto_render = false;	
		$char = Model_Character::get_info( $this -> request -> post('characterid'));
		$viewingchar =  Model_Character::get_info( Session::instance()->get('char_id') );
		//$currentregion = ORM::factory('region', $this -> request -> post('regionid'));
		
		if ($char -> type == 'npc' )
		{
			$html = "<table>";
			$html .= "<tr>
				<td width='10%' valign='top' rowspan='4' style='border-right:1px solid #999'>" .							
				html::image('media/images/npc/' . $char -> npctag . '_s.png') . 
				'</td>' .
				"<td style='vertical-align:top'>" . 
					html::anchor('character/publicprofile/'. $char -> id, 
					__('character.profile'), 
						array('target' => '_new')) . "<br/>" .
					html::anchor("character/attackchar/{$viewingchar->id}/{$char->id}",	__('charactions.attack') ) . '<br/>';
			if ($viewingchar -> id == 1 )
				$html .= html::anchor('/character/steal/' . $char -> id, __('charactions.steal' ));			
			$html .= "</td></tr>";
			$html .= "</table>";
			$html .= "<br/>";
		}
		else
		{
			$online = Model_Character::is_online($char -> id);
			$viewingcharrole = $viewingchar -> get_current_role();
			$viewedcharrole = $char -> get_current_role();
									
			$html = "<table>";			
				
			$html .= "<tr><td width='10%' valign='top' rowspan='10' style='border-right:1px solid #999'>" .
							Model_Character::display_avatar( $char -> id, $size = 's', $class = 'border' ). "</td>" .
							"<td style='vertical-align:top'>";
						
			if (!is_null($viewedcharrole))
				$html .= $char-> get_rolename( true ) . "<br/>";
			
			$html .= "<span class='value'>" . __('religion.church-' . $char -> church -> name) . "</span><br/>" .	
				"<span class='value'>" . __($char -> region -> kingdom -> name) . "</br/>";
			if ( $online )
				$html .= "<span style='color:#009933;font-weight:bold'>Online</span>";
			else
				$html .=  "<span style='color:#cc0000;font-weight:bold'>Offline</span>";
			$html .="<br/>";
			
			$html .= 
				"</span>" .	__('global.lastlogin') . ": <span class='value'>" . Model_Utility::format_date($char->user->last_login) . '</span><br/><br/>'.
				html::anchor('message/write/0/new/'.$char->id, __('global.write'), array('target' => '_new')) . '<br/>'.
				html::anchor('character/publicprofile/'.$char->id, __('character.profile'), array('target' => '_new'));
			
			// Steal
			
			if ($viewingchar -> id == 1 )
				$html .= "<br/>" . html::anchor('/character/steal/' . $char -> id, __('charactions.steal' ));			
			
			// Arrest
			
			if ( $char -> id != $viewingchar -> id 
				and !is_null( $viewingcharrole) 
				and $viewingcharrole -> tag == 'sheriff' 
				and $this -> request -> post('regiontype') != 'sea' )
			{
				$html .= "<br/>" . html::anchor('/barracks/arrest/' . $char -> id, __('structures_barracks.arrest' ));			
			}
			
			// Se il char che visualizza ha un ruolo religioso
			// visualizzo le azioni di cura disponibili
			
			if ( 
				$char -> position_id == $viewingchar -> position_id and
				(
					$viewingchar -> get_attribute( 'intel' ) >= 18 
					or
					$viewingchar -> has_religious_role() == true 
				)			
			)
			{
				
				// Per ogni malattia visualizzo il link corretto
				
				$diseases = $char -> get_diseases();
				if (!is_null($diseases))
				{
					foreach ($diseases as $disease)
					{
						$dinstance = Model_DiseaseFactory::createDisease( $disease -> param1 );
						if ( $dinstance -> get_iscurable() == true )
						// Cura malattia		
						$html .= "<br/>" . html::anchor
						(
							'character/cure/disease/' . $char -> id . '/' . $dinstance -> get_name(), 
							__('charactions.curedisease') .':&nbsp;' . __('character.disease_'. $dinstance -> get_name() )
						);				
					}
					
				}
				
				// Link cura salute
				if ( $char -> health < 100 )				
					$html .= "<br/>" . html::anchor
					(
						'character/cure/health/' . $char -> id, 
						__('charactions.curehealth' )
				
					);
				
				// Link iniziazione
				
				if ( $char -> church -> name == 'nochurch' )
					$html .= "<br/>" . html::anchor
					(
						'character/initiate/' . $char -> id, 
						__('charactions.initiation')
					);
			}
			
			$html .= "</td></tr>";
			$html .= "<table>";
			$html .= "<br/>";
		}
		
		$data['title'] = $char -> name;
		$data['html'] = $html;
		echo json_encode($data);
		
	}
	
	/*
	* Carica le informazioni di una struttura
	* @param none
	* @return str $html
	*/
	
	public function loadstructureinfo( )
	{
		
		$html = '<table>';
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		$structureid = $this -> request -> post( 'structureid' ); 		
		
		$structure = Model_StructureFactory::create( null, $structureid );
		
		//KO7::$log->add(KO7_Log::DEBUG, kohana::debug($structure));
		
		$this -> auto_render = false;	
		
		$workerbonus = Model_Character::get_premiumbonus( $char -> id, 'workerpackage' );
		
		if ($structure -> getSuperType() =='terrain')
			$image = 'terrain_1.jpg';
		else
			$image = $structure -> structure_type -> image;			
		
		$html .= "
			<tr>
			<td rowspan='10' width='20%' valign='top' style = 'border-right:1px solid #999'>" .  html::image(
				'media/images/structures/' . $image,
				array(
					'class' => 'border size75',
					) ) . "</td>
			<td style='vertical-align:top'>";
		
		//KO7::$log->add(KO7_Log::INFO, 'strid: ' . $structure -> structure_type -> name ); 
		//$html .=  "<b>" . __( $structure -> structure_type -> name ). "</b><hr/>";
		
		////////////////////////////////////////////
		// gestione terreno
		////////////////////////////////////////////
		
		if ( $structure -> getSuperType() == 'terrain' )
		{	
			
			$a = Model_CharacterAction::get_pending_action();
			$item_seeded = ORM::factory('cfgitem', $structure -> attribute2 );					
			
			switch ( $structure -> attribute1 ) 
			{
				// o incolto o in fase di semina
				case 0:	
					if ( !is_null( $a ) )
					{
						if ( $a->action=='seed' and $a->param1 == $structure->id ) 
							$html .= __('structures.terrain_attr0_seeding') . "<br/><br/>";												
					}
					else
						$html .= __('structures.terrain_attr0') .  "<br/><br/>";
					break;
				case 1:							
					$html .= sprintf(__('structures.terrain_attr1'), __($item_seeded->name), Model_Utility::countdown ($structure -> attribute3)) . '<br/><br/>';
					break;
				case 2: 
					if ( !is_null( $a ) )
					{
						if ( $a -> action=='harvest' and $a->param1 == $structure->id) 
							$html .= __('structures.terrain_attr2_harvesting') . "<br/><br/>";												
					}
					else
						$html .= sprintf(__('structures.terrain_attr2'), __($item_seeded->name)).  "<br/><br/>";
					break;
			}										
			
		}
		
		// costruzione link comuni		
		$html .= $structure -> build_common_links( $structure, $workerbonus );
		
		// se il char è owner, creiamo gli special link
		if ( $structure -> character_id == $char -> id )
			$html .= $structure -> build_special_links( $structure, $workerbonus );
		else
		{									
			$cannotbemanaged = Model_StructureGrant::get_chargrant( $structure, $char, 'none' );
			if ( 
				$cannotbemanaged == false 
				and 
				$structure -> getSupertype() != 'battlefield' 
			)
			{
				$html .= html::anchor( "/structure/manage/" . $structure -> id, __('global.manage'), array('class' => 'st_special_command'));							
			}
				
		}
		
		$html .= "</td></tr></table>";
		$data['title'] = $structure -> getName();
		$data['html'] = $html;
		echo json_encode($data);
	}
	
	/*
	* Carica le informazioni di un oggetto
	* @param none
	* @return html
	*/
	
	public function loaditeminfo( )
	{
		$html="";
		
		$this -> auto_render = false;	
		KO7::$log->add(KO7_Log::DEBUG, '-> Querying item: ' . $this -> request -> post('itemid') );
		
		$item = ORM::factory('item')
			-> where ( array( 
				'id' => $this -> request -> post('itemid'),
				'region_id' => $this -> request -> post('regionid'),
			)			
		) -> find();
		
		
		// se l' item non esiste piu...
		
		if ($item -> loaded == false )
		{
			$html .= "Questo oggetto non esiste più; probabilmente è stato preso da un passante.";
		}
		else
		{
			$html = "<table>";
			$html .= "<tr><td rowspan='3' style='border-right:1px solid #999'>" . html::image(
				'media/images/items/'. $item -> cfgitem -> tag . '.png',
				array('class' => 'border') ) . "</td>
				<td valign='top'>" . __( $item -> cfgitem -> name ) . "<br/>";
			$html .= html::anchor( "/item/takefromground/" . $item -> id, 'Take' ) . " - " ;
			$html .= html::anchor( "/item/takefromground/" . $item -> id . '/3', 'Take 3' ) . " - " ;
			$html .= html::anchor( "/item/takefromground/" . $item -> id . '/999', 'Take All' ) . "</td></tr>";
			$html .= "</table>";
			
		}
		
		$data['title'] = __( $item -> cfgitem -> name );
		$data['html'] = $html;
		echo json_encode($data);
		
	}

		
	/**
	* trova le informazioni diplomatiche per un regno
	*/
	
	public function diplomacyinfo()
	{
		$this -> auto_render = false;				
				
		$diplomacyrelations = Model_DiplomacyRelation::get_diplomacyrelations( $this -> request -> post('kingdom_id') );
		
		$kingdoms = Database::instance() -> query(
			'select k.id, r.name, coords 
			from kingdoms_v k, regions r
			where r.kingdom_id = k.id 
			and   r.capital = true') -> as_array();
		
		foreach( $kingdoms as $k )
		{
			list( $kingdomscoords[$k->id]['coordx'], $kingdomscoords[$k->id]['coordy'] ) = explode ( '.' , $k -> coords );
			$kingdomscoords[$k->id]['name'] = $k -> name ;
		}
		//var_dump($kingdomscoords);exit;
		
		$k = 0;
		foreach ( $diplomacyrelations as $dr )
		{	
			if ( $dr -> kingdom1_id == $this -> request -> post('kingdom_id') )
				$targetkingdom = $dr -> kingdom2_id ;
			else
				$targetkingdom = $dr -> kingdom1_id ;
			
			$info['type'] = $dr -> type;
			$info['name'] = $kingdomscoords[$targetkingdom]['name'];
			$info['coordx'] = $kingdomscoords[$targetkingdom]['coordx'];
			$info['coordy'] = $kingdomscoords[$targetkingdom]['coordy'];			
			$data[] = $info;
		}
		echo json_encode( $data ) ;
	
	}
	
	/*
	* Returns bonus price
	*/
	
	function getinfo()
	{
		
		$this -> auto_render = false;				
		$pb = Model_PremiumBonusFactory::create( $this -> request -> post('name') );
		$info = $pb -> get_info();
		$countdown = Model_Utility::secs2hmstostring($info['enddate']-time());
		$data = array(
			'id' => $this -> request -> post('name'),
			'originalprice' => $info['cuts'][$this -> request -> post('cut')]['price'],
			'discountedprice' => $info['cuts'][$this -> request -> post('cut')]['discountedprice'],
			'discount' => $info['discount'],
			'timeuntildiscountends' => $countdown
		);
		
		echo json_encode( $data );
		
	}
	
}
