<?php
namespace iMVC;
defined("IMVC_PATH") || define('IMVC_PATH', realpath(__DIR__)."/");

if(!defined('IMVC_INCLUDE_PATH'))
{
    define('IMVC_INCLUDE_PATH', realpath(__DIR__));
    set_include_path(implode(":", array(get_include_path(), IMVC_PATH)));
}

abstract class BaseMVC extends \stdClass
{    
    const SERIALIZED = "SERIALIZED";
    const XML = "XML";
    const HTML = "HTML";
    const JSON = "JSON";
    
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
        $GLOBALS['imvc']['register']['request'] = $request;
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
        return $GLOBALS['imvc']['register']['request'];
    }
    
    public function IsRequestRegistered()
    {
        return isset($GLOBALS['imvc']['register']['request']) &&
                $GLOBALS['imvc']['register']['request']!=NULL;
    }
    
    function IsSecure(array $array, array $existance_array = array(), array $check_sum_array = array(), $do_exception = 1, $verbose_exceptions = 0)
    {
        if(!isset($array))
        {
            if($do_exception)
                throw new \InvalidArgumentException($verbose_exceptions?"The array is not setted":"Invalid Request");
            else return false;
        }
        
        if(!count($check_sum_array) && !count($existance_array))
            throw new \InvalidArgumentException("\$existance_array is not supplied but demads operation on \$check_sum_array!!");
        
        if(count($existance_array) && !count($existance_array))
        {
            if($do_exception)
                throw new \InvalidArgumentException($verbose_exceptions?"The target array in empy!":"Invalid Request");
            else return false;
        }  
        foreach($existance_array as $value)
        {
            if(!isset($array[$value]))
            {
                if($do_exception)
                    throw new \InvalidArgumentException($verbose_exceptions?"The argumen `$value` didn't supplied":"Invalid Request");
                else return false;
            }
        }
        foreach($check_sum_array as $key=> $value)
        {
            if($array[$key] != $value)
            {
                if($do_exception)
                    throw new \InvalidArgumentException($verbose_exceptions?"The `$key`'s value didn't match with `$value`":"Invalid Request");
                return false;
            }
        }
        return true;
    }
    public function GetSecureGET(array $based_upon)
    {
        $t = time();
        $based_upon[] = $t;
        $tn = "s_".substr(sha1('t'), 0,5);
        $link ="&$tn=$t";
        require_once 'Security/Hash.php';
        $h = Security\Hash::Generate(implode("", $based_upon));
        $hn = "s_".substr(sha1('h'), 0,5);
        $link = $link."&$hn=$h";
        return $link;
    }
    public function IsGETSecured(array $based_upon)
    {
        $tn = "s_".substr(sha1('t'), 0,5);
        $hn = "s_".substr(sha1('h'), 0,5);
        $this->IsSecure($_GET, array($tn,$hn),array());
        $based_upon[] = $_GET[$tn];
        require_once 'Security/Hash.php';
        $h = Security\Hash::Generate(implode("", $based_upon));
        $this->IsSecure($_GET, array($tn,$hn),array($hn=>$h));
        return true;
    }
}