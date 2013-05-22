<?php
namespace iMVC\Exceptions;
class DBException extends \Exception
{
    protected $_detail;
    protected $_errorno;
    protected $_error_msg;

    public function __construct($message = null, $code = null , $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->createException($message? $message:  '<b>#'.  mysql_errno().'</b> - '.mysql_error());
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