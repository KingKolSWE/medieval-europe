<?php defined('SYSPATH') OR die('No direct access allowed.');

class Language_Controller extends Controller
{
	public function change_language( $lang = 'en_US' )
	{
		Model_User::change_language( $lang );
		url::redirect(request::referrer());
	}
	
}