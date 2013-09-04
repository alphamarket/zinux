<?php
namespace iMVC\kernel\routing;

require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:13:38
 */
class fakeRequest extends \iMVC\baseiMVC
{

	protected $_backedup_vars;

	/**
		 * construct a new fake request
		 * 
		 * @param uri
		 * @param GET
		 * @param POST
		 */
	function __construct(string $uri, array $GET = array(), array $POST = array())
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
	public function send($auto_echo = 1, $throw_exception = 0)
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