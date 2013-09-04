<?php


namespace iMVC\exceptions;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:06
 */
class appException extends Exception
{

	private $stack_trace;
	private $error_code;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	public function __construct($message, $code, $previous)
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