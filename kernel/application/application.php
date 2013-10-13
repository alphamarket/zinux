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
    protected $plugins;
    /**
     * config initializer
     * @var baseConfigLoader
     */
    protected $config_initializer;
    /**
     * db initializer instance
     * @var \zinux\kernel\application\baseInitializer
     */
    protected $dbInit;
    /**
     * Application boostrap
     * @var bootstrap
     */
    protected $applicationBoostrap;
    /**
     * Router boostrap
     * @var \zinux\kernel\routing\routerBootstrap
     */
    protected $routerBoostrap;
    /**
     * Router
     * @var \zinux\kernel\routing\router
     */
    protected $router;
    
    public function __construct($module_path = "../modules")
    {            
        # this cannot move into init()
        $this->_startup_invoked = false;
        # initialize current application instance
        $this->Initiate();
        # a fail safe for module dir existance
        if(!file_exists(\zinux\kernel\utilities\fileSystem::resolve_path($module_path)))
            die("Module directory not found!");
        # define module ROOT if not defined
        defined('MODULE_ROOT') || define('MODULE_ROOT',  \zinux\kernel\utilities\fileSystem::resolve_path($module_path."/"));
    }
    
    public function SetDBInitializer(dbInitializer $dbi)
    {
        $this->dbInit = $dbi;
        return $this;
    }
    
    public function SetCacheDirectory($cache_dir)
    {
        \zinux\kernel\caching\fileCache::RegisterCachePath($cache_dir);
        return $this;
    }
    
    public function SetRouterBootstrap(\zinux\kernel\routing\routerBootstrap $rb)
    {
        \zinux\kernel\routing\router::RegisterBootstrap($rb);
        return $this;
    }
    public function SetConfigIniliazer(baseConfigLoader $config_initializer)
    {
        $this->config_initializer = $config_initializer;
        return $this;
    }
    public function registerPlugin($name, $plugin_addres = "")
    {
        $this->plugins->registerPlugin($name, $plugin_addres);
        return $this;
    }
    /**
     * Sets bootstrap for application
     * @param \zinux\kernel\application\applicationBootstrap $ab
     * @return \zinux\kernel\application\application
     */
    public function SetBootstrap(applicationBootstrap $ab)
    {
        applicationBootstrap::RegisterBootstrap($ab);
        return $this;
    }

    public function Initiate()
    {
        # create an router
        $this->router = new \zinux\kernel\routing\router;
        # initialize plugins
        $this->plugins =  new plugin();
        # create a request instance
        $this->request = new \zinux\kernel\routing\request();
        # create a application bootstrap
        $this->applicationBoostrap = new applicationBootstrap();
         return $this;
    }

    /**
     * Run the application
     */
    public function Run()
    {
        $this->Initiate();
        
        if(!$this->_startup_invoked)
        {
            $this->Startup();
        }
        # process the request
        $this->request->Process();
        # run a pre strap opt.
        $this->applicationBoostrap->RunPrestrap($this->request);
        # process the request 
        $this->router->Process($this->request);
        # run the router
        $this->router->Run();

        return $this;
    }

    /**
     * Shutdowns application [ Runs Application's Poststraps in bootstrap file ]
     */
    public function Shutdown()
    {
            $this->applicationBoostrap->RunPoststrap($this->request);
            $this->Dispose();
            return $this;
    }

    /**
     * Startup and making application's ready with passed configuration file [ Loads configurations and db ]
     * 
     * @param config_file_address
     */
    public function Startup()
    {
        # no initializer return
        if(!$this->config_initializer) goto __DB_INIT;
        # create config instance
        $config = new config($this->config_initializer);
        # load configs
        $config->Load();
__DB_INIT:
        if($this->dbInit)
        {
            $this->dbInit->Initiate();
            $this->dbInit->Execute($this->request);
        }
        # set default module root
        defined('MODULE_ROOT') || define('MODULE_ROOT',  \zinux\kernel\utilities\fileSystem::resolve_path(ZINUX_ROOT.'/../modules/')."/");
        # check startup invoked
        $this->_startup_invoked = true;
        # return this instance
        return $this;
    }
}