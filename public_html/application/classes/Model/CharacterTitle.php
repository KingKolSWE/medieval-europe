<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_CharacterTitle extends ORM
{
  protected $sorting = array('position' => 'asc'); 
  protected $belongs_to = array('cfgachievement');
  
}