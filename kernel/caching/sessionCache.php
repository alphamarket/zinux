<?php
namespace zinux\kernel\caching;
require_once 'cache.php';

class sessionCache extends cache
{
    /**
     * The path to the cache file folder
     *
     * @var string
     */
    protected static $_cachedirectory = '';
    /**
     * uses for internal cache upgrading
     * @var type 
     */
    protected static $_soft_cache = array();
    /**
     * The name of the default cache file
     *
     * @var string
     */
    protected $_cachename = 'default';
    /**
     * choosen serialize function
     */
    protected $serialize_func = 'serialize';
    /**
     * choosen unserialize function
     */
    protected $unserialize_func = 'unserialize';

    /**
     * Default constructor
     * @param string[optional] $cache_name the cache name
     * @param string[optional] $serialize_func the function used for serializing the data
     * @param string[optional] $unserialize_func the function used for unserializing the serialized data
     * @throws \zinux\kernel\exceptions\notFoundException if any of serialization functions not found
     */
    public function __construct($cache_name = 'default', $serialize_func = "serialize", $unserialize_func = "unserialize") {
        if(isset($cache_name) && strlen($cache_name))
            $this->setCacheName($cache_name);
        $this->setCachePath('cache');
        # one-time check functions [ for speen boostups ]
        static $func_check = array();
        # validate serialize function
        if(!isset($func_check[$serialize_func]))
            if(!\function_exists($serialize_func))
                throw new \zinux\kernel\exceptions\notFoundException("Function `$serialize_func` not found!");
            else
                $func_check[$serialize_func] = 1;
        # validate unserialize function
        if(!isset($func_check[$unserialize_func]))
            if(!\function_exists($unserialize_func))
                throw new \zinux\kernel\exceptions\notFoundException("Function `$unserialize_func` not found!");
            else
                $func_check[$unserialize_func] = 1;
        # introduce the serialize function
        $this->serialize_func = $serialize_func;
        # introduce the unserialize function
        $this->unserialize_func = $unserialize_func;
    }
    /**
     * Finalize the pipe line destruction
     */
    public function __destruct() {
        # if current cache placeholder is empty
        if(!$this->count())
            # unset the current cache placeholder
            $this->deleteAll();
        # if no cache presented at cache directory
        if(!count($_SESSION[self::$_cachedirectory]))
            # unset it
            unset($_SESSION[self::$_cachedirectory]);
    }
    /**
     * save the data into cache
     * @param array $cacheData
     */
    protected function _saveData(array $cacheData){
        $this->_cachepath = self::$_cachedirectory;
        self::$_soft_cache[$this->_cachepath][$this->_cachename] = $cacheData;
        $cacheData = \call_user_func($this->serialize_func, $cacheData);
        if(!session_id() && !headers_sent())
            session_start();
        $_SESSION[$this->_cachepath][$this->_cachename] = $cacheData;
    }
    /**
     * Load appointed cache
     * 
     * @return mixed
     */
    protected function _loadCache() {
        $this->_cachepath = self::$_cachedirectory;
        # relative caching
        if(isset(self::$_soft_cache[$this->_cachepath][$this->_cachename]))
            return self::$_soft_cache[$this->_cachepath][$this->_cachename];
        if(!isset($_SESSION[$this->_cachepath][$this->_cachename]))
            return  false;
        return (self::$_soft_cache[$this->_cachepath][$this->_cachename] = \call_user_func($this->unserialize_func, $_SESSION[$this->_cachepath][$this->_cachename]));
    }
    /**
     * Delete all data in current cache 
     */
    public function deleteAll() {
        unset(self::$_soft_cache[$this->_cachepath][$this->_cachename]);
        unset($_SESSION[$this->_cachepath][$this->_cachename]);
    }
    /**
     * Cache path Setter
     * 
     * @param string $path
     * @return object
     */
    public function setCachePath($path) {
        self::$_cachedirectory = $path;
        $this->save("", "");
        $this->delete("");        
    }
    /**
     * Cache path Getter
     * 
     * @return string
     */
    public function getCacheDirectory() {
        return self::$_cachedirectory;
    }
}