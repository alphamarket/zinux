<?php
require_once ('..\..\BaseiMVC.php');

namespace iMVC\kernel\routing;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:08
 */
class fakeRequest extends BaseiMVC
{

	protected $$_backedup_vars;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * construct a new fake request
	 * 
	 * @param uri
	 * @param GET
	 * @param POST
	 */
	public function __construct(string $uri, array $GET = array, array $POST = array)
	{
	}

	/**
	 * backs up variables at global scope
	 */
	protected function backupVars()
	{
	}

	/**
	 * Restore backed up vars
	 */
	protected function restoreVars()
	{
	}

	/**
	 * send the fake request
	 * 
	 * @param auto_echo
	 * @param throw_exception
	 */
	public function send(boolean $auto_echo = 1, boolean $throw_exception = 0)
	{
	}

	/**
	 * set global vars for the fake request
	 */
	protected function setVars()
	{
	}

	/**
	 * An inner send procedure
	 */
	protected function innerSend()
	{
	}

	/**
	 * A security check for target uri request
	 */
	protected function securityCheck()
	{
	}

}
?>