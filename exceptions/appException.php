<?php
namespace iMVC\exceptions;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
class appException extends \Exception
{

	private $stack_trace;
	private $error_code;
	
	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	function __construct($message = null, $code = null, $previous = null)
	{
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
	
	function __destruct()
	{
	}
	
	/**
	 * 
	 * @param code
	 */
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
?>