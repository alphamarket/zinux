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
        $s = serialize($this->config_initializer);
        $sh = md5($s);
        # store the sig
        self::$load_cache_sig[$sh] = $s;
         # open cache
        $xc = new \zinux\kernel\caching\xCache(__CLASS__);
        # if configs cached
        if($xc->isCached(self::$load_cache_sig[$sh]))
        {
            # fetch cache configs
            $c = $xc->fetch(self::$load_cache_sig[$sh]);
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
        $xc->save(self::$load_cache_sig[$sh], $c);
__RETURN:
        # return configs
        return $c->config;
    }
    
    public static function GetConfig($path = NULL, $_ =NULL)
    {
        $conf = self::GetAll();
        foreach(func_get_args() as $arg)
        {
            if(!isset($conf[$arg]))
                throw new \zinux\kernel\exceptions\invalideArgumentException("index $arg does not exists in configuration array");
            $conf = $conf[$arg];
        }
        return $conf;
    }
    
    public static function Exists($path = NULL, $_ =NULL)
    {
        $conf = self::GetAll();
        foreach(func_get_args() as $arg)
        {
            if(!isset($conf[$arg]))
                return false;
            $conf = $conf[$arg];
        }
        return true;
    }
    
    public static function GetAll()
    {
        $xf = new \zinux\kernel\caching\xCache(__CLASS__);
        
        if(!self::$load_cache_sig || !count(self::$load_cache_sig))
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

    public function Initiate(){}
}
