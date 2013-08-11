<?php
namespace iMVC\Exceptions;
require_once 'AppException.php';

class InvalideArgumentException extends \iMVC\Exceptions\AppException
{
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct(isset($message) && strlen($message)?$message:"Invalid Argument.", $code, $previous);
        $this->SendErrorCode(500);
    }
}