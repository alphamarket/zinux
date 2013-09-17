<?php
namespace zinux\kernel\application;

require_once (dirname(__FILE__).'/../../baseZinux.php');

class bootstrap extends \zinux\baseZinux
{
    
    public function __construct()
    {
        $this->Initiate();
    }
    
    public function Initiate()
    {
        $app_dir = \zinux\kernel\utilities\fileSystem::resolve_path(PROJECT_ROOT."application");
        
        if(!$app_dir) return;
        
        $app_boot_file = \zinux\kernel\utilities\fileSystem::resolve_path($app_dir.DIRECTORY_SEPARATOR."bootstrap.php");
        
        if($app_boot_file)
            include_once $app_boot_file;
        
        $app_routes_file = \zinux\kernel\utilities\fileSystem::resolve_path($app_dir.DIRECTORY_SEPARATOR."routes.php");
        
        if($app_routes_file)
            include_once $app_routes_file;
        
    }  
    
    public function RunPrestrap(\zinux\kernel\routing\request &$request)
    {
        /**
         * Run application\routes.php
         */
        $cname = "application\\routes";
        
        if(class_exists($cname))
        {
            $c = new $cname;
            if(!method_exists($c, "getRoutes"))
                trigger_error("Method `$cname::GetRoutes` not found ...");
            else
            {
                \zinux\kernel\utilities\debug::_var($c->GetRoutes(),1);
            }
        }
        /**
         * Run application\bootstrap.php
         */
        $cname = "application\\boostrap";
        
        if(class_exists($cname))
        {
            $c = new $cname;

            $m = get_class_methods($c);

            foreach($m as $method)
            {
                if(\zinux\kernel\utilities\string::startsWith(strtoupper($method),"PRE_"))
                {
                    if(!is_callable(array($c, $method)))
                    {
                        trigger_error("The method $method found in bootstrap file `{$bs[1]}` but is not callable");
                    }
                    else
                        $c->$method($request);
                }
            }
        }
    }
    
    public function RunPoststrap(&$request)
    {
        $cname = "application\\boostrap";
        
        if(!class_exists($cname)) return;
        
        $c = new $cname;
        
        $m = get_class_methods($c);
        
        foreach($m as $method)
        {
            if(\zinux\kernel\utilities\string::startsWith(strtoupper($method),"POST_"))
            {
                if(!is_callable(array($c, $method)))
                {
                    trigger_error("The method $method found in bootstrap file `{$bs[1]}` but is not callable");
                }
                else
                    $c->$method($request);
            }
        }
    }
}
