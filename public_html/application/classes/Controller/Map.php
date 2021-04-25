<?php defined('SYSPATH') OR die('No direct access allowed.');
class Controller_Map extends Controller_Template
{

	public $template = 'template/gamelayout';
	
	/*
	* Visualizza la mappa
	* @param none
	* @return none
	*/
	
	public function view( )
	{	
				
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$sheets  = array('gamelayout' => 'screen', 'map' => 'screen');
		$view = View::factory('map/view');
		$bonuses = Model_Character::get_premiumbonuses( $char -> id ); ;
		
		//////////////////////////////////////////////////////////////////////////////////
		// Controllo il nodo corrente del char. Se la posizione � 0, 
		// significa che il pg sta viaggiando
		//////////////////////////////////////////////////////////////////////////////////
		
		if ( Model_Character::is_traveling( $char -> id ) )
		{
			$current_action = Model_Character::get_currentpendingaction( $char -> id );
			$current_position = ORM::factory('region', $current_action['param2'] );		
			$prev_position = ORM::factory('region', $current_action['param1'] );		
			KO7::$log->add(KO7_Log::DEBUG, "Char is traveling, setting current position to: {$current_position -> name}");
			$travelingtext = __('charactions.travelmessage', 
					__( $prev_position -> name ), __( $current_position -> name ) );
		}
		else
		{
			$travelingtext = '';
			$current_position = ORM::factory('region', $char -> position_id );		
			KO7::$log->add(KO7_Log::DEBUG, "Char is NOT traveling, setting current position to: {$current_position -> name}");
		}
		
		///////////////////////////////////////////////////
		// tiro su tutte le informazioni delle regioni 
		//////////////////////////////////////////////////
		Database::instance() -> query("select '--regions_byid--'");
		$regions = Configuration_Model::get_cfg_regions_byid();		
		Database::instance() -> query("select '--regions_withstructures--'");
		$regions_with_structures = Configuration_model::get_regions_structures();
		Database::instance() -> query("select '--regions_resources--'");
		$resources = Configuration_Model::get_resources_all_regions();
		Database::instance() -> query("select '--diplomaticrelations--'");
		$diplomacy = Configuration_Model::get_cfg_diplomacyrelations();		
		Database::instance() -> query("select '--kingdoms--'");
		$kingdoms = Configuration_Model::getcfg_kingdoms();
		Database::instance() -> query("select '--regionpaths--'");
		$region_paths = Configuration_Model::get_cfg_regions_paths2();		

		//$regions_str = print_r($regions, true);
		//KO7::$log->add(KO7_Log::INFO, $regions_str);
				
		// La chiesa del char ha il dogma per vedere le risorse?
		
		$hasdogma_resourceextractionblessing = 
			Church_Model::has_dogma_bonus( $char -> church_id, 'resourceextractionblessing');
		
		$afpachievement = 
			Model_Character::get_achievement( $char -> id, 'stat_fpcontribution' );
			
		foreach ($regions as $region_id => &$data )
		{
			
			// translate terms			
			
			$data['name'] = __($data['name']);
			$data['kingdom_name'] = __($data['kingdom_name']);
			$data['geographytext'] = __('global.type') . ": <span class='valuelight'>" . __( 'regioninfo.' . $data['geography'] ) . "</span>";
			$data['climatext'] = __('global.climate') . ": <span class='valuelight'>" . __( 
			'regioninfo.climate_' . $data['clima'] ) . "</span>";
			$data['infolink'] = html::anchor( 'region/info/' .$data['id'], 
						__('global.info'),
						array(							
							'target' => 'new' 
						));
			$data['lawslink'] = html::anchor( 
					'region/info_laws/'.$data['id'],
						__('regionview.submenu_laws'),
						array(
							'target' => 'new' 
						));
			
			// Carico informazioni su risorse (se la regione le ha)
			
			if (isset( $resources[$region_id] ) )
			{
				
				// Visibilit� risorse
			
				if (
					$hasdogma_resourceextractionblessing 
					and 
					!is_null($afpachievement) 
					and 
					in_array($afpachievement['stars'], array(3,4,5)) 
				)
				{		
					$data['canseeavailability'] = true;
				}
				else
					$data['canseeavailability'] = false;

				$_info=array();
				
				foreach ($resources[$region_id]['resources'] as $resourcename => $info )
				{
					$_info[$resourcename]['name'] = __('items.' . $resourcename . '_name');
					$_info[$resourcename]['availability'] = '(' . round($info['current']/$info['max'],2)*100 . '%)';
				}
							
				$data['resources']['info'] = $_info;
			}
		}
		
		// Carico Lista Regni
		
		$kingdomlist['']=__('map.findakingdom');
		$kingdomlist['all']=__('map.allkingdoms');
		foreach($kingdoms as $kingdomname => $kingdomdata)		
			$kingdomlist[$kingdomdata -> id] = __($kingdomname);
				
		//////////////////////////////////////////////////////////////////
		// Elaboro Regioni Adiacenti
		//////////////////////////////////////////////////////////////////
		
		$adjacentregions = $region_paths[$current_position -> id];
		
		// get travel info only for adjacent regions.
		
		$par['bonuses'] = $bonuses;
		$par['weightinexcess'] = 	$char -> get_weightinexcess(); 
		$par['hasshoes'] = $char -> get_bodypart_item ("feet"); 
		$par['char'] = $char;
		
		foreach ($adjacentregions as $adjacentregionid => $info)
		{
			//KO7::$log->add(KO7_Log::INFO, '-> Processing ' . $current_position -> name . '  -> adjacent region: ' . $info['data'] -> name2);

			$par['type'] = $info['data'] -> type;				
			$par['time'] = $info['data'] -> time;
			$par['sourcename'] = $info['data'] -> name1;
			$par['destname'] = $info['data'] -> name2;			
			$travelinfo  = Region_Path_Model::get_travelinfo( $par );
			$linktravel = false;
			$linktraveltext = __('global.travel');
			$linktravelaction = 'notset';
			
			// Travel from sea to land, diplay link SAIL only if there is an harbor

			//KO7::$log->add(KO7_Log::INFO, 'cp type: [' . $current_position -> type . '] info type:[' . $info['data'] -> type . ']' );	
			
			if ( $current_position -> type == "sea" and in_array( $info['data'] -> type, array('mixed', 'sea')) )
				//KO7::$log->add(KO7_Log::INFO, '-> checking if ' . $info['data'] -> name2 . ' has harbor');
				if ( array_key_exists( $info['data'] -> id2, $regions_with_structures['harbor'] ) )
				{
					//KO7::$log->add(KO7_Log::INFO, '-> ' . $info['data'] -> name2 . ' has harbor.');
					$linktravel = true;
					$linktravelaction = 'character/sail/' . $info['data'] -> id2;
				}

			// Travel from land to sea, display link SAIL only if there is an harbor
								
			if ( $current_position -> type == "land" and in_array( $info['data'] -> type, array('mixed', 'sea')) )
			{
				KO7::$log->add(KO7_Log::DEBUG, $current_position -> name . '-> adding sail link');
				if ( !is_null($regions_with_structures['harbor']) && array_key_exists( $current_position -> id, $regions_with_structures['harbor'] ) )
				{
					$linktravel = true;
					$linktravelaction = 'character/sail/' . $info['data'] -> id2;
				}
			}
			
			if ( $current_position -> type == 'fastsea' and $info['data'] -> type == 'fastsea' ) 
				if ( Model_Character::get_premiumbonus( $char -> id, 'travelerpackage' ) !== false )
				{
					$linktravel = true;
					$linktravelaction = 'character/sail/' . $info['data'] -> id2;
				}
			
			if ( $current_position -> type == 'sea' and $info['data'] -> type == 'sea' ) 
			{
				$linktravel = true;
				$linktravelaction = 'character/sail/' . $info['data'] -> id2;
			}

			if ( $current_position -> type == 'sea' and $info['data'] -> type == 'fastsea' ) 
			{
				$linktravel = true;
				$linktravelaction = 'character/sail/' . $info['data'] -> id2;
			}

			if ( $current_position -> type == 'fastsea' and $info['data'] -> type == 'sea' ) 
			{
				$linktravel = true;
				$linktravelaction = 'character/sail/' . $info['data'] -> id2;
			}
		
			// Travel land to land. If the target region is fastland check if the char has
			// travel bonus
			
			if ( $current_position -> type == "land" && $info['data'] -> type == "fastland")
				if ( Model_Character::get_premiumbonus( $char -> id, 'travelerpackage' ) !== false )
				{
					$linktravel = true;
					$linktravelaction = 'character/move/' . $info['data'] -> id2;
				}
			
			if ( $current_position -> type == "land" && $info['data'] -> type == "land")
			{
				$linktravel = true;
				$linktravelaction = 'character/move/' . $info['data'] -> id2;
			}
			
			// If region is disabled, it's not possible to travel to it.
			$regions[$adjacentregionid]['travelinfo'] = $travelinfo;					
			if ($info['data'] -> status2 == 'disabled' )
				$linktravel = false;			
			
			
			
			// check for battlefield
			
			if ( 
				isset($regions_with_structures['battlefield'])
					and
				array_key_exists( $adjacentregionid, $regions_with_structures['battlefield'] ) )
				{
					$linktravelaction .= '/1';
					$linktraveltext = __('global.traveltobattlefield');
				}
				
			$regions[$adjacentregionid]['travelinfo']['linktravel'] = $linktravel;
			$regions[$adjacentregionid]['travelinfo']['linktravelaction'] = html::anchor( $linktravelaction, $linktraveltext,
					array('class' => 'st_common_command'));
		
			
			
			// Costruisco array con solo le regioni collegate all'attuale
			// posizione del character
			
			$linked_regions[$adjacentregionid] = $regions[$adjacentregionid];			
			
			//var_dump($linked_regions); exit;
		
		}
		
		$view -> travelingtext = $travelingtext;
		$view -> kingdomlist = $kingdomlist;
		$view -> character = $char;			
		$view -> current_position = $current_position;
		$view -> linked_regions = $linked_regions;		
		$view -> jsonresources = json_encode($resources, JSON_FORCE_OBJECT);					
		$view -> jsondiplomacy = json_encode($diplomacy, JSON_FORCE_OBJECT);		
		$view -> jsonregions = json_encode($regions, JSON_FORCE_OBJECT);	
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		
	}
}
