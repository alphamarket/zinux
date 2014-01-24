<?php
namespace zinux\kernel\caching;
require_once 'arrayCache.php';
/**
 * Represents a connection to a set of memcache servers.
 * @link http://www.php.net/manual/en/class.memcache.php
 */
class memCache
{
    /**
     * default memcache port number
     */
   const DEFAULT_MEMCACHE_PORT = 11211;
   /**
    * default memcache host address
    */
    const DEFAULT_MEMCACHE_HOST = "127.0.0.1";
    /**
     * The name of the default cache file
     *
     * @var string
     */
    protected $_cachename = "default";
    /**
     * the memcache server
     * @var string
     */
    protected $_host;
    /**
     * memcache port number
     * @var integer
     */
    protected $_port;
    /**
     * save updates on destruction
     * @var boolean
     */
    protected $_save_on_destruction;
    /**
     * the memcache object
     * @var \Memcache
     */
    protected $mem_cache_instace = null;
    /**
     * internal soft cache
     * @var \zinux\kernel\caching\arrayCache
     */
    protected static $_soft_cache = array();
    /**
     * dirty flager
     * @var array 
     */
    protected static $_dirty = array();

    /**
     * Create a new instance of memCache
     * @param string $name [ optional ] the name of current cache
     * @param array $options the connection options, contains <pre>
     * {
     *      "host" : the memcache host address,
     *      "port" : the memcache server port number
     *      "save_on_destruction" : should updates get saved on destruction, or not [ default:false ]
     * } </pre>
     * @link http://www.php.net/manual/en/book.memcache.php Memcache official page
     */
    public function __construct($name = "default", $options = array())
    {
        # check if memcache exists?
        if (self::Is_memCache_Supported())
        {
            # invoke a memcache instance
            $this->mem_cache_instace = new \Memcache();
            # validate options
            if(!isset($options["host"]))
                $options["host"] = self::DEFAULT_MEMCACHE_HOST;
            if(!isset($options["port"]))
                $options["port"] = self::DEFAULT_MEMCACHE_PORT;
            if(!isset($options["save_on_destruction"]))
                $options["save_on_destruction"] = false;
            # set host address
            $this->_host = $options["host"];
            # set port address
            $this->_port = $options["port"];
            # save changes on destruction
            $this->_save_on_destruction = $options["save_on_destruction"];
            if($this->_save_on_destruction)
            {
                static $shutdown_in_effect_array = array();
                if(!isset($shutdown_in_effect_array[$name]))
                {
                    $shutdown_in_effect_array[$name] = true;
                    register_shutdown_function(array(__CLASS__, 'Dispose'), $name, $options);
                }
            }
            # set cache name
            $this->_cachename = $name;
            # test connection with configurations
            $this->testConnection(1);
            # load relative key list
            $this->loadcache();
        }
        else
        {
            \trigger_error("\Memcache not found for furture information check <a href='http://www.php.net/manual/en/book.memcache.php' target='__blank'>this</a>.", \E_USER_ERROR);
        }
    }
    public static function Dispose($cache_name, $options = array())
    {
        unset($options["save_on_destruction"]);
        $mc = new self($cache_name, $options);
        if(!$mc->is_dirty()) return;
        $mc->connect();
        if(!($res = $mc->mem_cache_instace->replace($mc->genKey(), $mc->get_internal_cache())))
        {
            $res = $mc->mem_cache_instace->set($mc->genKey(), $mc->get_internal_cache());
        }
        if($mc->mem_cache_instace->get($mc->genKey()) != $mc->get_internal_cache())
        {
            $mc->disconnect();
            throw new \Exception("It seems race condition happened on saving cache updates, updates not saved!");
        }
        $mc->disconnect();
    }
    /**
     * check if memcache supported with current system configurations
     * @return boolean
     */
    public static function Is_memCache_Supported()
    {
        return class_exists('\Memcache');
    }
    /**
     * test connection to memcache server
     * @param boolean $throw_exception throw exception if not connected
     * @return boolean TRUE if connection is OK, otherwise false
     * @throws \zinux\kernel\caching\MemcachedException
     */
    public function testConnection($throw_exception = 0)
    {
        try
        {
            $this->connect();
            $this->disconnect();
            return true;
        }
        catch(\Exception $mce)
        {
            if($throw_exception)
                throw $mce;
            return false;
        }
    }
    /**
     * connect to memCache and load keys
     * @link http://www.php.net/manual/en/memcache.connect.php
     */
     function connect()
    {
        if (!@$this->mem_cache_instace->connect($this->_host, $this->_port))
        {
            $this->mem_cache_instace = null;
            throw new \Exception("Unable to connect to memCacher server at '{$this->_host}:{$this->_port}'.");
        }
    }
    /**
     * loads relative key lists to current cache name
     */
    protected function loadcache()
    {
        $this->connect();
        if(!isset(self::$_soft_cache[$this->_cachename]))
        {
            $cache = $this->mem_cache_instace->get($this->genKey());
            self::$_soft_cache[$this->_cachename] = ($cache === false ? new \zinux\kernel\caching\arrayCache($this->genKey()) : $cache);
        }
        $this->disconnect();
    }
    protected function is_dirty()
    {
        return isset(self::$_dirty[$this->_cachename]) ? self::$_dirty[$this->_cachename] : FALSE;
    }
    protected function make_dirty()
    {
        self::$_dirty[$this->_cachename] = TRUE;
    }
    /**
     * get relative internal cache
     * @return \zinux\kernel\caching\arrayCache
     */
    protected function get_internal_cache()
    {
        return isset(self::$_soft_cache[$this->_cachename]) ? self::$_soft_cache[$this->_cachename] : NULL;
    }
    /**
     * initialize relative internal cache
     * @return \zinux\kernel\caching\arrayCache
     */
    protected function init_internal_cache()
    {
        self::$_soft_cache[$this->_cachename] = new \zinux\kernel\caching\arrayCache($this->genKey());
        return self::$_soft_cache[$this->_cachename];
    }
    /**
     * deinitialize relative internal cache
     */
    protected function deinit_internal_cache()
    {
        unset(self::$_soft_cache[$this->_cachename]);
        $this->make_dirty();
        $this->init_internal_cache();
    }
    /**
     * disconnect from memCache
     * @link http://www.php.net/manual/en/memcache.close.php
     */
     function disconnect()
    {
        $this->mem_cache_instace->close();
    }
    /**
     * generates keys from the $key
     * @param string $key
     * @return string
     */
     function genKey(\zinux\kernel\caching\memCache $mc = NULL)
    {
        if(!$mc)
            $mc = $this;
        return "cache-{$mc->_cachename}";
    }
    /**
     * Retrieve cached data by its key
     *
     * @param string $key
     * @return mixed
     * @link http://www.php.net/manual/en/memcache.get.php
     */
    public function fetch($key, $meta = false)
    {
        $iCache = $this->get_internal_cache();
        if($iCache && $iCache->isCached($key))
            return $this->get_internal_cache()->fetch($key, $meta);
        return NULL;
    }
    /**
     * @param timespan $expiration the expiration will sum with NOW datetime
     */
    public function setExpireTime($key, $expiration)
    {
        if(!$this->get_internal_cache()->isCached($key)) return FALSE;
        $this->get_internal_cache()->setExpireTime($key, $expiration);
        $this->make_dirty();
        if($this->_save_on_destruction) return true;
        $this->mem_cache_instace->replace($this->genKey(), $this->get_internal_cache());
    }
    /**
     * Store data in the cache
     *
     * @param string $key
     * @param mixed $data
     * @param timespan [ optional ] $expiration the expiration in seconds will sum with NOW datetime
     * @return object
     * @link http://www.php.net/manual/en/memcache.set.php
     */
    public function save($key, $data, $expire = 0) 
    {
        $this->get_internal_cache()->save($key, $data, $expire);
        $this->make_dirty();
        if($this->_save_on_destruction) return true;
        $this->connect();
        if(!($res = $this->mem_cache_instace->replace($this->genKey(), $this->get_internal_cache())))
        {
            $res = $this->mem_cache_instace->set($this->genKey(), $this->get_internal_cache());
        }
        if($this->mem_cache_instace->get($this->genKey()) != $this->get_internal_cache())
        {
            $this->disconnect();
            return FALSE;
        }
        $this->disconnect();
        return $res;
    }
    /**
     * Erase cached entry by its key
     *
     * @param string $key
     * @return boolean TRUE if deletion was successful; otherwise FALSE
     * @link http://www.php.net/manual/en/memcache.get.php
     */
    public function delete($key)
    {
        if(!$this->get_internal_cache()->isCached($key)) return TRUE;
        $this->get_internal_cache()->delete($key);
        $this->make_dirty();
        if($this->_save_on_destruction) return true;
        $this->connect();
        $res = $this->mem_cache_instace->replace($this->genKey(), $this->get_internal_cache());
        if($this->mem_cache_instace->get($this->genKey()) != $this->get_internal_cache())
        {
            $this->disconnect();
            return FALSE;
        }
        $this->disconnect();
        return $res;
    }
    /**
     * Retrieve all cached data
     *
     * @return array
     */
    public function fetchAll($meta = false)
    {
        return $this->get_internal_cache()->fetchAll($meta);
    }
    /**
     * Erase all cached entries
     */
    public function deleteAll()
    {
        $this->connect();
        $this->mem_cache_instace->delete($this->genKey());
        $this->disconnect();
        $this->deinit_internal_cache();
    }
    /**
     * Check whether data accociated with a key
     *
     * @param string $key
     * @return boolean
     */
    public function isCached($key)
    {
        return $this->get_internal_cache()->isCached($key);
    }
    /**
     * Cache name Setter
     *
     * @param string $name
     * @return object
     */
    public function setCacheName($name) {
        $this->_cachename = $name;
        
    }

    /**
     * Cache name Getter
     *
     * @return void
     */
    public function getCacheName($real_name = 0){
        return $real_name ? $this->genKey() : $this->_cachename;
    }

    /**
     * Get count of items that has been cached
     * @return integer
     */
    public function count()
    {
        return $this->get_internal_cache()->count();
    }
    /**
     * Flush all existing items at the server
     * @param boolean $wait_on_flush Note that after flushing, you have to wait a certain amount of time (in many case < 1s) to be able to write to Memcached again. make if 0/1 to flag to wait on flush or not.
     * @link http://www.php.net/manual/en/memcache.flush.php
     */
    public function flush($wait_on_flush = 1)
    {
        $this->connect();
        $this->mem_cache_instace->flush();
        $this->disconnect();
        self::$_soft_cache = array();
        $this->deinit_internal_cache();
        if($wait_on_flush)
            \sleep(1);
    }
}