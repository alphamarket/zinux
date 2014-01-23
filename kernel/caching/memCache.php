<?php
namespace zinux\kernel\caching;
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
     * the keys holder
     * @var array
     */
    protected static $_keys = array();
    /**
     * the memcache object
     * @var \Memcache
     */
    protected $mem_cache_instace = null;

    /**
     * Create a new instance of memCache
     * @param string $name [ optional ] the name of current cache
     * @param array $options the connection options, contains <pre>
     * {
     *      "host" : the memcache host address,
     *      "port" : the memcache server port number
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
            # set host address
            $this->_host = $options["host"];
            # set port address
            $this->_port = $options["port"];
            # set cache name
            $this->_cachename = $name;
            # test connection with configurations
            $this->testConnection(1);
            # load relative key list
            $this->loadkeys();
        }
        else
        {
            \trigger_error("\Memcache not found for furture information check <a href='http://www.php.net/manual/en/book.memcache.php' target='__blank'>this</a>.", \E_USER_ERROR);
        }
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
    protected function connect()
    {
        if (!@$this->mem_cache_instace->connect($this->_host, $this->_port))
        {
            $this->mem_cache_instace = null;
            throw new \Exception("Unable to connect to memCacher server at '{$this->_host}::{$this->_port}'.");
        }
    }
    /**
     * loads relative key lists to current cache name
     */
    protected function loadkeys()
    {
        $this->connect();
        if(!isset(self::$_keys[$this->_cachename]))
        {
            $keys = $this->mem_cache_instace->get("keys-{$this->_cachename}");
            self::$_keys[$this->_cachename] = ($keys === false ? array() : $keys);
        }
        $this->disconnect();
    }
    /**
     * disconnect from memCache
     * @link http://www.php.net/manual/en/memcache.close.php
     */
    protected function disconnect()
    {
        $this->mem_cache_instace->close();
    }
    /**
     * generates keys from the $key
     * @param string $key
     * @return string
     */
    protected function genKey($key)
    {
        return $this->_cachename.$key;
    }
    /**
     * Retrieve cached data by its key
     *
     * @param string $key
     * @return mixed
     * @link http://www.php.net/manual/en/memcache.get.php
     */
    public function fetch($key) {
        $this->connect();
        $data = $this->mem_cache_instace->get($this->genKey($key));
        $this->disconnect();
        return ($data === false) ? null : $data;
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
    public function save($key, $data, $expire = 0) {
        $this->connect();
        $res = $this->mem_cache_instace->set($this->genKey($key), $data, $expire);
        if($this->mem_cache_instace->get($this->genKey($key)) != $data)
            return FALSE;
        self::$_keys[$this->_cachename][$key] = $key;
        $this->mem_cache_instace->set("keys-{$this->_cachename}", self::$_keys[$this->_cachename]);
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
    public function delete($key) {
        $this->connect();
        $res = $this->mem_cache_instace->delete($this->genKey($key));
        unset(self::$_keys[$this->_cachename][$key]);
        $this->mem_cache_instace->set("keys-{$this->_cachename}", self::$_keys[$this->_cachename]);
        $this->disconnect();
        return $res;
    }
    /**
     * Retrieve all cached data
     *
     * @return array
     */
    public function fetchAll()
    {
        $this->connect();
        $data = array();
        foreach(self::$_keys[$this->_cachename] as $key)
        {
            $data[$key] = $this->mem_cache_instace->get($this->genKey($key));
        }
        $this->disconnect();
        return $data;
    }
    /**
     * get all keys registered with this cache
     * @return array
     */
    public function getKeys()
    {
        return isset(self::$_keys[$this->_cachename]) ? self::$_keys[$this->_cachename] : array();
    }
    /**
     * Erase all cached entries
     */
    public function deleteAll()
    {
        $this->connect();
        foreach(self::$_keys[$this->_cachename] as $key)
        {
            $this->mem_cache_instace->delete($this->genKey($key));
        }
        $this->mem_cache_instace->delete("keys-{$this->_cachename}");
        $this->disconnect();
        unset(self::$_keys[$this->_cachename]);
    }
    /**
     * Check whether data accociated with a key
     *
     * @param string $key
     * @return boolean
     */
    public function isCached($key)
    {
        return $this->fetch($key) !== NULL ? true : false;
    }
    /**
     * Cache name Setter
     *
     * @param string $name
     * @return object
     */
    public function setCacheName($name) {
        $this->_cachename = $name;
        return $this;
    }

    /**
     * Cache name Getter
     *
     * @return void
     */
    public function getCacheName(){
        return $this->_cachename;
    }

    /**
     * Get count of items that has been cached
     * @return integer
     */
    public function count()
    {
        return count(self::$_keys[$this->_cachename]);
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
        self::$_keys = array();
        if($wait_on_flush)
            \sleep(1);
    }
}