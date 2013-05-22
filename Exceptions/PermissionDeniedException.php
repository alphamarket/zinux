<?php

namespace iMVC\Exceptions;
/**
 * Description of permissionDeniedException
 *
 * @author dariush
 */
require_once 'AccessDeniedException.php';
class PermissionDeniedException extends \iMVC\Exceptions\AccessDeniedException
{
    public function __construct($message = null, $code = null, $previous = null) {
        if(!$message)
        {
            $message = "You <b>do not</b> have permission to view this page!";
        }
        parent::__construct($message, $code, $previous);
    }
}

?>
