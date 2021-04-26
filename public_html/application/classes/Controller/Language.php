<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Language extends Controller
{
	public function change_language( $lang = 'en_US' )
	{
		Model_User::change_language( $lang );
		HTTP::redirect(request::referrer());
	}
	
}