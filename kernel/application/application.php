<?php
namespace zinux\kernel\application;

require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
class application extends \zinux\baseZinux
{
    /**
     *
     * @var plugin
     */
    public $plugins;
    
    protected static $config_initializer = NULL;
    /**
     * db initializer instance
     * @var \zinux\kernel\application\baseInitializer
     */
    protected $dbInit;
    
    function __construct($module_path = "", dbInitializer $dbi = NULL)
    {            
            $this->Initiate();
            
            if(!file_exists(\zinux\kernel\utilities\fileSystem::resolve_path($module_path)))
                die("Module directory not found!");
            
            defined('MODULE_ROOT') || define('MODULE_ROOT',  \zinux\kernel\utilities\fileSystem::resolve_path($module_path."/"));
            
            $this ->dbInitializer($dbi)
                    ->plugins =  new plugin();
            # create a application bootstrap
            $app_boot = new bootstrap();
            # run the application bootstrap
            $app_boot->Run();
    }

    public function Initiate()
    {
        $this->_startup_invoked = false;
    }
    
    public function dbInitializer(dbInitializer $dbi = NULL)
    {
        $this->dbInit = $dbi;
        return $this;
    }

    /**
     * Run the application
     */
    public function Run()
    {
            if(!$this->_startup_invoked)
            {
                $this->Startup();
            }
            $r = new \zinux\kernel\routing\router();

            $request = new \zinux\kernel\routing\request();
            
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
    public function Startup(baseConfigLoader $config_initializer = NULL)
    {
            # no initializer return
            if(!$config_initializer) return $this;
            # cache the $config in
            $this->SetConfiginItializer($config_initializer);
            # create config instance
            $config = new config($config_initializer);
            # load configs
            $config->Load();
            # set default module root
            defined('MODULE_ROOT') || define('MODULE_ROOT',  \zinux\kernel\utilities\fileSystem::resolve_path(ZINUX_ROOT.'/../modules/')."/");
            # check startup invoked
            $this->_startup_invoked = true;
            # return this instance
            return $this;
    }
    
    public function SetConfiginItializer(baseConfigLoader $config_initializer)
    {
        if(!$config_initializer)
            throw new \zinux\kernel\exceptions\invalideArgumentException;
        self::$config_initializer = $config_initializer;
        return $this;
    }
}