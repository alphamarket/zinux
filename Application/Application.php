<?php
namespace iMVC\APP;

require_once __DIR__.'/../BaseMVC.php';
require_once 'Routing/Router.php';
require_once 'Model/BaseModel.php';
require_once 'Tools/Debug.php';
require_once 'Exceptions/include.all.inc';
require_once 'Controller/BaseController.php';
require_once 'Tools/Config.php';
require_once 'DB/php-activerecord/ActiveRecord.php';
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
        
        $this->LoadActiveRecord($req);
       
        $r->Run($req);
    }
    /**
     * Startup and making application's ready with passed configuration
     * @param string $config_file_address 
     */
    public function Startup($config_file_address)
    {
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
        if(!isset($GLOBALS['CONFIGS']['imvc']['modules']['path']))
            $mp = IMVC_PATH.'/../Modules/';
        else 
        {
            $mp = IMVC_PATH."/../".$GLOBALS['CONFIGS']['imvc']['modules']['path'];
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
    
    protected function LoadActiveRecord(\iMVC\Routing\Request $request)
    { 
        if(!isset($GLOBALS['CONFIGS']['db']))
        {
            trigger_error("In configuration db setting did not found, ActiveRecord is not loaded ....");
            return;
        }
        if(isset($GLOBALS['CONFIGS']['db']['suppress']) && $GLOBALS['CONFIGS']['db']['suppress'])
        {
            trigger_error("Suppressed database active record loader.");
            return;
        }
        if(!isset($GLOBALS["CONFIGS"]['db']['type']))
            $GLOBALS["CONFIGS"]['db']['type'] = "mysql";

        $db_type = $GLOBALS["CONFIGS"]['db']['type'];
        $username = $GLOBALS['CONFIGS']['db']['username'];
        $password = $GLOBALS['CONFIGS']['db']['password'];
        $host = $GLOBALS['CONFIGS']['db']['host'];
        $db_name = $GLOBALS['CONFIGS']['db']['name'];

        # define a dynamic connection string according to incomming request
        $connections = array(
            RUNNING_ENV => "$db_type://{$username}:{$password}@{$host}/{$db_name}",
        );

        # must issue a "use" statement in your closure if passing variables
        \ActiveRecord\Config::initialize(function($cfg) use ($connections, $request)
        {
            $db_m_path = MODULE_PATH."{$request->module}/Models/DB";

            if(!file_exists($db_m_path))
            {
                trigger_error("Database model's directory didn't found at '$db_m_path'");
            }

            \ActiveRecord\Config::instance()->set_model_directory($db_m_path);

            $cfg->set_connections($connections);
            
            $cfg->set_default_connection(RUNNING_ENV);
            
        });
        # test database connection
        try
        {
             ActiveRecord\Connection::instance($connections[RUNNING_ENV])->query("SHOW TABLES;");
        }
        catch (\Exception $pdoe)
        {
            trigger_error("Could not stablish a connection with database : '{$connections[RUNNING_ENV]}'.");
            echo "<br />Message: <br />";
            echo \iMVC\Tools\Debug::_var($pdoe->getMessage());
        }
    }
}