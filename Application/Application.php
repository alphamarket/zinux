<?php
namespace iMVC\APP;

require_once __DIR__.'/../BaseMVC.php';
require_once 'Exceptions/include.all.inc';
require_once 'Security/include.all.inc';
require_once 'Routing/Router.php';
require_once 'Model/BaseModel.php';
require_once 'Tools/Debug.php';
require_once 'Controller/BaseController.php';
require_once 'Tools/Config.php';
require_once 'DB/DBInitializer.php';
use \ActiveRecord;

class Application extends \iMVC\BaseMVC 
{
    public function __construct() {
        $this->Initiate();
    }
    public function Initiate()
    {
        $this->_startup_invoked = false;
    }
    /**
     * Runs the application 
     */
    public function Run()
    {
        if(!$this->_startup_invoked)
        {
            trigger_error ("Application is not started up. running without configurations... ");
            $this->Startup ("");
        }
        $r = new \iMVC\Routing\Router();
        
        $req = new \iMVC\Routing\Request();
        
        $dbi = new \iMVC\DB\DBInitializer();
        
        $dbi->InitActiveRecord($req);
       
        $r->Run($req);
    }
    /**
     * Startup and making application's ready with passed configuration
     * @param string $config_file_address 
     */
    public function Startup($config_file_address)
    {
        $config_file_address = realpath($config_file_address);
        
        if(!file_exists($config_file_address))
        {
            trigger_error ("$config_file_address config file does not exists... ");
            goto __END;
        }
        if(!defined('RUNNING_ENV'))
        {
            trigger_error ("RUNNING_ENV is not defined; autosetting to DEVELOPMENT.");
            define('RUNNING_ENV', "DEVELOPMENT");
            
        }
        $this->config_file_address = $config_file_address;
        $c = new \iMVC\Tools\Config();
        $c->Load($config_file_address, true, RUNNING_ENV);
        $this->Initiate();
__END:
        if(!isset($GLOBALS[CONFIGS]['imvc']['modules']['path']))
            $mp = IMVC_PATH.'/../Modules/';
        else 
        {
            $mp = IMVC_PATH."/../".$GLOBALS[CONFIGS]['imvc']['modules']['path'];
        }
        defined('MODULE_PATH') || define('MODULE_PATH', realpath($mp)."/");
        $this->_startup_invoked = true;
    }
    /**
     * Shutdowns application 
     */
    public function Shutdown()
    {
        $this->Dispose();
    }
    
    
}