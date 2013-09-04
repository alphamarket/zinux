<?php
namespace iMVC;

require_once ('kernel\security\security.php');


/**
 * This is a base class for all iMVC classes
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:21
 */
abstract class baseiMVC extends stdClass
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