<?php
namespace iMVC\exceptions;

require_once ('appException.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:23
 */
class notImplementedException extends appException
{

	private $error_code;

	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	public function __construct($message = null, $code = null, $previous = null)
	{
	}
	
	function __destruct()
	{
	}

	/**
	 * 
	 * @param code
	 */
	public function SendErrorCode($code = NULL)
	{
	}

	public function GetErrorCode()
	{
	}

}
?>