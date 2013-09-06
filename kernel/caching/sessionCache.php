<?php
namespace iMVC\kernel\caching;
require_once 'cache.php';

class sessionCache extends cache
{
    /**
     * The path to the cache file folder
     *
     * @var string
     */
    protected $_cachepath = 'cache';

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
            $this->setCache($cache_name);
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
        if(!isset($_SESSION[$this->_cachepath][$this->_cachename]))
            return  false;
        $u = unserialize($_SESSION[$this->_cachepath][$this->_cachename]);
        if(!isset($u['hash-sum']))
        {
            unset($_SESSION[$this->_cachepath][$this->_cachename]);
            die("cache data miss-hashed, cache file deleted...");
        }
        $h = $u['hash-sum'];
        unset($u['hash-sum']);
        if($h != $this->_getHash(serialize($u)))
        {
            unset($_SESSION[$this->_cachepath][$this->_cachename]);
            die("cache data miss-hashed, cache file deleted...");
        }  
        return $u;
    }

    public function eraseAll()
    {
        self::$_soft_cache = NULL;
        unset($_SESSION[$this->_cachepath][$this->_cachename]);
    }
}