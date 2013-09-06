<?php
namespace iMVC\kernel\caching;
require_once 'cache.php';

class sessionCache extends cache
{
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
}