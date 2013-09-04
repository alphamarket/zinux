<?php
require_once ('appException.php');

namespace iMVC\exceptions;


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
	public function __construct($message = null, $code = null, $previous = null)
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