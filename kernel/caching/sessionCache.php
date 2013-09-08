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
        if(isset($cache_name) &&strlen($cache_name))
            $this->setCacheName($cache_name);
        $this->setCachePath('cache');
    }

    protected function _saveData(array $cacheData){
        $cacheData['hash-sum'] = $this->_getHash(serialize($cacheData));
        $cacheData = serialize($cacheData);
        $_SESSION[$this->_cachepath][$this->_cachename] = $cacheData;
    }

    /**
     * Load appointed cache
     * 
     * @return mixed
     */
    protected function _loadCache() {
        $this->_cachepath = self::$_cachedirectory;
        if(!isset($_SESSION[$this->_cachepath][$this->_cachename]))
            return  false;
        $u = unserialize($_SESSION[$this->_cachepath][$this->_cachename]);
        if(!isset($u['hash-sum']))
        {
            unset($_SESSION[$this->_cachepath][$this->_cachename]);
            trigger_error("cache data miss-hashed, cache file deleted...");
            return false;
        }
        $h = $u['hash-sum'];
        unset($u['hash-sum']);
        if($h != $this->_getHash(serialize($u)))
        {
            unset($_SESSION[$this->_cachepath][$this->_cachename]);
            trigger_error("cache data miss-hashed, cache file deleted...");
            return false;
        }  
        return $u;
    }

    public function eraseAll()
    {
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
        $this->erase("");
        return $this;
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