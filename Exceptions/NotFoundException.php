<?php
namespace iMVC\Exceptions;
require_once 'AppException.php';

class NotFoundException extends \iMVC\Exceptions\AppException
{
    public function __construct($message = null, $code = null, $previous = null) {
        parent::__construct(isset($message) && strlen($message)?$message:"Page <b>".$_SERVER['REQUEST_URI']."</b> not found.", $code, $previous);
        $this->SendErrorCode(404);
    }
}