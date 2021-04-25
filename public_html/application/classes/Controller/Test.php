	<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Test extends Controller_Template
{	
	const SECURITYKEY = 'ro432u4elwfjreljehgrehrekqthkgtqteegq';
	
	// Imposto il nome del template da usare
	
	public $template = 'template/gamelayout';

	function test( $functionname, $dryrun = true, $par1 = null, $par2 = null, $par3 = null, $par4 = null )
	{
		
		$this -> autorender = false;	
		$parameters = array( $par1, $par2, $par3, $par4 );
	
		KO7::$log->add(KO7_Log::DEBUG, "-> Testing {$functionname}...");
		
		try 
		{
			Database::instance() -> query("set autocommit = 0");
			Database::instance() -> query("start transaction");
			Database::instance() -> query("begin");
		
			$callback = 'Test_Model::'.$functionname;		
			call_user_func_array( $callback, $parameters );
		
			if (!$dryrun)
			{
				Database::instance() -> query('commit');
				KO7::$log->add(KO7_Log::INFO, "Committed.");				
			}
			else
			{
				Database::instance() -> query('rollback');
				KO7::$log->add(KO7_Log::INFO, "Rollbacked.");								
			}
		} catch (Exception $e)
		{	
			var_dump($e -> getMessage());		
			var_dump($e -> getTraceAsString());
			KO7::$log->add(KO7_Log::ERROR, $e->getMessage());
			KO7::$log->add(KO7_Log::ERROR, 	"-> An error occurred, rollbacking.");
			Database::instance() -> query("rollback");			
		}		
		
		exit;
		
	}
	
}

?>
