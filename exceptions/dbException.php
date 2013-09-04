<?php
namespace iMVC\exceptions;

require_once ('appException.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class dbException extends appException
{

	protected $_detail;
	protected $_errorno;
	protected $_error_msg;

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
	 * automaticly create mysql exception
	 * @param string details about the current mysql exception
	 * 
	 * @param detail
	 */
	public function createException($detail = null)
	{
	}

	/**
	 * get generated mysql error number
	 * @return int
	 */
	public function getErrorNo()
	{
	}

	/**
	 * get generated mysql error message
	 * @return string
	 */
	public function getErrorMessage()
	{
	}

}
?>