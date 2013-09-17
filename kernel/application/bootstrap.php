<?php
namespace zinux\kernel\application;

require_once (dirname(__FILE__).'/../../baseZinux.php');

class bootstrap extends \zinux\baseZinux
{
    public function Initiate()
    {
        
    }  
    
    public function Run()
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
}
