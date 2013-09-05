<?php
namespace iMVC\exceptions;

require_once ('appException.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
class accessDeniedException extends appException
{
	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	function __construct($message = null, $code = null, $previous = null)
	{
            parent::__construct(strlen($message)?$message:"Access denied to <b>".$_SERVER['REQUEST_URI']."</b>.", $code, $previous);
            $this->SendErrorCode(403);
	}

}
?>