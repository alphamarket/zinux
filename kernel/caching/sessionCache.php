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
     * Default constructor
     *
     * @param string [optional] $config
     * @return void
     */
    public function __construct($cache_name = 'default') {
        if(isset($cache_name) && strlen($cache_name))
            $this->setCacheName($cache_name);
        $this->setCachePath('cache');
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
        $cacheData = serialize($cacheData);
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
        return (self::$_soft_cache[$this->_cachepath][$this->_cachename] = unserialize($_SESSION[$this->_cachepath][$this->_cachename]));
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