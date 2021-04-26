<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Message extends Controller_Template
{
	// Imposto il nome del template da usare
	
	public $template = 'template/gamelayout';

	// Se l'utente accede a messages viene rediretto alla view dei ricevuti
	
	public function index()
	{
		HTTP::redirect('message/received');		
	}

	/**
	* Indice archivio
	* @param none
	* @return none
	*/
	
	function archiveindex()
	{
	
		$view    = View::factory('message/archiveindex');
		$subm    = View::factory ('template/submenu');
		$sheets  = array(
			'gamelayout' => 'screen', 
			'pagination' => 'screen', 
			'submenu' => 'screen');					
		
		$limit = 25 ;// numero record per pagina
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$bonus = Model_Character::get_premiumbonus( $char -> id, 'professionaldesk' );
		
		//var_dump($bonus);exit;
		
		if ( !$bonus )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );				
			HTTP::redirect( '/message/received');
		}
		
		$capacity = Model_Character::get_stat_d( $char -> id, 'professionaldeskslot' );
		$archivedmessages = ORM::factory('message') -> 			
			where( 
				array(
					'char_id' => $char -> id,
					'archived' => 'Y' ) ) -> count_all();
		
		
		// Seleziono i messaggi che non siano stati cancellati dalla posta in arrivo
		
		$sql = 
		"select 
			m.isread, 
			m.fromchar_id,
			m.tochar_id, 
			m.id, 
			m.subject, 			
			m.date 
		from characters c, messages m
		where    m.archived = 'Y' 
		and    m.char_id = c.id 
		and    m.char_id = {$char->id}
		order by m.date desc";
		 
		$rset = Database::instance() -> query( $sql );
		
		$messages = array();
		
		$i = 0;
		foreach ($rset as $message)
		{
			$fromchar = Model_Character::create_publicprofilelink($message->fromchar_id);
			$tochar = Model_Character::create_publicprofilelink($message->tochar_id);
			$messages[$i]['id'] = $message->id;
			$messages[$i]['from'] = $fromchar;
			$messages[$i]['fromchar_id'] = $message->fromchar_id;
			$messages[$i]['tochar_id'] = $message->tochar_id;
			$messages[$i]['to'] = $tochar;
			$messages[$i]['subject'] = $message->subject;
			$messages[$i]['date'] = $message->date;			
			$i++;
		}
		
		$view -> capacity = $capacity -> value;
		$view -> archivedmessages = $archivedmessages;
		$view -> bonus = $bonus;
		$subm -> submenu = Model_Message::get_horizontalmenu('archiveindex');
		$view -> submenu = $subm;		
		$view -> messages = $messages;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	}
	
	/**
	* archivia un messaggio
	* @param ID messaggio 
	* @return none
	*/
	
	public function archive( $type, $message_id )
	{
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$message = ORM::factory( 'message', $message_id );
		
		if ( $message -> char_id != $char -> id )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );				
			HTTP::redirect( '/message/' . $type );
		}
		
		$capacity = Model_Character::get_stat_d( $char -> id, 'professionaldeskslot' );
		
		if ( $message -> archived == 'N' )
		{
		
			$archivedmessages = ORM::factory('message') -> 			
			where( 
				array(
					'char_id' => $char -> id,
					'archived' => 'Y' ) ) -> count_all();
			
			if ( $archivedmessages >= $capacity -> value )
			{
				Session::instance()->set('user_message', "<div class=\"error_msg\">".	
					__('message.error-archivefull')."</div>" );	
			}			
			else
			{
				$message -> archived = 'Y';
				$message -> save();
				Session::instance()->set('user_message', "<div class=\"info_msg\">".__('message.info-archivedok')."</div>" );					
			}
				
		
		}
		else
		{
			$message -> archived = 'N';
			$message -> save();
			Session::instance()->set('user_message', "<div class=\"info_msg\">".__('message.info-unarchivedok')."</div>" );	
		
		}
			
		HTTP::redirect(request::referrer());
	
	}
	
	/**
	* Cisualizza i messaggi ricevuti
	* @param none
	* @return none
	*/
	
	public function received()
	{
	
		$view    = View::factory('message/received');
		$subm    = View::factory ('template/submenu');
		$sheets  = array(
			'gamelayout' => 'screen', 
			'pagination' => 'screen', 
			'submenu' => 'screen');
					
		$limit = 25 ;// numero record per pagina
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		// filtri
		
		// check for bonus
		$bonus = Model_Character::get_premiumbonus( $char -> id, 'professionaldesk' );
		
		if ( 
			($this -> input -> get('subject') 
			or 
			$this -> input -> get('sender') 
			)
			and
			!$bonus
			)
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );				
			HTTP::redirect( '/message/received');
		}
		
		if ( $this -> input -> get('subject') )
		{
			$subject = '%' . $this -> input -> get('subject') . '%' ;
			$subject = Database::instance() -> escape_str( $subject );
		}
		else
			$subject = '';
		
		if ( $this -> input -> get('sender') )
		{
			$sender = '%' . $this -> input -> get('sender') . '%' ;
			$sender = Database::instance() -> escape_str( $sender );
		}
		else
			$sender = '';
		
		$criteria = '';
		
		if ( $subject != '' )
			$criteria .= __('message.subject') . ': ' . $this -> input -> get('subject');
		
		if ( $sender != '' )
			$criteria .= __('message.sender') . ': ' . $this -> input -> get('sender');
		
		
		// Seleziono i messaggi che non siano stati cancellati dalla posta in arrivo
		
		$sql = 
		"select 
			c.name sender, 
			m.isread, 
			m.fromchar_id,
			m.id, 
			m.archived,
			m.subject, 
			m.date 
		 from characters c, messages m 
		 where  m.fromchar_id = c.id 		 
		 and    m.archived = 'N' " ;
		 
		 if ( $subject != '' )
			$sql .= " and m.subject like '%" . $subject . "%'" ;
			
		 if ( $sender != '' )
			$sql .= " and c.name like '%" . $sender . "%'" ;
		 
		 $sql .= " and    m.tochar_id = " . $char -> id . "
		 and    m.char_id = " . $char -> id . "
		 order by m.date desc ";
		 
		$messages = Database::instance() -> query( $sql );
				
		$this -> pagination = new Pagination(array(
		 'base_url'=>'message/received',
		 'uri_segment'=>'received',
		 'style'=>"extended",
		 'total_items' => $messages -> count(),
		 'items_per_page' => $limit));		
		
		$sql .= " limit $limit offset " . $this -> pagination -> sql_offset ;
		
		$messages = Database::instance() -> query( $sql );	
		
		$view -> criteria = $criteria;
		$view -> bonus = Model_Character::get_premiumbonus( $char -> id, 'professionaldesk' );
		$subm -> submenu = Model_Message::get_horizontalmenu('received');
		$view -> submenu = $subm;
		$view -> pagination = $this->pagination;
		$view -> messages = $messages;		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	}


	/**
	* visualizza i messaggi inviati
	* @param none
	* @return none
	*/
	
	public function sent()
	{
	
		$limit = 25 ;// numero record per pagina
		$view    = View::factory('message/sent');
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout' => 'screen', 'pagination'=>'screen', 'submenu'=>'screen');
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		// check for bonus
		$bonus = Model_Character::get_premiumbonus( $char -> id, 'professionaldesk' );
		
		if ( 
			($this -> input -> get('subject') 
			or 
			$this -> input -> get('recipient') 
			)
			and
			!$bonus
			)
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );				
			HTTP::redirect( '/message/received');
		}
		
		if ( $this -> input -> get('subject') )
		{
			$subject = '%' . $this -> input -> get('subject') . '%' ;
			$subject = Database::instance() -> escape_str( $subject );
		}
		else
			$subject = '';
		
		if ( $this -> input -> get('recipient') )
		{
			$recipient = '%' . $this -> input -> get('recipient') . '%' ;
			$recipient = Database::instance() -> escape_str( $recipient );
		}
		else
			$recipient = '';
		
		$criteria = '';
		
		if ( $subject != '' )
			$criteria .= __('message.subject') . ': ' . $this -> input -> get('subject');
		
		if ( $recipient != '' )
			$criteria .= __('message.to') . ': ' . $this -> input -> get('recipient');		
		
		// Seleziono solo i messaggi che non siano stati cancellati dalla posta inviata
		
		$sql = 
		"select 
			c.name receiver, 
			m.isread, 
			m.tochar_id, 
			m.id, 
			m.subject, 
			m.date,			
			m.archived 
		 from  characters c, messages m 		 
		 where m.tochar_id = c.id		 
		 and   m.fromchar_id = " . $char -> id . "
		 and   m.archived = 'N' " ;
		 
		 if ( $subject != '' )
			$sql .= " and m.subject like '%" . $subject . "%'" ;
			
		 if ( $recipient != '' )
			$sql .= " and c.name like '%" . $recipient . "%'" ;
	
		 $sql .= " and   m.char_id = " . $char -> id . "
		 order by m.date desc ";
		
		$messages = Database::instance() -> query( $sql );
		
		$this->pagination = new Pagination(array(
		 'base_url'=>'message/sent',
		 'uri_segment'=>'sent',
		 'style'=>"extended",
		 'total_items'=>$messages->count(),
		 'items_per_page'=>$limit));		
		
		$sql .= " limit $limit offset " . $this -> pagination -> sql_offset ;		
		$messages = Database::instance() -> query( $sql );	
		
		// Visualizzo i messaggi
		$view -> criteria = $criteria;
		$view -> bonus = $bonus;
		$subm -> submenu = Model_Message::get_horizontalmenu('sent');
		$view -> submenu = $subm;
		$view -> pagination = $this -> pagination;
		$view -> messages = $messages;		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		
	}


	/** 
	* Scrive un messaggio
	* @param int $id id messaggio
	* @param str $mode {new, reply or forward}
	* @param int $character_id ID personaggio
	*/
	
	public function write( $id = 0, $mode = 'new', $character_id = null )
	{
		
		$view = View::factory('message/write');
		$subm    = View::factory ('template/submenu');
		$sheets  = array(
			'gamelayout' => 'screen', 
			'pagination'=>'screen', 
			'submenu'=>'screen');		
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		$form = array(
				'to' => '',		
				'subject' => '',
				'type' => 'normal',
				'massive' => false,
				'body' => '',
		);
		
		// controllo se l' utente � validato
		
		if ( $char -> user -> status != 'active' )
		{
			Session::instance()->set('user_message', "<div class=\"error_msg\">".__('charactions.userisnotactive')."</div>" );									
			HTTP::redirect('/message/received');
		}
		
		if ( !$_POST )
		{
						
			$to = '';	
			$type = 'normal';
			$subject = '';
			$body = '';
			
			if ( !is_null( $character_id ) )
			{
				$recipient = ORM::factory('character', $character_id );
				if ( $recipient -> loaded )
					$to = $recipient -> name ;				
			}

			
			///////////////////////////////////////////////////
			// se l'id non � nullo, � una reply o un forward
			///////////////////////////////////////////////////
			
			if ( $id != 0 )
			{				
		
				$message = ORM::factory('message', $id );
				
				if ( !$message -> loaded or $message -> char_id != $char -> id )
				{
					Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );				
					HTTP::redirect( '/message/sent');
				}
								
				$recipient = ORM::factory('character', $message -> tochar_id );
				$sender = ORM::factory('character', $message -> fromchar_id );
				
				$type = 'normal';
				
				if ( $mode == 'reply' )
				{
					
					$subject = 'Re: ' . str_replace( 'Re: ', '', $message -> subject) ;										
					if ( $sender -> loaded )
						$to = $sender -> name ;
					else
						$to = '';
						
				}
				else
				{					
					$subject = 'Fwd: ' . $message -> subject ;
					$to = '';
				}
				
				$text = str_replace( "[messagequote]", '', $message -> body);
				$text = str_replace( "[/messagequote]", '', $text);
				
				$body = 
					"\r\n\r\n" .
					$char -> signature . 
					"\r\n" . 
					"-------" . 
					"\r\n" .
					"\r\n" .
					'[messagequote]' .
					'[b]From:[/b] ' . $sender -> name . "\r\n" . 
					'[b]Sent on:[/b] ' . Model_Utility::format_datetime($message -> date) . "\r\n" .
					'[b]Sent to:[/b] ' . $recipient -> name . 
					"\r\n" . 
					"\r\n" .
					$text .
					'[/messagequote]';
			}
			
			$form = array(
				'to' => $to,		
				'subject' => $subject,
				'type' => $type,
				'massive' => false,
				'body' => $body,
			);
			
			$errors = $form;
		}
		else
		{     
			
			$post = Validation::factory($this -> request -> post())				
				->add_rules('subject', 'required', 'length[1,255]')
				->add_rules('body', 'required')								
				->add_callbacks('subject', array($this, '_checkbadwords'))
				->add_callbacks('body', array($this, '_checkbadwords'))
				->add_callbacks('to', array($this, '_checkrecipient'))
				->add_callbacks('massive', array($this, '_checkmassive'));				
      
			if ( $post -> validate() )
			{
				
				$m = new Model_Message();
				$sender = Model_Character::get_info( Session::instance()->get('char_id') );
				$recipient = ORM::factory('character')->where( array('name' => $this->request->post('to')))->find();
				$subject = $this->request -> post('subject');
								
				$body = $this -> request -> post('body');
				
				// includi signature in fondo al body solo su nuovi messaggi.
								
				if ($mode == 'reply' or $mode == 'forward' )
					;
				else			
					$body .= "\r\n\r\n" . $sender -> signature;
				
				$massive = $this -> request -> post('massive');
				$type = $this -> request -> post('type'); 
				
				if ( $sender -> id == $recipient -> id )
				{
					Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );				
					HTTP::redirect( '/message/received');				
				}
				
				// se la tipologia � weddingproposal, il messaggio può essere scritto solo da un maschio ad una donna
				
				if ( $type == 'weddingproposal' )
				{
					$param1 = null;
					if ($sender -> sex != 'M' or $recipient -> sex != 'F' )
					{
						Session::instance()->set('user_message', "<div class=\"error_msg\">".__('message.error-weddingproposalsex')."</div>" );				
						HTTP::redirect( '/message/received');
					
					}
				}
				
				if ( $type == 'weddingannulment' )
				{	
					$param1 = 1;
				
					if  (Model_Character::is_marriedto(
						$sender -> id,
						$recipient -> id, $dummy) == false)								
					{
						Session::instance()->set('user_message', 
							"<div class=\"error_msg\">".__('message.error-notmarriedto',
								$recipient -> name)."</div>" );				
						HTTP::redirect( '/message/received');				
					}
				}
				
				$ret = $m -> send( 
					$sender, $recipient, $subject, $body, $massive, $system = false, $copyforsender = true, $type = $type, $param1 = 1);
			
				if ( $ret == 'OK' )
				{					
					Session::instance()->set('user_message', "<div class=\"info_msg\">".__('message.message_success')."</div>" );				
					HTTP::redirect( '/message/sent');
				}
				else
					Session::instance()->set('user_message', "<div class=\"error_msg\">".__( $ret )."</div>" );
				
				$form = arr::overwrite($form, $post->as_array()); 
				
			}
			else
			{				
				// Traduco gli errori con gli errori custom internazionalizzati
		
				$errors = $post -> errors('form_errors');                             
				
				$view -> bind('errors', $errors);
				$form = arr::overwrite($form, $post -> as_array());      
			}
			
		}
		
		$view -> char = $char;
		$view -> bonus = Model_Character::get_premiumbonus( $char -> id, 'professionaldesk' );
		$view -> bind ('form', $form );
		$subm -> submenu = Model_Message::get_horizontalmenu('write');
		$view -> submenu = $subm;
		$this -> template->content = $view;
		$this -> template->sheets = $sheets;
	
	
	}
	
	/**
	** Visualizza un messaggio
	* @param: message_id: ID del messaggio
	* @return none
	*/
	
	public function view( $type, $message_id )
	{
	
		$view = View::factory('message/view');
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout' => 'screen', 'pagination'=>'screen', 'submenu'=>'screen');
		$lnkmenu = Model_Message::get_horizontalmenu('view');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		$message = ORM::factory('message', $message_id );
		if ( $message -> loaded == false or ( $message -> char_id != $char -> id ))
		{ 
			Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );				
			HTTP::redirect( '/message/' . $type );
		}
		
		// Se il char � il destinatario e il messaggio non � stato ancora letto
		// allora aggiorno la flag read
		
		if (! $message -> isread )
		{
			$message -> isread = true;
			$message -> save();			
			Model_MyCache::delete( '-charinfo_' . $char -> id . '_unreadmessages' );
		}

		// Visualizzo il messaggio

		$view -> char = $char;
		$subm -> submenu = $lnkmenu;	
		$view -> bonus =  Model_Character::get_premiumbonus( $char -> id, 'professionaldesk' );
		$view -> type = $type;
		$view -> sender = ORM::factory('character', $message -> fromchar_id );
		$view -> recipient = ORM::factory('character', $message -> tochar_id );
		$view -> submenu = $subm;
		$view -> message = $message;		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	}


	/** 
	* Cancella un messaggio
	* @param: string Tipo messaggio (sent/received)
	* @param: int id messaggio
	* @return: none
	*/
	
	public function delete($type, $message_id)
	{
				
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$message = ORM::factory('message', $message_id ); 
		
		if ( 
			!$message -> loaded 
			or ( 
			$message -> char_id != $char -> id) )
		{				
			Session::instance()->set('user_message', "<div class=\"error_msg\">".__('global.operation_not_allowed')."</div>" );							
		}
		
		$message -> delete();		
		Model_MyCache::delete (  '-charinfo_' . $char -> id . '_unreadmessages' );

		Session::instance()->set('user_message', "<div class=\"info_msg\">".__('message.message_delete')."</div>" );						
		HTTP::redirect('/message/' . $type );		
	}
	
	// Funzione che cancella i messaggi selezionati
	
	public function deleteselectedmessages (  )
	{	
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$selected_messages = $this -> request -> post('messages') ;		
		$messagestodelete = implode( ", ", array_keys ( $selected_messages ));
		
		Database::instance() -> query ( 
		"delete from messages 
		where char_id = " . $char -> id . " 
		and   id in ( " . $messagestodelete . ")" );
		
		HTTP::redirect(request::referrer() );
	}
	
	/*
	 * Verifica se nel body ci sono bad words	 
	 * @param  Validation  $array   oggetto Validation
	 * @param  string      $field   nome del campo che deve essere validato
	 */
	
	public function _checkbadwords(Validation $array, $field)
	{
		$badwords = Model_Configuration::get_badwordscfg();
		
		//var_dump($badwords);exit;
		
		foreach ($badwords as $badword => $data)
		{
			if (strpos(strtoupper($array[$field]), strtoupper($badword)) !== false) 
			{
				$array -> add_error($field, 'postcontainsbadwords');								
				$array[$field] = str_replace("***","",$array[$field]);				
				$array[$field] = str_replace(strtoupper($badword),"***$badword***",strtoupper($array[$field]));				
				return false;
			}
		}
		return true;
		
	}
	
	/*
	 * Callback: verifica che l' utente abbia precisato il recipient
	 * e se esiste nel gioco
	 * @param  Validation  $array   oggetto Validation
	 * @param  string      $field   nome del campo che deve essere validato
	 */
	 
	public function _checkrecipient( Validation $array, $field)
	{
		KO7::$log->add(KO7_Log::DEBUG, ($array['massive']) );
		
		if ( $array['massive'] and !empty($array[$field]) )
		{
			$array->add_error($field, 'incoherentmode');
			return false;
		}
		
		if ( $array['massive'] and empty($array[$field]) )
		{			
			return true;
		}
		
		if ( empty($array[$field]))
		{
			$array->add_error($field, 'required');
			return false;
		}
		 
		$char = ORM::factory('character')->where( array('name' => $array[$field]))->find();
		if ( !$char->loaded ) 
		{
			$array->add_error( $field, 'char_not_exist'); 
			return false;
		}
		return true;
	}
	
	
	
	/**
	* Controlla se il giocatore ha diritto 
	* ad inviare una email massiva
	*/
	
	function _checkmassive(Validation $array, $field )
	{
		return false;
	}
		
}
