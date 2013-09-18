<?php
namespace zinux\kernel\application;

require_once (dirname(__FILE__).'/../../baseZinux.php');

class applicationBootstrap extends \zinux\baseZinux
{
    
    protected  static $_boostraps = array();
    
    public function __construct()
    {
        $this->Initiate();
    }
    
    public function Initiate(){}
    
    public function RunPrestrap(\zinux\kernel\routing\request &$request)
    {
         foreach(self::$_boostraps as $c)
        {
            $m = get_class_methods($c);

            foreach($m as $method)
            {
                if(\zinux\kernel\utilities\string::startsWith(strtoupper($method),"PRE_"))
                {
                    if(!is_callable(array($c, $method)))
                    {
                        trigger_error("The method $method found in bootstrap instance `{$c}` but is not callable");
                    }
                    else
                        $c->$method($request);
                }
            }
        }
    }
    
    public function RunPoststrap(&$request)
    {
        foreach(self::$_boostraps as $c)
        {
            $m = get_class_methods($c);

            foreach($m as $method)
            {
                if(\zinux\kernel\utilities\string::startsWith(strtoupper($method),"POST_"))
                {
                    if(!is_callable(array($c, $method)))
                    {
                        trigger_error("The method $method found in bootstrap instance `{$c}` but is not callable");
                    }
                    else
                        $c->$method($request);
                }
            }
        }
    }
    
    public static function RegisterBootstrap(applicationBootstrap $appBootstrap, $name = "")
    {
        if(strlen($name))
            self::$_boostraps[$name] = $appBootstrap;
        else
            self::$_boostraps[] = $appBootstrap;
    }
    
    public static function UnregisterBootstrap($name = "default")
    {
        unset(self::$_boostraps[$name]);
    }
}
