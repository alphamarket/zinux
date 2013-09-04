<?php
require_once ('..\..\BaseiMVC.php');

namespace iMVC\kernel\routing;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:09
 */
class preDispatcher extends BaseiMVC
{

	function __construct()
	{
	}

	function __destruct()
	{
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