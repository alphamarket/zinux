<?php
namespace iMVC\Exceptions;
require_once 'AppException.php';

class NotFoundException extends \iMVC\Exceptions\AppException
{
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct(strlen($message)?$message:"Page <b>".$_SERVER['REQUEST_URI']."</b> not found.", $code, $previous);
        if(!headers_sent ()) header('HTTP/1.1 404 Not Found');
    }
}