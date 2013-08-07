<?php
namespace iMVC\Exceptions;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SecurityException
 *
 * @author dariush
 */
require 'AppException.php';
class SecurityException extends AppException
{
    public function __construct($message =null, $code=null, $previous=null) {
        if(!$message)
        {
            $message = "Security error";
        }
        parent::__construct($message, $code, $previous);
        $this->SendErrorCode(403);
    }
}

?>
