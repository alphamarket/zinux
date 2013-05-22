<?php
namespace iMVC;
defined("IMVC_PATH") || define('IMVC_PATH', realpath(__DIR__));

if(!defined('IMVC_INCLUDE_PATH'))
{
    define('IMVC_INCLUDE_PATH', realpath(__DIR__));
    set_include_path(implode(":", array(get_include_path(), IMVC_PATH)));
}

abstract class BaseMVC extends \stdClass
{    
    public abstract function Initiate();
    
    /**
     * All temp vars should be named starting with '_'
     * This function unsets them.
     */
    public function Dispose()
    {
        foreach($this as $key => $value)
        {
            if($key[0] == "_")
                unset($this->$key);
        }
    }
    
    public function SetRequest(\iMVC\Routing\Request $request)
    {
        $GLOBALS['imvc']['register']['request'] = json_encode($request);
    }
    /**
     * @return \iMVC\Routing\Request
     * @throws AppExceptions if there is no registed request
     */
    public function GetRequest()
    {
        if(!$this->IsRequestRegistered())
        {
            require_once IMVC_PATH.'/Exceptions/AppException.php';
            throw new \iMVC\Exceptions\AppException("There is no previously registered request");
        }
        return json_decode($GLOBALS['imvc']['register']['request']);
    }
    
    public function IsRequestRegistered()
    {
        return isset($GLOBALS['imvc']['register']['request']) &&
                $GLOBALS['imvc']['register']['request']!=NULL;
    }
    
}