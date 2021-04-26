<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Admin extends Controller_Template
{
	// Imposto il nome del template da usare
	
	public $template = 'template/gamelayout';
	
	// Console amministratore
	
	public function console()
	{
		
		if ( !Auth::instance() -> logged_in('admin') and !Auth::instance()->logged_in('staff'))		
			HTTP::redirect('/user/login');

		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory( 'admin/console');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');										 
		$message = '';
		
		if ( !$_POST )
			;
		else {	
			
			if ( $this -> request-> post('skin') != '' )
			{
				Model_Character::modify_stat_d( $character -> id,
					'skin', 
					0,
					null,
					null,
					true,
					$this -> request -> post('skin')
				);
			}
		
			if ( $this -> request -> post('unblockactions') != '' )
			{
				$rc = $this -> unblockactions($message );	
				if ( $rc == false )
					{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
				else  
					{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");}	
			}
			
			if ( $this -> request-> post('bancharactergame') != '' )
			{
				$rc = $this -> bancharacter(
					$this -> request -> post('charactername'), 
					'game',
					$this -> request -> post('bandate'), 
					$this -> request -> post('banreason'), 
					$message);
				
				if ( $rc == false )
					{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
				else  
					{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");}	
			}
			
			if ( $this -> request-> post('resetpassword') != '' )
			{
				
				$character = ORM::factory('character') 
					-> where( 'name', $this -> request -> post('charactername'))
					-> find();
				
				if ( $character -> loaded )
				{
					$character -> user -> password = 1234;
					$character -> user -> save();
				}
				
				
				Session::instance()->set('user_message', "<div class=\"info_msg\">Password per {$character->name} (user: {$character->user->username}) resettata a 1234.</div>");
			}
			
			if ( $this -> request-> post('bancharacterchat') != '' )
			{
				$rc = $this -> bancharacter(
					$this -> request -> post('charactername'), 
					'chat',
					$this -> request -> post('bandate'), 
					$this -> request -> post('banreason'), 
					$message);
				
				if ( $rc == false )
					{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
				else  
					{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");}	
			}
			
			if ( $this -> request-> post('kill') != '' )
			{
				$rc = $this -> killcharacter($this -> request -> post('character'), $message );	
				if ( $rc == false )
					{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
				else  
					{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");}	
			}
			
			if ( $this -> request-> post('restorechar') != '' )
			{
				
				$rc = Admin_Model::restorechar(
					$this -> request -> post('charactername'), 
					$this -> request -> post('ispaid'), 
					$this -> request -> post('anonymize'), 
					$this -> request -> post('newname'), 
					$this -> request -> post('regionname'), 
					$message );
									
				if ( $rc == false )
					{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
				else  
					{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");}	
			}
			
			if ( $this -> request-> post('changename') != '' )
			{
				$rc = $this -> changecharname(
					$this -> request -> post('oldcharactername'), 
					$this -> request -> post('newcharactername'), $message );	
				
				if ( $rc == false )
					{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
				else  
					{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");}	
			}
			
			if ( $this -> request-> post('changeemail') != '' )
			{
				$rc = $this -> changecharemail(
					$this -> request -> post('charactername'), 
					$this -> request -> post('newemail'), $message );	
				
				if ( $rc == false )
					{Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>");}
				else  
					{Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");}
			}					
			
		}
		
		$lnkmenu = Admin_Model::get_horizontalmenu('console');		
		$subm->submenu = $lnkmenu;
		$view->submenu = $subm;		
		$this->template -> content = $view;
		$this->template -> sheets = $sheets;	
	
	}
		
	
	/**
	* Sblocca azioni bloccate
	* @param message messaggio di ritorno
	* @return false o true
	*/
	
	function unblockactions( &$message )
	{
		$message = "Azioni sbloccate.";
		
		if (!Auth::instance()->logged_in('admin'))
		{
			$message = __('global.operation_not_allowed' );
			return false;
		}
				
		Database::instance() -> query(Database::UPDATE, "
			update character_actions set keylock = null 
			where keylock is not null 
			and status = 'running'
			and character_id = character_id "
		);
		
		return true;
	}

	function multicheck( )
	{
		$view = View::factory( 'admin/multicheck');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');			
		$characters = array();
		
		if (!Auth::instance()->logged_in('admin') and !Auth::instance()->logged_in('staff'))
		{
			$message = __('global.operation_not_allowed' );
			HTTP::redirect('/');
		}
		
		if ( $_POST )
		{
			//var_dump($_POST);exit;
			
			$character = ORM::factory('character') -> where( 
				'name', '=',$this -> request -> post ('charactername') ) -> find();
				
			$instr = array();
			
			if ($character -> loaded )
			{
				KO7::$log->add(KO7_Log::DEBUG, '-> Searching all IPs of lastlogin...');				
				
				if ($this -> request -> post('searchip'))	
				{
					$sql = "
					SELECT distinct tu.ipaddress 
					FROM trace_user_logins tu, users u, characters c
					WHERE u.id = tu.user_id
					AND   u.id = c.user_id 
					AND   c.name = {$character->name}";
					
					$res = Database::instance() -> query(Database::SELECT, $sql);
					foreach ($res as $row )			
						$instr[] = "'{$row->ipaddress}'";
					$instrtext = implode(",", $instr);
				
					$sql = "
						SELECT u.id user_id, c.name character_name, c.id character_id, u.ipaddress, u.username, tu.logincookie, from_unixtime(logintime) logintime,
						from_unixtime(u.bandate) bandate, u.status 
						FROM trace_user_logins tu, users u, characters c
						WHERE u.id = tu.user_id
						AND   u.id = c.user_id 
						AND   tu.ipaddress in ({$instrtext})
						AND   tu.ipaddress != '0.0.0.0' 
						ORDER BY ipaddress ASC, logintime DESC
						";	
				}
				else
				{
					$sql = "
					SELECT distinct ifnull(tu.logincookie, concat('cookienotyetset-',c.id)) logincookie
					FROM trace_user_logins tu, users u, characters c
					WHERE u.id = tu.user_id
					AND   u.id = c.user_id 					
					AND   c.name = {$character->name}";
					
					$res = Database::instance() -> query(Database::SELECT, $sql);
					foreach ($res as $row )			
						$instr[] = "'{$row->logincookie}'";
					$instrtext = implode(",", $instr);
					
					$sql = "
						SELECT u.id user_id, c.name character_name, c.id character_id, tu.ipaddress, u.username, tu.logincookie, from_unixtime(logintime) logintime,
						from_unixtime(u.bandate) bandate, u.status  						
						FROM trace_user_logins tu, users u, characters c
						WHERE u.id = tu.user_id
						AND   u.id = c.user_id 
						AND   tu.logincookie in ({$instrtext})
						ORDER BY logincookie ASC, logintime DESC
						";	
					
				}
				
				$res = Database::instance() -> query(Database::SELECT, $sql);
				$characters = Database::instance() -> query(Database::SELECT, $sql) -> as_array();
			}
			else
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">Questo char non esiste.</div>");				
			}
		}
		
		$lnkmenu = Admin_Model::get_horizontalmenu('multicheck');		
		$subm -> submenu = $lnkmenu;
		$view -> characters = $characters;
		$view -> submenu = $subm;		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;	
		
	}
	
	/** 
	* Uccide un char
	* @param nome char
	* @param message
	* @return OK o NOK
	*/
	
	function killcharacter( $name ,&$message )
	{
	
		if (!Auth::instance()->logged_in('admin'))
		{
			$message = __('global.operation_not_allowed' );
			return false;
		}		
		
		$character = ORM::factory('character') -> where ( 'name', '=', $name ) -> find();
		
		if ( !$character -> loaded )
		{
			$message = 'Il personaggio: ' . $name . ' non esiste.';
			return false;
		}
	
		Database::instance()->query(Database::UPDATE, "update characters set glut=-1, health=-1 where id = " . $character -> id );
			
		Database::instance()->query(Database::UPDATE, "update character_actions set keylock=null, starttime = unix_timestamp(), endtime = unix_timestamp()
		where action = 'consumeglut' and character_id = " . $character -> id );
		
		$message = 'Il personaggio: ' . $name . ' &egrave; stato ucciso.'; 
		
		return true;

	}
	
	function bancharacter( $name, $context, $date, $reason, &$message )
	{
	
		if ( 
			!Auth::instance()->logged_in('admin') and
			!Auth::instance()->logged_in('staff')
		)
		{
			$message = __('global.operation_not_allowed' );
			return false;
		}
		
		$character = ORM::factory('Character') -> where ( 'name', '=', $name ) -> find();
		
		if ( !$character -> loaded )
		{
			$message = 'Il personaggio: ' . $name . '	 non esiste.';
			return false;
		}
	
		$bandate = strtotime( $date );
		
		if ($context == 'game')
		{					
			Database::instance()->query(Database::UPDATE, "
				update users 
				set status = 'banned', 
				bandate = {$bandate},
				reason = '{$reason}'
				where id = " . $character -> user_id );		
		}
		else
		{
			Model_Character::modify_stat_d(
				$character -> id, 'chatban', 0, null, null, true, $bandate, $reason );			
		}
		
		$message = 'Il personaggio: ' . $name . " &egrave; stato bannato. Context: {$context}"; 
		
		return true;

	}
	
	function changecharname( $oldname, $newname, &$message )
	{
		
		$charold = ORM::factory('Character') -> where ( 'name', '=', $oldname ) -> find();
		$db = Database::instance();		
		
		if ( !$charold -> loaded )
		{
			$message = 'Questo personaggio non esiste.';
			return false;			
		}
			
		$charnew = ORM::factory('Character') -> where ( 'name', '=', $newname ) -> find();
		
		if ( $charnew -> loaded )
		{
			$message = 'Il nome ' . $newname . '&egrave; gi&agrave; usato.';
			return false;			
		}
		
		$charold -> name = $newname;
		$charold -> save();
		$pe = new Character_PermanentEvent_Model();
		$pe -> character_id = $charold -> id;
		$pe -> type = 'normal';
		$pe -> description = "__permanentevents.namechange;$oldname;$newname";
		$pe -> timestamp = time();
		$pe -> save();
			
		if ( kohana::config('medeur.deleteforumaccount' ) )
		{
			$dbforum = Database::instance('forum');		
			$dbforum -> query (Database::UPDATE, "update smf_members set real_name = {$newname} where member_name = '" .
			$charold -> user -> username . "'");
		}
		
		$message = 'Il nome &egrave; stato cambiato.';
		
		return true;
	}
	
	function changecharemail( $name, $newemail, &$message )
	{
		
		$char = ORM::factory('character') -> where ( 'name', $name ) -> find();
		
		if ( !$char -> loaded )
		{
			$message = 'Questo personaggio non esiste.';
			return false;			
		}
		
		Model_User::modifyemail( $char -> user, $newemail );
		$message = 'Character email changed to: ' . $newemail;
		
		return true;
	}
	
	
	/**
	* Assegna i dobloni ad un user
	* @param none
	* @return none
	*/
	
	public function givedoubloons()
	{
		
		if (!Auth::instance()->logged_in('admin'))		
			HTTP::redirect('/user/login');		
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory( 'admin/givedoubloons');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');
		$lnkmenu = array('/admin/console/' => __('admin.main'),
		                 '/admin/giveitems/' => __('admin.giveitems'),
										 '/admin/add_adminmessage/' => __('admin.adminmessage'),
										 '/admin/wardrobeapprovalrequests/' => 'Richieste Guardaroba'
										 );
		
		$form = array ( 'quantity' => 1, 'to_username' => '' );

		if ( !$_POST )
			;
		else
		{
			$post = Validation::factory($this->request->post());
			
			$par[0] = ORM::factory( 'character' ) -> where ( 'name', '=', $this->request->post('to_username' ) ) -> find();
			$par[1] = $this->request->post('quantity');
			$par[2] = 'adminsend';
			$par[3] = $this ->request -> post('reason');
			$par[4] = 'Administration';
			$par[5] = $character;
			
			$ca = Character_Action_Model::factory("givedoubloons");		
			
			if ( $ca -> do_action( $par,  $message ) )
			{ 				
					Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
					HTTP::redirect ( 'admin/givedoubloons' );
			}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				$form = arr::overwrite($form, $post->as_array());								
				$view->form = $form;					
				$this->template->content = $view;										
			}		
			
		}
		$subm->submenu = $lnkmenu;
		
		$view->form = $form;
		$view->submenu = $subm;

		$this->template->sheets = $sheets;	
		$this->template->content = $view;
	
	
	}
	
	function add_adminmessage()
	{
		if (!Auth::instance()->logged_in('admin'))
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">" . 
				__('global.operation_not_allowed' ). "</div>");			
			HTTP::redirect('admin/console'); 
		}
		
		$view = View::factory( 'admin/add_adminmessage');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');
									 
		$form = array (
			'summary' => '',
			'message' => '' );
	
		if ( !$_POST )
		{
			;
		}
		else
		{
			$post = Validation::factory($this -> request -> post( ))
				->rule('summary',
                    function(Validation $array, $field, $value) {
				        if (is_null($value) or strlen($value) < 10 or strlen($value) > 255)  {
				            $array->error($field, "must_be_between_10_and_233");
                        }
                    }, array(':validation', ':field', ':value'))
				->rule('message','not_empty');
			
			if ($post->check())
			{
				$message = new Admin_Message_Model();					
				$message -> summary = $this -> request -> post('summary');
				$message -> message = $this -> request -> post('message');
				$message -> message = $this -> request -> post('message');
				$message -> timestamp = time();
				$message -> save();	
				
				My_Cache_Model::set ( '-global_adminmessage', $message -> as_array() );
				
				Character_Event_Model::addrecord( 1, 'announcement', 
					'__events.adminmessageposted' .				
					';' .   html::anchor( 'admin/read_adminmessage/' . $message -> id, $message -> summary ), 		
					'system' ); 		
				
				Session::instance()->set('user_message', "<div class=\"info_msg\">Hai inserito un nuovo messaggio.</div>");				
			}
			else
			{
				$errors = $post->errors('form_errors'); 
				$view -> bind('errors', $errors);				
			}
		}
		$lnkmenu = Admin_Model::get_horizontalmenu('add_adminmessage');		
		$view -> form = $form;
		$subm -> submenu = $lnkmenu;		
		$view -> submenu = $subm;
		$this -> template -> sheets = $sheets;	
		$this -> template -> content = $view;
		
	}
	
	function read_adminmessage( $message_id )
	{
	
		$view = View::factory( 'admin/view_adminmessage');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');		
	
		$message = ORM::factory('admin_message', $message_id);
		if ( $message -> loaded )
		{
			$message -> read ++;
			$message -> save();
		}
		else
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">" . 
				__('global.messagenotfound') . "</div>");			
			HTTP::redirect('/');
		}
		
		$view -> message = $message;
		$this -> template -> sheets = $sheets;	
		$this -> template -> content = $view;		
	
	}
	
	function list_allmessages()
	{
		$limit = 20	;		
		$view = View::factory( 'admin/list_allmessages');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		
		$messages = ORM::factory('AdminMessage') -> find_all();
		
		$this -> pagination = new Pagination(array(
			'base_url'=>'admin/list_allmessages',
			'uri_segment'=>'list_allmessages',
			'query_string' => 'page',
			'total_items' => $messages -> count(),
			'items_per_page'=> $limit));			
		
		$messages = ORM::factory('AdminMessage')
            ->offset($this->pagination->sql_offset)
            ->limit($limit);
		
		$view -> pagination = $this -> pagination;
		$view -> messages = $messages;		
		$this -> template -> sheets = $sheets;	
		$this -> template -> content = $view;	
	}

	/*
	 * Manage npcs
	 */
	
	public function manage_npcs()
	{

		if (!Auth::instance()->logged_in('admin') and !Auth::instance()->logged_in('staff'))
			HTTP::redirect('/user/login');

		$view = View::factory( 'admin/manage_npcs');
		$sheets = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm = View::factory ('template/submenu');
		$message = '';

		//$char = Character_Model::get_info(Session::instance()->get('char_id'));
		$form = array (
			'quantity' => 1,
			'npc' => '',
			'item' => '',
			'regions' => '' );

		/*
		$items = ORM::factory('cfgitem') -> select_list('id', 'name');
		foreach ($items as $key => $value ) {
			$regions[$key] = __($value);
		}
		 */

		$npc_names = array(
			'smallrat' => array( 'name' => 'Small Rat' ),
			'largerat' => array( 'name' => 'Large Rat' ),
			'chicken' => array( 'name' => 'Chicken' ),
			'largedog' => array( 'name' => 'Large Dog' ),
		);

		//Create map for dropdown menu
		foreach($npc_names as $key => $value) {
			$npc_dropdown[$key] = $value['name'];
		}

		//var_dump($cbitems);exit;

		asort($npc_dropdown);
		//asort($regions);

		if ($_POST)
		{
			var_dump( $_POST );// exit;
			$post = Validation::factory($this->request->post());

			$n = intval($this->request->post('quantity'));
			$npctag = $this->request->post('npc');
			$region_name = $this->request->post('regions');
			KO7::$log->add(KO7_Log::INFO, "-> Creating NEW NPC {$npctag}, n. {$n}.");

			$names = array();

			$npcs = ORM::factory('Character')
				-> where ('npctag', '=', $npctag )
				-> find_all();

			$region = ORM::factory('region') -> where (
					'name', '=', strtolower('regions.' . $region_name)) -> find();

			echo $region->id;

			foreach ($npcs as $npc)
				$names[$npc->name] = $npc->name;

			for($i = 0; $i < $n; $i++)
			{

				$name = $npc_names[$npctag]['name'] . ' called ' . mt_rand(1, 99999);

				if (array_key_exists($name, $names)) {
					$i--;
					continue;
				}

				KO7::$log->add(KO7_Log::DEBUG, "-> Creating NEW NPC: {$name}");

				$npcclass = NpcFactory_Model::create($npctag);
				$npcclass->create($name);
				$npcclass->setRegion_id($region->id);
				$npcclass->setPosition_id($region->id);
				$npcclass->save();

				$action_ai = Character_Action_Model::factory('npcai');
				$action_ai -> character_id = $npcclass -> id;
				$action_ai -> save();
				//VAR_DUMP($npcclass);
				//VAR_DUMP($action_ai);
			}

			$message = 'NPCs modified successfully!';
			Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
			$form = arr::overwrite($form, $post -> as_array());
		}

		$lnkmenu = Admin_Model::get_horizontalmenu('manage_npcs');
		$subm -> submenu = $lnkmenu;
		$view -> form = $form;
		$view -> submenu = $subm;
		$view -> npcs = $npc_dropdown;
		//$view -> regions = $regions;
		$this -> template->sheets = $sheets;
		$this -> template->content = $view;
	}


	/*
	 * Assegna oggetti ad un char 
	*/
	
	public function giveitems()
	{
		
		if ( !Auth::instance()->logged_in('admin') 
			and 
			 !Auth::instance()->logged_in('staff')
		)		
			HTTP::redirect('/user/login');				
		
		$view = View::factory('admin/giveitems');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$form = array ( 
			'quantity' => 1, 
			'to_username' => '', 
			'item' => '', 
			'reason' => '' );
		
		$items = ORM::factory('cfgitem') -> select_list('id', 'name');					
		foreach ($items as $key => $value )
			$cbitems[$key] = __($value); 
		
		//var_dump($cbitems);exit;
		
		asort($cbitems);
		
		if ($_POST)
		{
			//var_dump( $_POST ); exit; 
			$post = Validation::factory($this->request->post());
	
			$par[0] = ORM::factory( 'character' ) 
				-> where ( array( 'name' => $this->request->post('to_username' ) )) -> find(); 
			$par[1] = ORM::factory('cfgitem') 
				-> where( 'id', $this -> request -> post('item'))->find();	
			$par[2] = $this -> request -> post('quantity');				
			$par[3] = $this -> request -> post('reason' );
			$par[4] = $char;
			
			$ca = Character_Action_Model::factory("giveitem");							
			if ( $ca -> do_action( $par, $message ) )
			{ 				
				// traccia invio 		
				
				Character_Event_Model::addrecord( 
					$par[4] -> id, 
					'normal', 
					'__events.itemsent_event' . 
					';' .  $par[2] . 
					';__' . $par[1] -> name .
					';' . $par[0] -> name . 
					';' . date("d-M-Y H:i:s", time())
				);
						
				Model_Utility::mail( kohana::config('medeur.adminemail'),
					"Item sent by console", 
					$par[2] . ' ' . __($par[1] -> name) . ' has been sent to: ' . $par[0] -> name . ' by: ' . 
					$par[4] -> name );			
				
				Session::instance()->set('user_message', "<div class=\"info_msg\">". $message . "</div>");
				$form = arr::overwrite($form, $post -> as_array());												
				
		}	
			else	
			{ 
				Session::instance()->set('user_message', "<div class=\"error_msg\">". $message . "</div>"); 
				$form = arr::overwrite($form, $post -> as_array());												
			}		
			
		}
		
		$lnkmenu = Admin_Model::get_horizontalmenu('giveitems');		
		$subm -> submenu = $lnkmenu;		
		$view -> form = $form;
		$view -> submenu = $subm;		
		$view -> cbitems = $cbitems; 		
		$this -> template->sheets = $sheets;	
		$this -> template->content = $view;
	
	
	}

 /**
 * Visualizza richieste di approvazione
 * @param none
 * @return none
 */
	
	public function wardrobeapprovalrequests()
	{
		if (!Auth::instance()->logged_in('admin'))		
			HTTP::redirect('/user/login');		
		
		$view = View::factory( 'admin/wardrobeapprovalrequests');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$subm    = View::factory ('template/submenu');
		
		$requests = ORM::factory('wardrobe_approvalrequest') -> where ( 'status', 'new' ) -> find_all();
		$lnkmenu = Admin_Model::get_horizontalmenu('wardrobeapprovalrequests');		
		$subm -> submenu = $lnkmenu;
		$view -> requests = $requests;
		$view -> submenu = $subm;
		$this -> template->sheets = $sheets;	
		$this -> template->content = $view;
	
	
	}

	/**
	 * Visualizza una request da approvare
	 * @param id ID request
	 * @return none
	*/
	
	public function viewwardroberequest( $id = null )
	{
		$licenses = array();	

		if (!Auth::instance()->logged_in('admin'))		
			HTTP::redirect('/user/login');		
		
		$view = View::factory( 'admin/viewwardroberequest');
		$sheets  = array(
			'gamelayout' => 'screen', 
			'submenu' => 'screen', 
			'character' => 'screen');
		$subm    = View::factory ('template/submenu');
		
		
		if ( !$_POST )
		{
			$request = ORM::factory('wardrobe_approvalrequest', $id );	
			$character = ORM::factory('character', $request -> character -> id);
			
			
			$sql = "
			SELECT wc.id, wc.tag, wc.previewfilepath preview
                        FROM character_premiumbonuses cb, cfgwardrobeitems wc, cfgpremiumbonuses cfb
                        WHERE cfb.name like 'atelier-license%' 
                        and   cfb.id = cb.cfgpremiumbonus_id
                        and   cb.character_Id = {$character -> id}
			and cb.param1 = wc.tag";
			
			$res = Database::instance() -> query(Database::SELECT, $sql);
			$i = 0;
			
			foreach ($res as $row)
			{
				$licenses[$i]['id'] = $row -> id;
				$licenses[$i]['tag'] = $row -> tag;
				$licenses[$i]['preview'] = $row -> preview . "/" . $character -> sex . "/" . $row -> tag . ".png";
			}
			
			$bonuses = Model_Character::get_premiumbonuses( $request -> character_id );
			
			
		}
		else
		{
		
			
			$request = ORM::factory('wardrobe_approvalrequest', $this -> request -> post('id') );
			$path = DOCROOT . 'media/images/characters/wardrobe/' . $request -> character_id ;	
			
			
			if ( $request -> loaded == false )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">Questa richiesta non esiste</div>"); 
				HTTP::redirect('admin/wardrobeapprovalrequests');
			}
			
			// Accept Request, Charge
			
			if ( $this -> request -> post('AcceptCharge') != '' )
			{
			
				// check if char has enough doubloons
				if ( $request -> character -> get_item_quantity( 'doubloon' ) < 150 )
				{
					Session::instance()->set('user_message', "<div class=\"error_msg\">Il char non ha 150 dobloni.</div>"); 
					HTTP::redirect('admin/wardrobeapprovalrequests');
				}
				// muovi immagini nella directory corretta
				
				Wardrobe_Model::approvecustomizeditems( $request );
				
				// take off doubloons				
				$request -> character -> modify_doubloons( -150, 'wardrobeapprovalfree' );
				
				// marca request come accettata
				$request -> status = 'accepted';
				$request -> save();
				
				// manda evento al player				
				Character_Event_Model::addrecord( $request -> character -> id, 'normal', '__wardrobe.requestaccepted' );
			
			}
			
			// Accept request, don't charge
			
			if ( $this -> request -> post('AcceptNoCharge') != '' )
			{
			
				// muovi immagini nella directory corretta
				
				Wardrobe_Model::approvecustomizeditems( $request );
								
				// marca request come accettata
				$request -> status = 'accepted';
				$request -> save();
				
				// manda evento al player				
				Character_Event_Model::addrecord( $request -> character -> id, 'normal', '__wardrobe.requestaccepted' );
			
			}
			
			if ( $this -> request -> post('Refuse') != '' )			
			{
				
				// marca request come rifiutata
				$request -> status = 'rejected';
				$request -> reason = $this -> request -> post('reason');
				$request -> save();
	
				// manda evento al player		
				
				Character_Event_Model::addrecord( $request -> character -> id, 'normal', 
					'__wardrobe.requestrefusedrefund;' . $this -> request -> post('reason'));
			
			}
						
			Session::instance()->set('user_message', "<div class=\"info_msg\">Richiesta processata.</div>");
			HTTP::redirect('admin/wardrobeapprovalrequests');
			
		}
		
		$lnkmenu = array('/admin/console/' => __('admin.main'),	
										 '/admin/giveitems/' => __('admin.giveitems'),
										 '/admin/add_adminmessage/' => __('admin.adminmessage'),
										 '/admin/wardrobeapprovalrequests/' => 'Richieste Guardaroba'										 
										 );

		$lnkmenu = Admin_Model::get_horizontalmenu('wardrobeapprovalrequests');		
		$equippeditems = Model_Character::get_equipment( $request -> character -> id );
		$subm -> submenu = $lnkmenu;
		$view -> equippeditems = $equippeditems;
		$view -> licenses = $licenses;
		$view -> request = $request;
		$view -> submenu = $subm;
		$this -> template->sheets = $sheets;	
		$this -> template->content = $view;	
	
	}	
	
	public function changeuserstatus( $user_id, $status )
	{
		
		if (
			!Auth::instance()->logged_in('admin')
			and
			!Auth::instance()->logged_in('staff')
		)		
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">Permessi insufficienti.</div>");
			HTTP::redirect('/user/login');		
		}
		
		if ( !in_array( $status, array( 'active', 'suspended', 'canceled' )))
		{
			Session::instance()->set('user_message', "<div class=\"info_msg\">Stato: {$status} non previsto.</div>");
		}
		
		if ($status == 'active' )
			$sql = "
			UPDATE users 
			SET status = '{$status}',
			gracedate = unix_timestamp() + (24 * 3 * 3600) 
			WHERE id = {$user_id}";
		else
			$sql = "
			UPDATE users 
			SET status = '{$status}'			
			WHERE id = {$user_id}";
		
		Database::instance() -> query( Database::UPDATE, $sql );
		
		Session::instance()->set('user_message', "<div class=\"info_msg\">Stato utente modificato a: {$status}</div>"); 
		
		HTTP::redirect('admin/multicheck');
		
	}
	
		
}
