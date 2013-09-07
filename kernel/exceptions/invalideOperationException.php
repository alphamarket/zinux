<?php
namespace iMVC\kernel\exceptions;

require_once ('appException.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:23
 */
class invalideOperationException extends appException
{

	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	function __construct($message = null, $code = null, $previous = null)
	{
            parent::__construct(isset($message) && strlen($message)?$message:"Invalide Operation.", $code, $previous);
            $this->SendErrorCode(500);
        }
	
}
?>