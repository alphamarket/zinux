<?php
namespace iMVC\kernel\application;

require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
class application extends \iMVC\baseiMVC
{
    protected static $config_file_address = NULL;
    /**
     * db initializer instance
     * @var \iMVC\kernel\db\basedbInitializer
     */
    protected $dbInit;
    
    function __construct($module_path = "", \iMVC\kernel\db\basedbInitializer $dbi = NULL)
    {
            $this->Initiate();
            
            if(!file_exists(\iMVC\kernel\utilities\fileSystem::resolve_path($module_path)))
                die("Module directory not found!");
            
            defined('MODULE_ROOT') || define('MODULE_ROOT',  \iMVC\kernel\utilities\fileSystem::resolve_path($module_path."/"));
            
            $this->dbInit =$dbi;
    }

    public function Initiate()
    {
        $this->_startup_invoked = false;
    }

    /**
     * Run the application
     */
    public function Run()
    {
            if(!$this->_startup_invoked)
            {
                trigger_error ("Application has not started up. running without configurations...");
                $this->Startup ("");
            }
            $r = new \iMVC\kernel\routing\router();

            $request = new \iMVC\kernel\routing\request();
            
            $request->Process();
            
            if($this->dbInit)
            {
                $this->dbInit->Initiate();
                $this->dbInit->Execute($request);
            }
            
            $r->Run($request);
            
            return $this;
    }

    /**
     * Shutdowns application
     */
    public function Shutdown()
    {
            $this->Dispose();
            return $this;
    }

    /**
     * Startup and making application's ready with passed configuration file
     * 
     * @param config_file_address
     */
    public function Startup($config_file_address = NULL)
    {
            if(!$config_file_address) 
            {
                if(\iMVC\kernel\utilities\fileSystem::resolve_path(self::$config_file_address))
                {
                    $config_file_address = self::$config_file_address;
                }
                else
                {
                    trigger_error ("No config file supplied.");
                    goto __END;
                }
            }
            $this->SetConfigFile($config_file_address);

            if(!file_exists($config_file_address) || !is_file($config_file_address))
            {
                trigger_error ("$config_file_address config file does not exists... ");
                goto __END;
            }
            if(!defined('RUNNING_ENV'))
            {
                trigger_error ("RUNNING_ENV is not defined; autosetting to DEVELOPMENT.");
                define('RUNNING_ENV', "DEVELOPMENT");
            }
            $c = new \iMVC\kernel\utilities\config($config_file_address);
            $c->Load(RUNNING_ENV, true);
    __END:
            defined('MODULE_ROOT') || define('MODULE_ROOT',  \iMVC\kernel\utilities\fileSystem::resolve_path(IMVC_ROOT.'/../modules/')."/");
            $this->_startup_invoked = true;
            return $this;
    }
    
    public function SetConfigFile($address)
    {
        $address = \iMVC\kernel\utilities\fileSystem::resolve_path($address);
        if(!$address)
            throw new \iMVC\kernel\exceptions\notFoundException("Config file does not exists ...");
        self::$config_file_address = $address;
        return $this;
    }
}
?>