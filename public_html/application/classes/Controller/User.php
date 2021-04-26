<?php //defined('SYSPATH') OR die('No direct access allowed.');

class Controller_User extends Controller_Template
{		
	public $template = 'template/gamelayout';


    public function _checkcaptcha(Validation $array, $field, $value)
    {
        $valid = true;
        return;

        $query = http_build_query([
            'secret' => '6Lf_v3MUAAAAANqZZNLdcnp61ux0aEXhCWkfPqkE',
            'response' => $this -> request -> post('g-recaptcha-response'),
        ]);

        $url = "https://www.google.com/recaptcha/api/siteverify?" . $query;
        KO7::$log->add(KO7_Log::DEBUG, kohana::debug($url));
        $ch = curl_init( $url );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = json_decode(curl_exec($ch));
        KO7::$log->add(KO7_Log::DEBUG, curl_error($ch));
        KO7::$log->add(KO7_Log::DEBUG, kohana::debug($response));
        $valid = false;
        if ($response && $response -> success) {
            $valid = true;
        }
        else
        {
            $valid = false;
            $array -> error($field, 'captchaerror');
            KO7::$log->add(KO7_Log::DEBUG, KO7_Debug::dump($array));
        }

    }

    public function _fake_email(Validation $array, $field, $value)
    {
        $d = explode("@", $value);
        // controllo il db
        $fake_domain = (bool) ORM::factory('Blockedemailprovider') -> where('domain', '=', $d[1])->count_all();

        if ($fake_domain)
        {
            // aggiungo l' errore
            $array -> error($field, 'blocked_domain');
        }
    }

    /*
     * Normal user registration
     * @param none
     * @return none
    */
    function action_register()
    {
        $this->register();
    }
	
	function register()
	{
		
		// GOOGLE SSO		
		$google = new Model_GoogleBridge();
		
		// FACEBOOK SSO
		//$fb = new Facebook_Bridge_Model();
		
		$view = View::factory('page/home');
		$this -> template = View::factory('template/homepage');
		$sheets = array( 'home' => 'screen',);	 	
		$this -> template -> content = $view; 
		$this -> template -> sheets = $sheets;  
		$this -> auth = KO7_Auth::instance();
			
		$form = array(
			'username' => '',
			'email' => '', 
			'referreruser' => '',
			'accepttos' => false,
			'newsletter' => true,
		);
		
		//if a post exists, validate and process input
		
		if ($_POST)
		{
			
			$post = Validation::factory($this -> request -> post())
                ->rule('username',
                    function(Validation $array, $field, $value) {
                        if (!ctype_alnum($value)) {
                            $array->error($field, 'not_alpha_numeric');
                            return;
                        } elseif (strlen($value) < 5 or strlen($value) > 20) {
                            $array->error($field, 'too_long_or_short');
                            return;
                        }
                    },
                    array(':validation', ':field', ':value')
                )-> rule('email',
                    function(Validation $array, $field, $value) {
				        if (strlen($value) < 1 or strlen($value) > 60) {
				            $array->error($field, 'too_long_or_short');
				            return;
                        } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $array->error($field, 'not_valid_email');
                        }
                    },
                    array(':validation', ':field', ':value')
                ) -> rule('referral_id',
                    function(Validation $array, $field, $value) {
                        if (!is_null($value) and !ctype_digit($value)) {
                            $array->error($field, 'not_numeric');
                        }
                    },
                    array(':validation', ':field', ':value')
                );

			$post -> rule('username',function(Validation $array, $field, $value){ $this->_unique_username($array, $field, $value); }, array(':validation', ':field', ':value'));
			$post -> rule('email', function(Validation $array, $field, $value){$this->_unique_email($array, $field, $value); }, array(':validation', ':field', ':value'));
			$post -> rule('email', function(Validation $array, $field, $value){$this->_fake_email($array, $field, $value); }, array(':validation', ':field', ':value'));
			$post -> rule('captchaanswer', function(Validation $array, $field, $value){$this->_checkcaptcha($array, $field, $value); }, array(':validation', ':field', ':value'));
			$post -> rule( 'referral_id',  function(Validation $array, $field, $value){$this->_c_referral_id($array, $field, $value); }, array(':validation', ':field', ':value'));

			$additional_params = [
			    'birthday' => null,
			    'gender' => null,
			    'status' => 'new',
			    'ipaddress' => Request::$client_ip,
			    'request_ids' => $this -> request -> param('request_ids'),
			    'referrersite' => null
			];
			
			if ( $post -> check() )
			{
			    echo "HERE\n";
				$rc = Model_User::registerorloginuser( array_merge($post->data(), $additional_params), $message );
				if ( $rc == false )
					Session::instance()->get_once('user_message', "<div class=\"error_msg\">" . $message . "</div>");
				else
				{
					header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
                    echo "HERE2\n";
					HTTP::redirect( 'boardmessage/index/europecrier');
				}
			}
			else
			{
                echo "HERE3\n";
                $errors = $post -> errors('form_errors');
                $view -> errors = $errors;
				$form = Arr::overwrite( $form, $post -> data());
				print_r($form);
			}
		}
		// else, redirect to home
		else
		{
            echo "HERE4\n";
            HTTP::redirect('/');
		}
		

		$view -> form = $form;		
		//$view -> facebook_login_url = $fb -> get_login_url();
		$view -> google_login_url = $google -> get_google_login_url();
		
	}
	

	/*
	 * Validazione utente, verifica che il token passato sia uguale a quello
	 * associato all' utente	 
	 */

	public function activate($user_id = null, $activationtoken = null)
	{
		$this -> template = View::factory('template/homepage');
		$view = View::factory('user/activate');
		$sheets = array('home' => 'screen');
	 	
		$this->template = View::factory('template/homepage');
		$this->template->sheets = $sheets;
		
		$user = ORM::factory('User')->where( array(
		  'id' => $user_id, 
		  'activationtoken' => $activationtoken,
		  'status' => 'new'
		   ))->find();

		//KO7::$log->add(KO7_Log::DEBUG, kohana::debug( $user ));
  
		if ( ! $user->loaded  )
		{
			$view->message = __('user.activate_userortokennotfound',
				html::anchor( "/user/resendvalidationtoken/", __('user.activate_resendtoken') ));
			$this->template->content = $view;		
			return;
		}  
    			
		$user->status = 'active' ;
		
		if ( $user->save() )
			$view->message = __('user.activate_useractivated', html::anchor('/', __( 'menu_notlogged.login' ) ) );
		else
			$view->message = __('user.activate_validationerror');

		$this->template->content = $view;
		$this->template->sheets = $sheets;
		return;
	}


	/**
	 * Reinvia il token di validazione per la email specificata. 
	 * @param none
	 * @return none
    */

	public function resendvalidationtoken()
	{
		
		$this -> template = View::factory('template/homepage');
		$sheets = array('home' => 'screen');	 	
		$view = View::factory('user/resendvalidationtoken');
		
		$form = array(			
			'email' => '',  
		);				
		
		// copio errors da form cosi' gli errori matchano le chiavi della form
		
		$errors = $form;		
		
		if ( $_POST )
		{       
		
			$post = Validation::factory($_POST)
                ->rule('email',
                    function(Validation $array, $field, $value) {
                        if (strlen($value) < 1 or strlen($value) > 60) {
                            $array->error($field, 'too_long_or_short');
                            return;
                        } elseif (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $array->error($field, 'not_valid_email');
                        }
                    },
                    array(':validation', ':field', ':value'));

		  
		  if ($post->check() )
		  {

				$user = ORM::factory('User')->where(
					'email', '=', $this -> request -> post('email'))->and_where(
					'status', '=', 'new'
				) -> find();
				
				if ($user -> loaded)
				{
					if (!in_array( $user -> referrersite, array( 'facebook', 'bbrelax') ) )
					{
						// email
						$subject = __('user.resendvalidationtoken_emailsubject');
						$body    = sprintf (__('user.resendvalidationtoken_emailbody'),
						'https://' . $this->request->uri() . "/index.php/user/activate/".$user->id."/".$user->activationtoken);
						$to = $post['email'];				
						$result = Model_Utility::mail( $to, $subject, $body );
						
						if ( $result ) 
						{                      
							Session::instance()->get_once('user_message', "<div class=\"info_msg\">".__('user.resendvalidationtoken_success')."</div>" );
						}
						else
						{
								Session::instance()->get_once('user_message', "<div class=\"error_msg\">".__('user.resendvalidationtoken_error')."</div>" );
						}        
					}  
					else
					{
						Session::instance()->get_once('user_message', "<div class=\"info_msg\">".__('user.resendvalidationtoken_noneedtobevalidated')."</div>" );
						
					}
				}
				// Nessun utente � stato trovato con l' email specificata
				else
				{	
					Session::instance()->get_once('user_message', "<div class=\"error_msg\">".__('user.resendvalidationtoken_nouserfound')."</div>" );
				}
  
		  }
		  else
		  {      
				$errors = $post->errors('form_errors');                             
				$view -> bind('errors', $errors);        			
		  }
		
		}
		
		
		$view -> bind('form', $form);
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

	} 

	/*
	 * Reinvia la password per la email specificata. 	 
	 */

	public function resendpassword()
	{		
		
		// Imposto il template e gli stylesheets
		
		$this -> template = View::factory('template/homepage');
		$sheets = array('home' => 'screen');

		$view = View::factory('user/resendpassword');
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;

		$form = array('email' => '');				
			
		// copio errors da form cosi' gli errori matchano le chiavi della form
		
		$errors = $form;
		
		if ( $_POST )
		{
			
			$this -> auth = KO7_Auth::instance();
			$post = Validation::factory($_POST)
				-> pre_filter('trim', TRUE)
				-> add_rules( 'email', 'required', 'email', 'length[1,30]' );
				
			if ($post->validate() )
			{
				
				$user = ORM::factory('User') ->
					where( array( 
						'email' => $this -> request -> post('email')
				)) -> find();

				//var_dump($user);exit;

				if ( $user->loaded )
				{

					//print kohana::debug( "previous user password: " . $user->password );
					$newpassword_clr = substr(md5(time()),1,5);
					KO7::$log->add( KO7_Log::INFO, "user: " . $user -> username . " new password clear: " . $newpassword_clr );
					$user -> password = $newpassword_clr;                             
					//print kohana::debug("new user password: " . $user->password);

					$result_save = $user -> save();

					// email
					
					$subject = __('user.resendpassword_emailsubject');
					$body    = sprintf (__('user.resendpassword_emailbody'), $newpassword_clr, $user->username );
					$to      = $post['email'];					
					$result_email = Model_Utility::mail( $to, $subject, $body );
								
					Session::instance()->get_once('user_message', "<div class=\"info_msg\">".__('user.resendpassword_success')."</div>");
					
				}  
				
				// Nessun utente � stato trovato con l' email specificata
			
				else
				{
					Session::instance()->get_once('user_message', "<div class=\"error_msg\">".__('user.resendpassword_nouserfound')."</div>");
				}
			}
			else
			{      							
				$errors = $post -> errors('form_errors');				
				$view -> errors = $errors;
				
			}
		  
			$form = arr::overwrite($form, $post->data());
		  
		}
		
		$view -> form = $form;
	
		}

	
	/**
	 * Autenticazione utente - Normale login
	 * @param none
	 * @return none
	*/
	public function action_login()
    {
        //$this->response->body('hello, world!');
        //return;
        $this->login();
    }

	public function login( $homepage = 'classic' )
	{
		
		// FACEBOOK SSO
		//$fb = new Facebook_Bridge_Model();
		// GOOGLE SSO

        echo Debug::source(__FILE__, __LINE__);

        //$google = new Model_GoogleBridge();


        echo Debug::source(__FILE__, __LINE__);

        //$this->response->body('hello, world!');
		$message = 'ASDF';
		$this -> template = View::factory('template/homepage');
        //$this->response->body($message);
		echo Debug::vars($message);
		$view = View::factory('page/home');			
		$sheets = array( 'home' => 'screen' );
		//$db = Database::instance();
		$this -> template -> sheets = $sheets;  
		$form = array( 
			'username' => '', 
			'password' => '', 
			'email' => '',
			'referreruser' => '',
		);

		//echo "ASDF";
        //$this->response->body('hello, world!');

		$user = $character = null;
		
		// POST: Normale login

        KO7::$log->add(KO7_Log::INFO, 'HERE!');
        echo "HERE";
		
		if ( $this->request->post() )
		{
			
			$post = Validation::factory($_POST)
				 -> pre_filter('trim', TRUE)
				 -> add_rules('username', 'required')				
				 -> add_rules('password', 'required');
		
						
			if ($post -> validate() )
			{
				$post = new Validation( $this -> request-> post() );
				$username = $this->request->post('username');
				$password = $this->request->post('password');
				$this -> auth = KO7_Auth::instance();
				$error = null;			
				
				// L' utente esiste?
				
				KO7::$log->add(KO7_Log::INFO, '-> Check: user: [' . $username . '], exists?');
				// si può usare l' username.
				$user = ORM::factory( 'User' ) -> where ( 'username', '=', $username) -> find();
				
				if ( !$user -> loaded )
				{
					$error = 'user.login_usernotfound';
					Session::instance()->get_once( 'user_message', "<div class=\"error_msg\">".__( $error )."</div>");
				}			
				
				if ( is_null( $error ) )
				{
					// check user and password
					
					$rc = $this -> auth -> login( $user, $password );						
					KO7::$log->add(KO7_Log::DEBUG, "-> Return from aut: {$rc}");
					if ( $rc )
					{
						
						$data['referrersite'] = $user -> referrersite;
						$data['username'] = $username;		
						$data['email'] = $user -> email;
						$data['ipaddress'] = Request::$client_ip;
						$data['fb_id'] = 'normal';
						
						$rc = Model_User::registerorloginuser( $data, $message );
						
						if ( $rc == false )
							Session::instance()->get_once( 'user_message', "<div class=\"error_msg\">" . $message . "</div>");
						else
						{
							header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
							HTTP::redirect( 'region/view');
						}
						
					}
					else
					{
						KO7::$log->add( KO7_Log::DEBUG, "-> Password [{$password}] is wrong." );
						Session::instance()->get_once( 'user_message', "<div class=\"error_msg\">".__("user.incorrectpassword")."</div>");
					}
					
				}
				
			}	
			else
				Session::instance()->get_once( 'user_message', "<div class='error_msg'>".__("user.login_autherror")."</div>");
		}
		else
		{
			KO7::$log->add( KO7_Log::DEBUG, '-> Called login, but POST is null.' );
			//KO7::$log->add( KO7_Log::DEBUG, kohana::debug( $this -> request -> post() ) );
			HTTP::redirect( '/' );
		}
		
		KO7::$log->add(KO7_Log::DEBUG, '-> Redirecting to view...' );
		//$view -> facebook_login_url = $fb -> get_login_url();
		$view -> google_login_url = $google -> get_google_login_url();
		$view -> referrerurl = $this -> request -> post('referral');
				
		$view -> form = $form;
		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;    
		
	}
	
	/**
	* Y8.com SSO
	* @param none
	* @return none
	*/
	
	function y8_login()
	{
	
		$view = View::factory('page/home');
		$sheets = array( 'home' => 'screen' );
		$db = Database::instance();				
		$this -> template -> sheets = $sheets;  
		$message = '';
		$access_token = null;
		
		$appID = "54d65aa2694862f28f003b6c";
		$appSecret = "f7d251525ab35037d34429a7465215df92f10a1f820511163f8dbc6ee8fe0a53";			
		$this -> auth = KO7_Auth::instance();
		$user = $character = null;
		
		if (isset($_GET['code'])) {
			$idCode = $_GET['code'];
		} else {
			$idCode = 0;
		}
		if (isset($_GET['state'])) {
			$idState = $_GET['state'];
		} else {
			$idState = 0;
		}
	
		//Reset playerID information
		$PlayerUserID = 0;
		
		//Check loading status
		$loadGame = 0;
	
		//Affiliate information
		$referrersite = 'y8.com';
		
		//Include all callback options & Get token
		require_once(dirname(realpath(__FILE__)) . "/../libraries/vendors/IdNet/classes/CallHelper.php");		
		$result = CallHelper::getCurl($appID, $appSecret, $idCode);
		//Get token
		
		$token_info = json_decode($result, true);
		if (isset($token_info["error"])) {
			;
		} else {
			$access_token = $token_info["access_token"];
		}
		
		$userdata = CallHelper::getUserDataCurl($access_token);			
		$data['username'] = $userdata['nickname'];
		$data['email'] = $userdata['email'];
		if ( empty( $userdata['gender'] ) )
			$data['gender'] = '';
		else				
			$data['gender'] = ($userdata['gender'] == 'male' ? 'm' : 'f') ;								
		$data['birthday'] = null;
		$data['password'] = md5(time());
		$data['newsletter'] = 'N' ;						
		$data['idnet_id'] = $userdata["pid"];				
		$data['external_id'] = $userdata["pid"];				
		$data['fb_id'] = 'y8';
		$data['referrersite'] = 'y8.com' ;
		$data['referreruser'] = null;
		$data['status'] = 'active';
		$data['activationtoken'] = null;
		$data['created'] = time();
		$data['ipaddress'] = Request::$client_ip;
		$data['tutorialmode'] = 'Y';
		
		$rc = Model_User::registerorloginuser( $data, $message );
		if ( $rc == false )
			Session::instance()->get_once( 'user_message', "<div class=\"error_msg\">" . $message . "</div>");
		else
		{
			header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
			HTTP::redirect( 'boardmessage/index/europecrier');
		}
		
		$view -> referrerurl = $this -> request -> post('referral');
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;  	
		
	}
	
	/**
	* Google SSO
	* @param none
	* @return none
	*/
	
	function google_login()
	{
		
		$view = View::factory('page/home');
		$sheets = array( 'home' => 'screen' );
		$this -> template -> sheets = $sheets;  		
				
		KO7::$log->add(KO7_Log::DEBUG, '-> Google login called' );
		
		// Accertati che non siano in corso attacchi di request forgery, e che l'utente
		// che invia la richiesta di connessione sia quello previsto.
				
		KO7::$log->add(KO7_Log::DEBUG, '-> Querying google...' );
		//KO7::$log->add(KO7_Log::DEBUG, kohana::debug($this -> request -> param()));
		
		$google = new Model_GoogleBridge();
		$service = $google -> get_service();
		$client = $google -> get_client();
		
		KO7::$log->add(KO7_Log::DEBUG, '-> Authenticating...');
		
		$client -> authenticate($this -> request -> param('code'));
		//KO7::$log->add(KO7_Log::DEBUG, kohana::debug( $client ));
		$accesstoken = $client->getAccessToken();
		Session::instance()->set('googleaccesstoken', $accesstoken );
		
		// get user data
		$info = $service -> people -> get ('me');		
		$emails = $info -> getEmails();
		
		// Leggo referrer dal cookie
		
		$val = cookie::get('referraldata', 'cookiemissing');					
		parse_str($val, $values);
		
		KO7::$log->add(KO7_Log::DEBUG, kohana::debug($values));
		
		$data = array();			
		$data['username'] = 'g_' . substr(uniqid('', true), 1, 10);
		$data['email'] = $emails['0'] -> value;
		$data['birthday'] = null;
		$data['gender'] = ($info -> getGender() == 'male') ? 'm' :  'f' ;
		$data['newsletter'] ='Y';
		$data['fb_id'] = null;
		$data['external_id'] = $info -> getId();
		$data['password'] = md5(time());				
		$data['referral_id'] = 0;
		$data['status'] = 'active';
		$data['activationtoken'] = null;
		$data['created'] = time();
		$data['ipaddress'] = Request::$client_ip;
		$data['tutorialmode'] = 'Y';
		$data['referrersite'] = 'google';
		
		if (isset($values['referreruser']) )
			$data['referreruser'] = $values['referreruser'];
		else
			$data['referreruser'] = null;
		
		$rc = Model_User::registerorloginuser( $data, $message );
		if ( $rc == false )
		{

			Session::instance()->get_once( 'user_message', "<div class=\"error_msg\">" . $message . "</div>");
			HTTP::redirect('/');
		}
		else
		{
			header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
			//KO7::$log->add(KO7_Log::INFO, '-> Redirecting to europectrier...');
			HTTP::redirect( 'boardmessage/index/europecrier');
		}				
	}			
	
	/**
	* BB-Relax SSO
	* @param none
	* @return none
	*/
	
	function relaxbb_login()
	{
		
		$view = View::factory('page/home');
		$sheets = array( 'home' => 'screen' );
		$db = Database::instance();				
		$this -> template -> sheets = $sheets;  
		$message = '';

		// Import BBrelax SDK
		
		require_once('application/libraries/vendors/bbrelax/iplayer.php');
		$iplayer = IPlayer::handle_request();
		
		$form = array(
				'username' => '',			
				'password' => ''	
		);
		
		$this -> auth = KO7_Auth::instance();
		$user = $character = null;
		
		// load user.
		KO7::$log->add(KO7_Log::DEBUG, '-> BBRelax login: trying to fetch user.' );
		
		try{
			$user_bbrelax = $iplayer -> user_info();
		}catch( IPlayer_Exception $e )
		{
			KO7::$log->add('error', '-> BBRelax exception: ' . $e -> getMessage() );
			$user_bbrelax = null;
			die( 'An error has occurred: ' . $e -> getMessage());			
		}
		
		$data['username'] = $user_bbrelax['nickname'];
		$data['email'] = $user_bbrelax['username'];
		$data['gender'] = $user_bbrelax['gender'];
		$data['birthday'] = strtotime($user_bbrelax['birthday']);
		$data['password'] = md5(time());
		$data['newsletter'] = 'N' ;
		$data['referrersite'] = 'iplayer.org';
		$data['referreruser'] = null;
		$data['fb_id'] = 'relax';
		$data['status'] = 'active';
		$data['activationtoken'] = null;
		$data['created'] = time();
		$data['ipaddress'] = Request::$client_ip;
		$data['tutorialmode'] = 'Y';
		
		$rc = Model_User::registerorloginuser( $data, $message );
		if ( $rc == false )
			Session::instance()->get_once( 'user_message', "<div class=\"error_msg\">" . $message . "</div>");
		else
		{
			header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
			HTTP::redirect( 'boardmessage/index/europecrier');
		}
			
	}
	
	/**
	* Facebook SSO
	* @param none
	* @return none
	*/
	
	function fb_login()
	{		
			
		// Prendi info dell utente...
		
		$graph_url = "https://graph.facebook.com/me?access_token=" . $_REQUEST['access_token'];		
		$ch = curl_init();		
		curl_setopt ($ch, CURLOPT_URL, $graph_url );
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		$rawdata = curl_exec($ch);
			
		$user = json_decode($rawdata);
		KO7::$log->add(KO7_Log::INFO, '----- FB User -----');
		KO7::$log->add(KO7_Log::INFO, kohana::debug($user));
		
		if (isset($user -> error)) 
		{
			die ("Could not get a valid token from Facebook.");
		}
		else
		{
			if (isset($user -> email))
				$email = $user -> email;
			else
				$email = uniqid('', true) . '@nowhere.com';
			
			$data = array();			
			$data['username'] = 'f_' . substr(uniqid('', true), 1, 10);
			$data['email'] = $email;
			$data['birthday'] = null;
			if (isset($user -> gender))
				$data['gender'] = $user -> gender == 'male' ? 'm' :  'f' ;
			else
				$data['gender'] = '';
			$data['newsletter'] ='Y';
			$data['referrersite'] = 'facebook';
			$data['fb_id'] = $user -> id;
			$data['external_id'] = $user -> id;
			$data['password'] = md5(time());				
			$data['referral_id'] = 0;
			$data['status'] = 'active';
			$data['activationtoken'] = null;
			$data['created'] = time();
			$data['ipaddress'] = Request::$client_ip;
			$data['tutorialmode'] = 'Y';
			$data['request_ids'] = $this -> request -> param('requests_id');
			
			// Leggo referrer dal cookie
			
			$val = cookie::get('referraldata', 'cookiemissing');					
			parse_str($val, $values);
			
			
			// check requestids from facebook
			// se c'� una request_ids � un invito 
			// quindi � complementare al referreruser
			// che arriverebbe da visita diretta.
			
			if (!is_null($data['request_ids']))
			{
				KO7::$log->add(KO7_Log::DEBUG, '-> Adding Referral...');
				$pendingrequest = ORM::factory('facebook_inviterequest') 
					-> where ( array (
						'status' => 'new',
						'request_id' => $data['request_ids'],
						'friend_id' => $data['fb_id']
						) ) -> find();
				
				if ($pendingrequest -> loaded )
				{
					$data['referreruser'] = $pendingrequest -> user_id;
					$pendingrequest -> status = 'processed';
					$pendingrequest -> save();					
				}				
			}
			else
				if (isset($values['referreruser']) )
					$data['referreruser'] = $values['referreruser'];
				else
					$data['referreruser'] = null;
			
			

			$rc = Model_User::registerorloginuser( $data, $message );
			
			if ( $rc == false )
			{

				Session::instance()->get_once( 'user_message', "<div class=\"error_msg\">" . $message . "</div>");
				HTTP::redirect('/');
			}
			else
			{
				header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
				//KO7::$log->add(KO7_Log::INFO, '-> Redirecting to europectrier...');
				HTTP::redirect( 'boardmessage/index/europecrier');
			}				
		}	
			
	}
	
	/*
	 * Logout utente
	 *
	 */
	
	public function logout()
	{
		
		$character = Model_Character::get_info( Session::instance()->get('char_id') );
		$this -> template = View::factory('template/homepage');
		$sheets = array('home' => 'screen');
		
		$message = '';
		
		// se il char ha il bonus automaticsleep, setta
		// automatic sleep a sì a meno che non sia esplicitamente
		// vietato
		
		if ( !is_null( $character ))
		{
			if ( 
				Model_Character::get_premiumbonus( $character -> id, 'automatedsleep' ) !== false
				and
				$character -> user -> disablesleepafteraction == 'N' 
			)
			{
				KO7::$log->add(KO7_Log::INFO, "{$character -> name} logged out, I am setting sleepafteraction to Y.");
				$character -> user -> sleepafteraction = 'Y' ;
				$character -> user -> save();
			}
		}
		
		Session::instance() -> destroy();
		
		$this->template->content=View::factory('user/logout'); 
		$this->template->sheets = $sheets;   
		
		Auth::instance()->logout( true );	
	}

	/*
	* Visualizza il profilo dell' utente
	*
	*/
	
	public function profile()
	{
	
      $view    = View::factory('user/profile');
      $subm    = View::factory ('template/submenu');
      $sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
	  $char = Model_Character::get_info( Session::instance()->get('char_id') );
      
	  $lnkmenu = $char -> user -> get_account_submenu( 'profile' );	 
	  $subm->submenu = $lnkmenu;
	  $view->submenu = $subm;
      $user = Auth::instance() -> get_user();      
      $view->bind('user', $user);
      	                 
      $this->template->content = $view;
      $this->template->sheets = $sheets;
	}
	
	/**
	* Modifica utente
	* @param none
	* @return none 
	*/

	public function changepassword()
	{
    
		$view    = View::factory('user/changepassword');
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		$lnkmenu = $char -> user -> get_account_submenu( 'changepassword' );	 
		$subm -> submenu = $lnkmenu;
		$view -> submenu = $subm;
		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		$form = array(
			'old_password' => '',
			'password' => '', 
			'password_confirm' => '',			  
		);	
	
		$errors = $form;
	
		if ( $_POST )
		{      
			  
			
			$user = Auth::instance() -> get_user();						
			$post = Validation::factory($_POST)
				 -> pre_filter('trim', TRUE)
				 -> add_rules('old_password', 'required')				
				 -> add_rules('password', 'required')
				 -> add_rules('password_confirm', 'required')
				 -> add_rules('password_confirm', 'matches[password]');
			
			$post -> add_callbacks('old_password', array($this, '_checkoldpassword'));
			
			if ($post -> validate() )
			{
				$user -> password = $this -> request -> post( 'password'); $user -> save();
				Session::instance()->get_once('user_message', "<div class=\"info_msg\">".__('user.change_password_ok')."</div>" );
				HTTP::redirect('user/profile');
			}	
			else
			{

				$errors = $post -> errors('form_errors');                             
				//print kohana::debug( $errors);
				$view -> bind('errors', $errors); 					
			}
			$view -> bind('form', $form);				
			
		}		
		
	}
	
	/*
	* Callback che verifica l' unicità del nome
	*
	* @param  Validation  $array   oggetto Validation
	* @param  string      $field   nome del campo che deve essere validato
	*/

  public function _unique_username(Validation $array, $field, $value)
  {
     // controllo il db
     $name_exists = (bool) ORM::factory('User')->where('username', '=', $value)->count_all();
   
     if ($name_exists)
     {
         // aggiungo l' errore
         $array->error($field, 'username_exists');
     }
  }

	/*
	 * Callback che verifica l' unicità della email
	 *
	 * @param  Validation  $array   oggetto Validation
	 * @param  string      $field   nome del campo che deve essere validato
	 */

  public function _unique_email(Validation $array, $field, $value)
  {
     // controllo il db
     $email_exists = (bool) ORM::factory('User')->where('email', '=', $value)->count_all();
   
     if ($email_exists)
     {
         // aggiungo l' errore
         $array->error($field, 'email_exists');
     }
  }

  
	/*
	* Callback che verifica che il referral id specificato esista nel database 
	*
	* @param  Validation  $array   oggetto Validation
	* @param  string      $field   nome del campo che deve essere validato
	*/

  public function _c_referral_id(Validation $array, $field, $value)
  {
       
  
     if ( empty($value) )
        return;
				
     // controllo il db
     $id_exists = (bool) ORM::factory('User')->where(
        'id', '=', $value)->and_where('status', '!=', 'canceled')->count_all();
   
     if (! $id_exists)
     {
         // aggiungo l' errore
         $array -> error($field, 'id_notexisting');
     }
  }
  
	/*
	* Callback: verifica che l' utente abbia accettato il terms of service.
	*
	* @param  Validation  $array   oggetto Validation
	* @param  string      $field   nome del campo che deve essere validato
	*/
  
  public function _c_accepttos(Validation $array, $field, $value)
  {
    if ( $value != true )
      $array->error($field, 'tos_notaccepted');
  }

	/*
	* Callback: verifica che la vecchia pwd sia corretta
	*
	* @param  Validation  $array   oggetto Validation
	* @param  string      $field   nome del campo che deve essere validato
	*/

	public function _checkoldpassword (Validation $array, $field, $value)
	{
	$user = Auth::instance()->get_user();			
	$salt = Auth::instance()->find_salt( $user->password );
	if ( strcmp($user->password , Auth::instance()->hash_password( $value, $salt )) )
		$array->error($field, 'matches');
	}  

	/**
	* Funzione che lista i referral
	* @param none
	* @return none
	*/

	public function referrals()
	{
	
		$view = View::factory( 'user/referrals');
		$sheets  = array('gamelayout' => 'screen', 'character'=>'screen', 'pagination'=>'screen', 'submenu'=>'screen');		
		$user = Auth::instance()->get_user();			
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		
		$db = Database::instance();
		$sql = "
			select r.*, c.name, c.id character_id, u.created 
			from user_referrals r, characters c, users u
			where 
			r.user_id = " . $user -> id . " 
			and r.referred_id = c.user_id and c.user_id = u.id
			order by r.id desc" ;
		
		
		$referrals = $db->query( $sql );
		
		//$output = $this->profiler->render(TRUE);				
		
		$submenu = View::factory("character/submenu");
		$submenu -> action = 'referrals';
		$view -> submenu = $submenu;
		$view->referrals = $referrals;
		$view->user = $user;
		$view->char = $char;
		$this->template->content = $view;
		$this->template->sheets = $sheets;
	
	}

	/*
	 * Configure Account
	 * @param none
	 * @return none
	 */
	 
	 public function configure()
	 {
	 
		$_user = Auth::instance()->get_user();							
		$user = ORM::factory('User', $_user -> id);
		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$view = View::factory ( 'user/configure');
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout'=>'screen', 'submenu'=>'screen');
		$lnkmenu = $char -> user -> get_account_submenu( 'configure' );	 
		$titles = array(		
			'notitle' => __('global.title_notitle_b'),
			'artisan' => __('global.title_artisan_b'),
			'bachelor' => __('global.title_bachelor_b'),
			'brother' => __('global.title_brother_b'),
			'burgher' => __('global.title_burgher_b'),
			'commoner' => __('global.title_commoner_b'),
			'don' => __('global.title_don_b'),
			'despot' => __('global.title_despot_b'),
			'esquire' => __('global.title_esquire_b'),
			'explorer' => __('global.title_explorer_b'),
			'father' => __('global.title_father_b'),
			'freeman' => __('global.title_freeman_b'),
			'gentleman' => __('global.title_gentleman_b'),
			'magister' => __('global.title_magister_b'),
			'monsignor' => __('global.title_monsignor_b'),
			'master' => __('global.title_master_b'),
			'mercenary' => __('global.title_mercenary_b'),
			'merchant' => __('global.title_merchant_b'),
			'peasant' => __('global.title_peasant_b'),
			'pirate' => __('global.title_pirate_b'),
			'rogue' => __('global.title_rogue_b'),
			'scholar' => __('global.title_scholar_b'),
			'sergeant' => __('global.title_sergeant_b'),
			'wanderer' => __('global.title_wanderer_b'),
			'warlord' => __('global.title_warlord_b'),
			'warrior' => __('global.title_warrior_b'),
		);
			   		
		// combo Linguaggi
		
		$view -> spokenlanguages = array(
			'' => __('global.select'),
			'Bulgarian' => 'Bulgarian',
			'Croatian' => 'Croatian',
			'Czech' => 'Czech',
			'Dutch' => 'Dutch',
			'English' => 'English',
			'French' => 'French',			
			'German' => 'German',
			'Italian' => 'Italian',			
			'Portuguese' => 'Portuguese',
			'Russian' => 'Russian',						
			'Serbian' => 'Serbian',
			'Spanish' => 'Spanish',
		);
		
		
		if ( !$_POST )
			;
		else
		{
		
			// ***** General Panel *****
			
			if ( $this -> request -> post('general') != '' )
			{
				
				$user -> nationality = $this -> request -> post('nationality' );
			
				// Hide max stat badges
			
				if ( $this -> request -> post('hidemaxstatsbadges') == 'activate')
					$user -> hidemaxstatsbadges = 'Y';
				else
					$user -> hidemaxstatsbadges = 'N';

				// available for religious functions
			
				if ( $this -> request -> post('availableregfunctions') == 'available')
					$user -> availableregfunctions = 'Y';
				else
					$user -> availableregfunctions = 'N';
									
				// Spoken Languages
				
				//var_dump($this -> request -> post());exit;
				
				// linguaggi parlati, su user
								
				foreach ($user -> user_languages as $language )				
				{
					$language -> language = $this -> request -> post('spokenlanguage'.$language -> position);
					//var_dump($language);exit;
					$language -> save();
				}
				
				if ( $this -> request -> post('showlanguagesinpublicprofile') == 'show')
					$user -> showlanguages = 'Y';
				else
					$user -> showlanguages = 'N';
				
				$user -> save();			
				$par[0] = $user -> nationality;
				Model_GameEvent::process_event( $char, 'configurenationality', $par );
			
			}
			
			// ***** SKIN *****
			
			if ( $this -> request-> post('skin') != '' )
			{
				Model_Character::modify_stat_d( $char -> id,
					'skin', 
					0,
					null,
					null,
					true,
					$this -> request -> post('skin')
				);
			}
			
			// ***** BASIC PACKAGE OPTIONS *****
			
			if ( $this -> request -> post('basicpackage') != '' )
			{
				Model_Character::modify_stat_d(
					$char -> id,
					'basicpackage',
					0,
					'title',
					null,
					true,					
					$this -> request -> post('title') . '_' . strtolower($char -> sex),
					$this -> request -> post('title')
				);
				
			}
			
			// ***** EMAIL *****
			
			if ( $this -> request -> post('emailsection') == 'Modify' )
			{				
				
				// newsletter
			
				if ( $this -> request -> post('newsletter') == 'send')
					$user -> newsletter = 'Y';
				else
					$user -> newsletter = 'N';
			
				// receive IG messages on email
				
				if ( $this -> request -> post('receiveigmessagesonemail') == 'receive')
					$user -> receiveigmessagesonemail = 'Y';
				else
					$user -> receiveigmessagesonemail = 'N';
				
/*
				if ($user -> email != $this -> request -> post('email') )
					User_model::modifyemail( $user, $this -> request -> post('email'));
*/
				
				$user -> save();
			
			}
			
			// ***** AUTOMATED REST *****
			
			if ( $this -> request -> post('automatedsleep') != '' )
			{
				// Automated Rest
			
				if ( Model_Character::get_premiumbonus( $char -> id, 'automatedsleep' ) !== false )
				{
				
					if ( $this -> request -> post('disablesleepafteraction') == 'activate')
						$user -> disablesleepafteraction = 'Y';
					else
						$user -> disablesleepafteraction = 'N';		
					
					if ( $this -> request -> post('maxglut') < 1 or $this -> request -> post('maxglut') > 50 )
					{
					
					Session::instance()->get_once('user_message', "<div class=\"error_msg\">" . __('user.error-maxglutvalue')."</div>" );
						HTTP::redirect('/user/configure');
					}
					
					$user -> maxglut = 	$this -> request -> post('maxglut');
					$user -> save();
					
				}
			}
			
			Session::instance()->get_once('user_message', "<div class=\"info_msg\">".__('user.customization_ok')."</div>" );
			
		}
		
		
		// countries		
		$countrycodes = ORM::factory('cfgcountrycode')->find_all();		
		foreach ($countrycodes as $cc)
			$ccodes[$cc -> code] = $cc -> country;
		
		// reload user
		
		$user = ORM::factory('User', $user -> id);
		$languages = array();
		foreach ($user -> user_languages as $language)
			$languages[$language -> position] = $language -> language;
		
		$stat = Model_Character::get_stat_d(
				$char -> id,
				'basicpackage',
				'title');
			
		if ($stat -> loaded)
			$title = $stat -> stat2;
		else
			$title = '';
		
		$subm -> submenu = $lnkmenu;
		$view -> languages = $languages;
		$view -> title = $title;
		$view -> countrycodes = $ccodes;		
		$view -> submenu = $subm;
	  $view -> user = $user;		
		$view -> titles = $titles;
		$view -> char = $char;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
		
	}
	 
	/**
	* Returns all bonus bought by player
	* @param none
	* @return none
	*/
	
	function bonuspurchases()
	{
		
		$view = View::factory( 'user/bonuspurchases');
		$subm    = View::factory ('template/submenu');
		$sheets  = array('gamelayout' => 'screen', 'character'=>'screen', 'pagination'=>'screen', 'submenu'=>'screen');		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$limit = 20	;

		$activebonuses = Model_Character::get_premiumbonuses($char -> id);
		
		$purchasedbonuses = Database::instance() -> query("
			SELECT cp.id, c.name, c.cutunit, cp.user_id, 
			cp.targetuser_id, cp.targetcharname, cp.character_id, cp.cfgpremiumbonus_id, 
			-- cp.cfgpremiumbonuses_cut_id, 
			cp.starttime, cp.endtime, cp.param1, cp.param2, cp.doubloons 
			FROM character_premiumbonuses cp, cfgpremiumbonuses c
			WHERE cp.character_id = {$char -> id} 
			AND   cp.cfgpremiumbonus_id != 0 
			AND   cp.cfgpremiumbonus_id = c.id
			ORDER BY cp.endtime desc
			");
				
		$this->pagination = new Pagination(array(
			'base_url'=>'user/bonuspurchases',
			'uri_segment'=>'bonuspurchases',
			'style' =>  'extended',
			'total_items' => $purchasedbonuses -> count(),
			'items_per_page'=>$limit));				
		
		$purchasedbonuses = Database::instance() -> query("
			SELECT cp.id, c.name, c.cutunit, cp.user_id, 
			cp.targetuser_id, cp.targetcharname, cp.character_id, cp.cfgpremiumbonus_id, 
			-- cp.cfgpremiumbonuses_cut_id, 
			cp.starttime, cp.endtime, cp.param1, cp.param2, cp.doubloons 
			FROM character_premiumbonuses cp, cfgpremiumbonuses c
			WHERE cp.character_id = {$char -> id} 
			AND   cp.cfgpremiumbonus_id != 0 
			AND   cp.cfgpremiumbonus_id = c.id			
			ORDER BY cp.endtime desc
			limit $limit offset " . $this -> pagination -> sql_offset );
			
		$lnkmenu = $char -> user -> get_account_submenu( 'bonuspurchases' ); 	
		$subm -> submenu = $lnkmenu;
		$view -> submenu = $subm;
		$view -> char = $char;
		$view -> tabindex = 0;
		$view -> pagination = $this->pagination;
		$view -> purchasedbonuses = $purchasedbonuses;
		$view -> activebonuses = $activebonuses;
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;
	
	}

	/**
	* Returns all purchases made by the account
	* @param none
	* @return none
	*/
	
	public function purchases( )
	{
		
		$view = View::factory( 'user/purchases');
		$subm    = View::factory ('template/submenu');
		$sheets  = 
			array('gamelayout' => 'screen', 'character'=>'screen', 'pagination'=>'screen', 'submenu'=>'screen');		
		$char = Model_Character::get_info( Session::instance()->get('char_id') );
		$limit = 20	;
		
		$purchases = Database::instance() -> query("
			SELECT * 
			FROM electronicpayments
			WHERE user_id = {$char -> user_id}
			ORDER BY id desc;
			");
				
		$this->pagination = new Pagination(array(
			'base_url'=>'user/purchases',
			'uri_segment'=>'purchases',
			'style' =>  'extended',
			'total_items' => $purchases -> count(),
			'items_per_page' => $limit));				
		
		$purchases = Database::instance() -> query("
			SELECT * 
			FROM electronicpayments 
			WHERE user_id = {$char -> user_id}			
			ORDER BY id desc 
			limit $limit offset " . $this -> pagination -> sql_offset );
			
		$lnkmenu = $char -> user -> get_account_submenu( 'purchases' ); 	
		$subm -> submenu = $lnkmenu;
		$view -> submenu = $subm;
		$view -> char = $char;
		$view -> pagination = $this->pagination;
		$view -> purchases = $purchases;		
		$this -> template -> content = $view;
		$this -> template -> sheets = $sheets;		
	}

  /**
  * unsubscribe from the newsletter
  * @param hashcode
  * @param email address
  * @return none
  */
  
  public function unsubscribe ( $username, $hash )
  {
	KO7::$log->add(KO7_Log::DEBUG, '-> Trying to unsubscribe {$username}, hash: {$hash}' );
	$user = ORM::factory('User') -> where (
		array( 
			'activationtoken' => $hash,
			'username' => $username,
			'newsletter' => 'Y' 
		)) -> find();
		
	if ( $user -> loaded )
	{
		$user -> newsletter = 'N';
		$user -> save();
		HTTP::redirect('page/display/unsubscribe-ok');
	}
	else
		HTTP::redirect('page/display/unsubscribe-nok');
  }
  

  
}
