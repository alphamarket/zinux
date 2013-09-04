<?php
require_once ('appException.php');

namespace iMVC\exceptions;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:09
 */
class invalideArgumentException extends appException
{

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

}
?>