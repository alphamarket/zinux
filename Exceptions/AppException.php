<?php
namespace iMVC\Exceptions;
    class AppException extends \Exception
    {
        private $error_code;
        
        public function SendErrorCode($code = NULL)
        {
            if($code)
                $this->error_code = $code;
            if(!headers_sent ())
            {
                header('HTTP/1.1 '.$this->error_code);
                return true;
            }
            return false;
        }
        
        public function GetErrorCode(){return $this->error_code;}
    }
