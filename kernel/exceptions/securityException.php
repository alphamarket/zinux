<?php
namespace zinux\kernel\exceptions;
require_once 'appException.php';

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class securityException extends appException
{
    public function __construct($message =null, $code=null, $previous=null) {
        if(!isset($message) || !strlen($message))
        {
            $message = "Security error";
        }
        parent::__construct($message, $code, $previous);
        $this->SendErrorCode(403);
    }
}
?>
