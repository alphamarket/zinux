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
        return isset($cachedData[$key]);
    }

    /**
     * Store data in the cache
     *
     * @param string $key
     * @param mixed $data
     * @param timespan [ optional ] $expiration the expiration will sum with NOW datetime
     * @return object
     */
    public function save($key, $data, $expiration = 0) {
        $storeData = array(
            'data'   => $data
        );
        if($expiration)
        {
            $storeData['time'] = time();
            $storeData['expire'] = $expiration;
        }
        $dataArray = $this->_loadCache();
        if (true === is_array($dataArray)) {
            $dataArray[$key] = $storeData;
        } else {
            $dataArray = array($key => $storeData);
        }
        $this->_saveData($dataArray);
    }
    /**
     * Retrieve cached data by its key
     * 
     * @param string $key
     * @param boolean [optional] $timestamp
     * @return string
     */
    public function fetch($key, $meta = false) {
        if($this->deleteExpired($key))
            return NULL;
        $cachedData = $this->_loadCache();
        if(!isset($cachedData[$key])) return NULL;
        if($meta) return $cachedData[$key];
        return $cachedData[$key]["data"];
    }

    /**
     * Retrieve all cached data
     * 
     * @param boolean [optional] $meta
     * @return array
     */
    public function fetchAll($meta = false) {
        if (!$meta) {
            $results = array();
            $cachedData = $this->_loadCache();
            if ($cachedData) {
                foreach ($cachedData as $k => $v) {
                    if($this->deleteExpired($k))
                        continue;
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
    }

    /**
     * Erase all expired entries
     * @param string $key if a key passed, only delete the key if it is expired; otherwise checks expired keys in all cached data
     * @return integer
     */
    public function deleteExpired($key = NULL) {
        $cacheData = $this->_loadCache();
        if (true === is_array($cacheData)) {
            $counter = 0;
            if($key)
                if(!isset($cacheData[$key]))
                    return true;
                elseif (isset($cacheData[$key]['expire']) && true === $this->_checkExpired($cacheData[$key]['time'], $cacheData[$key]['expire'])) {
                    unset($cacheData[$key]);
                    $counter++;
                }
            else foreach ($cacheData as $key => $entry) {
                if (isset($cacheData[$key]['expire']) && true === $this->_checkExpired($entry['time'], $entry['expire'])) {
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
    public function isExpired($key, $is_meta = 0)
    {
        if($is_meta)
            $meta = $key;
        else
        {
            $cacheData = $this->_loadCache();
            if(!isset($cacheData[$key])) return TRUE;
            $meta = $cacheData[$key];
        }
        if(!isset($cacheData[$key]['expire']))
            return false;
        return $this->_checkExpired($meta['time'], $meta['expire']);
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
        if ($expiration !== 0) {
            $timeDiff = time() - $timestamp;
            return ($timeDiff > $expiration) ? true : false;
        }
        return false;
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
     * @param timespan $expiration the expiration will sum with NOW datetime
     */
    public function setExpireTime($key, $expiration)
    {
        if(!($f = $this->fetch($key)) && $expiration > 0)
        {
            throw new \ErrorException("`$key` does not exists");
        }
        $this->save($key, $f, $expiration);
    }
    /**
     * Get count of items that has been cached
     * @return integer
     */
    public function count()
    {
        if(!($cachedData = $this->_loadCache())) return 0;
        return count($cachedData);
    }
}