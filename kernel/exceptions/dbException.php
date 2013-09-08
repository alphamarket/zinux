<?php
namespace zinux\kernel\exceptions;

require_once ('appException.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class dbException extends appException
{

	protected $_detail;
	protected $_errorno;
	protected $_error_msg;

	/**
	 * 
	 * @param message
	 * @param code
	 * @param previous
	 */
	public function __construct($message = null, $code = null , $previous = null)
        {
            parent::__construct($message, $code, $previous);
            $this->createException($message? $message:  '<b>#'.  mysql_errno().'</b> - '.mysql_error());
            $this->SendErrorCode(500);
        }
        /**
         * automaticly create mysql exception
         * @param string details about the current mysql exception 
         */
        public function createException($detail = null) 
        {
            $this->message = $detail;
            $this->_errorno = mysql_errno();
            $this->_error_msg = mysql_error();
        }

        /**
         * get generated mysql error number
         * @return int 
         */
        public function getErrorNo()
        {
            return $this->_errorno;
        }
        /**
         * get generated mysql error message
         * @return string 
         */
        public function getErrorMessage()
        {
            return $this->_error_msg;
        }

}
?>