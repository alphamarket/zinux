<?php
namespace iMVC\Exceptions;
require_once 'AppException.php';

class InvalideArgumentException extends \iMVC\Exceptions\AppException
{
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct(strlen($message)?$message:"Invalid Argument.", $code, $previous);
        header('HTTP/1.1 500 Internal Server Error');
    }
}