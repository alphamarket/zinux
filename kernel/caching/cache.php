<?php
namespace iMVC\kernel\caching;
/**
 * Simple Cache class
 * API Documentation: https://github.com/cosenary/Simple-PHP-Cache
 * 
 * @author Christian Metz
 * @since 22.12.2011
 * @copyright Christian Metz - MetzWeb Networks
 * @version 1.3
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */

abstract class cache {
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
     * uses for internal cache upgrading
     * @var type 
     */
    protected static $_soft_cache = NULL;

    /**
     * Default constructor
     *
     * @param string|array [optional] $config
     * @return void
     */
    public abstract function __construct($config = null);
    /**
     * saves cache data 
     */
    protected abstract function _saveData(array $cacheData);
    /**
     * Load appointed cache
     * 
     * @return mixed
     */
    protected abstract function _loadCache();
    /**
     * Erase all cached entries
     * 
     * @return object
     */
    public abstract function eraseAll();

    /**
     * Check whether data accociated with a key
     *
     * @param string $key
     * @return boolean
     */
    public function isCached($key) {
        $cachedData = $this->_loadCache();
        return isset($cachedData[$key]['data']);
    }

    /**
     * Store data in the cache
     *
     * @param string $key
     * @param mixed $data
     * @param integer [optional] $expiration
     * @return object
     */
    public function store($key, $data, $expiration = 0) {
        $storeData = array(
            'time'   => time(),
            'expire' => $expiration,
            'data'   => $data
        );
        $dataArray = $this->_loadCache();
        if (true === is_array($dataArray)) {
            $dataArray[$key] = $storeData;
        } else {
            $dataArray = array($key => $storeData);
        }
        $this->_saveData($dataArray);
        return $this;
    }
    /**
     * Retrieve cached data by its key
     * 
     * @param string $key
     * @param boolean [optional] $timestamp
     * @return string
     */
    public function retrieve($key, $timestamp = false) {
        $this->eraseExpired();
        $cachedData = $this->_loadCache();
        (false === $timestamp) ? $type = 'data' : $type = 'time';
        if(!isset($cachedData[$key][$type])) return NULL;
        return $cachedData[$key][$type];
    }

    /**
     * Retrieve all cached data
     * 
     * @param boolean [optional] $meta
     * @return array
     */
    public function retrieveAll($meta = false) {
        $this->eraseExpired();
        if ($meta === false) {
            $results = array();
            $cachedData = $this->_loadCache();
            if ($cachedData) {
                foreach ($cachedData as $k => $v) {
                  $results[$k] = $v['data'];
                }
            }
            return $results;
        } else {
            return $this->_loadCache();
        }
    }

    /**
     * Erase cached entry by its key
     * 
     * @param string $key
     * @return object
     */
    public function erase($key) {
        $cacheData = $this->_loadCache();
        if (true === is_array($cacheData)) {
            if (true === isset($cacheData[$key])) {
                unset($cacheData[$key]);
                $this->_saveData($cacheData);
            } else {
                throw new \Exception("Error: erase() - Key '{$key}' not found.");
            }
        }
        return $this;
    }

    /**
     * Erase all expired entries
     * 
     * @return integer
     */
    public function eraseExpired() {
        $cacheData = $this->_loadCache();
        if (true === is_array($cacheData)) {
            $counter = 0;
            foreach ($cacheData as $key => $entry) {
            if (true === $this->_checkExpired($entry['time'], $entry['expire'])) {
                unset($cacheData[$key]);
                $counter++;
            }
            }
            if ($counter > 0) {
                $this->_saveData($cacheData);
            }
            return $counter;
        }
    }

    /**
     * Get the filename hash
     * 
     * @return string
     */
    protected function _getHash($filename) {
        return sha1($filename);
    }

    /**
     * Check whether a timestamp is still in the duration 
     * 
     * @param integer $timestamp
     * @param integer $expiration
     * @return boolean
     */
    protected function _checkExpired($timestamp, $expiration) {
        $result = false;
        if ($expiration !== 0) {
            $timeDiff = time() - $timestamp;
            ($timeDiff > $expiration) ? $result = true : $result = false;
        }
        return $result;
    }

    /**
     * Cache path Setter
     * 
     * @param string $path
     * @return object
     */
    public function setCachePath($path) {
        if($path[strlen($path)-1]!=DIRECTORY_SEPARATOR)
            $path = $path.DIRECTORY_SEPARATOR;
        $this->_cachepath = $path;
        return $this;
    }

    /**
     * Cache path Getter
     * 
     * @return string
     */
    public function getCachePath() {
        return $this->_cachepath;
    }

    /**
     * Cache name Setter
     * 
     * @param string $name
     * @return object
     */
    public function setCache($name) {
        $this->_cachename = $name;
        return $this;
    }

    /**
     * Cache name Getter
     * 
     * @return void
     */
    public function getCache() {
        return $this->_cachename;
    }
}