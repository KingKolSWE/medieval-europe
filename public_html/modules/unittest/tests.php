<?php

if ( ! class_exists('KO7'))
{
	die('Please include the ko7 bootstrap file (see README.markdown)');
}

if ($file = KO7::find_file('classes', 'Unittest/Tests'))
{
	require_once $file;

	// PHPUnit requires a test suite class to be in this file,
	// so we create a faux one that uses the ko7 base
	class TestSuite extends Unittest_Tests {}
}
else
{
	die('Could not include the test suite');
}
