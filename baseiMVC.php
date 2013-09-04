<?php
namespace iMVC;

defined("IMVC_PATH") || define('IMVC_PATH', realpath(__DIR__)."/");

if(!defined('IMVC_INCLUDE_PATH'))
{
    define('IMVC_INCLUDE_PATH', realpath(__DIR__));
    set_include_path(implode(":", array(get_include_path(), IMVC_PATH)));
}


require_once (dirname(__FILE__).'/kernel/security/security.php');


/**
 * This is a base class for all iMVC classes
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:21
 */
abstract class baseiMVC extends \stdClass
{

	/**
	 * A security class instance
	 */
	protected $security;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * This will dispose any temp attributes starts with '_' prefix in their names
	 */
	public function Dispose()
	{
	}

	/**
	 * The initiation works on loading class
	 */
	abstract public function Initiate();
}
?>
