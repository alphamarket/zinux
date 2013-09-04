<?php
namespace iMVC\kernel\routing;

require_once 'request.php';
require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:23
 */
class preDispatcher extends \iMVC\baseiMVC
{

	function __construct()
	{
	}

	function __destruct()
	{
	}

        public function Initiate()
        {
            ;
        }
        public function Dispose()
        {
            parent::Dispose();
        }

	/**
	 * Initiate the pre-dispacther processes
	 * 
	 * @param request
	 */
	public function Process(request $request)
	{
	}

	/**
	 * Runs every function in {MODULE}\BootStrap.php function which end with 'Init' in
	 * postfix in its name.
	 * 
	 * @param request
	 */
	public function RunBootstraps(request $request)
	{
	}

	/**
	 * Runs ini config file's methods
	 * 
	 * @param request
	 */
	public function RunINI(request $request)
	{
	}

	/**
	 * Runs reserved URI
	 * 
	 * @param request
	 */
	public function RunReservedURI(request $request)
	{
	}

}
?>