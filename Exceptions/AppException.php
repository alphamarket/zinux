<?php
namespace iMVC\Exceptions;
    class AppException extends \Exception
    {
        private $stack_trace;
        
        public function __construct($message, $code, $previous) {
            parent::__construct($message, $code, $previous);
            try
            {
                throw new \Exception();
            }
            catch(\Exception $e)
            {
                $p = explode("\n", $e->getTraceAsString());
                $s = "";
                for($i=1;$i<count($p);$i++)
                    $s .= $p[$i].'<br />';
                $this->stack_trace = $s;
            }
        }
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
        public function GetErrorTraceAsString(){return $this->stack_trace;}
    }
