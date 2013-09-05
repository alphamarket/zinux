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
	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	public function __construct($message=null, $code=null, $previous=null) 
        {
            if(!isset($message) || !strlen($message))
                    $message = "The method has not implemented...";
            parent::__construct(isset($message) && strlen($message)?$message:"Page <b>".$_SERVER['REQUEST_URI']."</b> not found.", $code, $previous);
            $this->SendErrorCode(404);
        }

}
?>