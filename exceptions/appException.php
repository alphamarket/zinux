<?php
namespace iMVC\exceptions;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
class appException extends Exception
{

	private $stack_trace;
	private $error_code;
	
	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	function __construct($message = null, $code = null, $previous = null)
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

	public function GetErrorTraceAsString()
	{
	}

}
?>