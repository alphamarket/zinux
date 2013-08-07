<?php
namespace iMVC\Exceptions;
require_once 'AppException.php';

class InvalideOperationException extends \iMVC\Exceptions\AppException
{
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct(strlen($message)?$message:"Invalide Operation.", $code, $previous);
        $this->SendErrorCode(500);
    }
}