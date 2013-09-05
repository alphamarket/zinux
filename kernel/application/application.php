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

	function __construct()
	{
            $this->Initiate();
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
                trigger_error ("Application is not started up. running without configurations... ");
                $this->Startup ("");
            }
            $r = new \iMVC\kernel\routing\router();

            $req = new \iMVC\kernel\routing\request();

            $dbi = new \iMVC\db\ActiveRecord\initializer();

            $dbi->InitActiveRecord($req);

            $r->Run($req);
	}

	/**
	 * Shutdowns application
	 */
	public function Shutdown()
	{
            $this->Dispose();
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
                trigger_error ("No config file supplied.");
                goto __END;
            }
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
    __END:
            if(!isset($GLOBALS[CONFIGS]['imvc']['modules']['path']))
                $mp = IMVC_PATH.'/../modules/';
            else 
            {
                $mp = IMVC_PATH."/../".$GLOBALS[CONFIGS]['imvc']['modules']['path'];
            }
            defined('MODULE_PATH') || define('MODULE_PATH', realpath($mp)."/");
            $this->_startup_invoked = true;
	}

}
?>