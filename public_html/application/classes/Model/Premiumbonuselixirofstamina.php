<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_ElixirofstaminaPremiumBonus extends Model_PremiumBonus{	function __construct()    {        $this -> name = 'elixirofstamina';	}
	function postsaveactions( $char, $cut, $par, &$message )
	{		$info = $this -> get_info();		$item = Model_Item::factory( null, $this -> name );		$item -> additem( 'character', $char-> id, $info['cuts'][$cut]['cut'] );		parent::postsaveactions($char, $cut, $par, $message );		return true;	}
}