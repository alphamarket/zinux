<?php
namespace iMVC\exceptions;

require_once ('accessDeniedException.php');


/**
 * Description of permissionDeniedException
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:23
 */
class permissionDeniedException extends accessDeniedException
{

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

}
?>