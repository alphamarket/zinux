<?php
namespace zinux\kernel\application;

require_once dirname(__FILE__).'/../../baseZinux.php';

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:21
 */
class config extends \zinux\baseZinux
{
    /**
     * cache string
     * @var string
     */
    public static $load_cache_sig = NULL;
    /**
     * Config Initializer
     * @var baseConfigLoader 
     */
    public $config_initializer;
    
    public function __construct(baseConfigLoader $config_initializer)
    {
        if(!$config_initializer)
            throw new \zinux\kernel\exceptions\invalideArgumentException("The config initializer cannot be NULL");
        
        $this->config_initializer = $config_initializer;
    }
    /**
     * Fetch config file's values
     */
    public function Load()
    {
        # sig. hash
        $sh = sha1(\serialize($this->config_initializer));
        # store the sig
        self::$load_cache_sig[$sh] = $sh;
        # open cache
        # don't use xCache it will overload the session file
        $fc = new \zinux\kernel\caching\fileCache(__CLASS__);
        # if configs cached
        if($fc->isCached(self::$load_cache_sig[$sh]))
        {
            # fetch cache configs
            $c = $fc->fetch(self::$load_cache_sig[$sh]);
            # check if file modified
            if($c->modif_time==filemtime($this->config_initializer->file_address))
                goto __RETURN;
        }
        # create new container
        $c = new \stdClass();
        # initliate the config initializer
        $this->config_initializer->Initiate();
        # execute the config initialize
        $c->config = $this->config_initializer->Execute();
        # validate return type 
        if(!is_array($c->config))
            throw new \zinux\kernel\exceptions\invalideOperationException("The config initliazer output should be and array() but recieved ".gettype($c->config));
        # set file modified datetime
        $c->modif_time = filemtime($this->config_initializer->file_address); 
        # cache the result
        $fc->save(self::$load_cache_sig[$sh], $c);
__RETURN:
        # return configs
        return $c->config;
    }
    /**
     * Get related config value
     * @param string $path
     * @param string $_
     * @return string the config value
     * @throws \zinux\kernel\exceptions\invalideArgumentException if the config path not found
     */
    public static function GetConfig($path, $splitter = ".")
    {
        if(!$path || !\is_string($path))
            throw new \zinux\kernel\exceptions\invalideArgumentException("The path not well-initialized!");
        $conf = self::GetAll();
        foreach(array_filter(\explode($splitter, $path)) as $arg)
        {
            if(!isset($conf[$arg]))
                throw new \zinux\kernel\exceptions\invalideArgumentException("Index `$arg` does not exists in configuration array");
            $conf = $conf[$arg];
        }
        return $conf;
    }
    
    /**
     * Check if an config loaded and exists
     * @param string $path
     * @param string $_
     * @return boolean
     */
    public static function Exists($path, $splitter = ".")
    {
        if(!$path || !\is_string($path))
            throw new \zinux\kernel\exceptions\invalideArgumentException("The path not well-initialized!");
        $conf = self::GetAll();
        foreach(array_filter(\explode($splitter, $path)) as $arg)
        {
            if(!isset($conf[$arg]))
                return false;
            $conf = $conf[$arg];
        }
        return true;
    }
    /**
     * Get all loaded config
     * @return array
     * @throws \zinux\kernel\exceptions\invalideOperationException if no config ever loaded
     */
    public static function GetAll()
    {
        # don't use xCache it will overload the session file
        $xf = new \zinux\kernel\caching\fileCache(__CLASS__);
        
        if(!self::HasLoaded())
            throw new \zinux\kernel\exceptions\invalideOperationException("The config file has not been loaded");
        
        $conf = array();
        
        foreach(self::$load_cache_sig as $cache_sig)
        {
            if($xf->isCached($cache_sig))
            {
                $c = $xf->fetch($cache_sig);
                $conf = array_merge($conf , $c->config);
            }
        }
        
        return $conf;
    }
    /**
     * Check if any config loaded
     * @return boolean 
     */
    public static function HasLoaded()
    {
        if(!self::$load_cache_sig || !count(self::$load_cache_sig))
            return false;
        return true;
    }
}
