<?php
namespace zinux\kernel\caching;
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
     * The name of the default cache file
     *
     * @var string
     */
    protected $_cachename = 'default';
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
    public abstract function deleteAll();
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
    public function save($key, $data, $expiration = 0) {
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
    public function fetch($key, $timestamp = false) {
        $this->deleteExpired();
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
    public function fetchAll($meta = false) {
        $this->deleteExpired();
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
    public function delete($key) {
        $cacheData = $this->_loadCache();
        if (true === is_array($cacheData)) {
            if (true === isset($cacheData[$key])) {
                unset($cacheData[$key]);
                $this->_saveData($cacheData);
            }
        }
        return $this;
    }

    /**
     * Erase all expired entries
     * 
     * @return integer
     */
    public function deleteExpired() {
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
    public abstract function setCachePath($path);

    /**
     * Cache path Getter
     * 
     * @return string
     */
    public abstract function getCacheDirectory();

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
}