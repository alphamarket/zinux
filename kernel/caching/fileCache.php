<?php
namespace zinux\kernel\caching;
require_once 'cache.php';

class fileCache extends cache
{
    /**
     * The path to the cache file folder
     *
     * @var string
     */
    protected static $_cachedirectory = '';
    /**
     * The cache file extension
     *
     * @var string
     */
    protected $_extension = '.cache';
    /**
     * uses for internal cache upgrading
     * @var type 
     */
    protected static $_soft_cache = array();

    /**
     * Default constructor
     *
     * @param string|array [optional] $config
     * @return void
     */
    public function __construct($name = "default"){
        if(isset($name) &&strlen($name))
            $this->setCacheName($name);
        $path = self::$_cachedirectory;
        if(!isset($path) || !is_string($path) || !strlen($path))
        {
            $this->setCachePath(self::getDefaultCacheDirectory());
        }
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
    protected function _saveData(array $cacheData)
    {
          self::$_soft_cache[$this->getCacheName()] = $cacheData;
          $cacheData['hash-sum'] = $this->_getHash(serialize($cacheData));
          $cacheData = serialize($cacheData);
          file_put_contents($this->getCacheFile(), $cacheData);
    }
    /**
     * Erase all cached entries
     * 
     * @return object
     */
    public function deleteAll() {
        $cache = $this->getCacheFile();
        if(file_exists($cache))
            # delete the cache file
            unlink($cache);
        # free the soft cache
        unset(self::$_soft_cache[$this->getCacheName()]);
        return $this;
    }

    /**
     * Load appointed cache
     * 
     * @return mixed
     */
      protected function _loadCache() {
            # relative caching 
            if(isset(self::$_soft_cache[$this->getCacheName()]))
                return self::$_soft_cache[$this->getCacheName()];
            if (true === file_exists($this->getCacheFile())) {
                $file = file_get_contents($this->getCacheFile());
                $u = unserialize($file);
                if(!isset($u['hash-sum']))
                {
                    unlink($this->getCacheFile());
                    trigger_error("cache data miss-hashed, cache file deleted...");
                    return false;
                }
                $h = $u['hash-sum'];
                unset($u['hash-sum']);
                if($h != $this->_getHash(serialize($u)))
                {
                    unlink($this->getCacheFile());
                    trigger_error("cache data miss-hashed, cache file deleted...");
                    return false;
                }  
                # cache the cache!
                self::$_soft_cache[$this->getCacheName()] = $u;
                return $u;
              }
              else {
                return false;
            }
    }

    /**
     * Get the cache directory path
     * 
     * @return string
     */
    public function getCacheFile() {
        if (true === $this->_checkCacheDir()) {
            $filename = $this->getCacheName();
            $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
            return $this->getCacheDirectory() . $this->_getHash($filename) . $this->getExtension();
        }
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
     * Check if a writable cache directory exists and if not create a new one
     * 
     * @return boolean
     */
    protected function _checkCacheDir() {
        if (!@(is_dir($this->getCacheDirectory()) || mkdir($this->getCacheDirectory(), 0775, true))) {
            throw new \Exception('Unable to create cache directory ' . $this->getCacheDirectory());
        } elseif (!is_readable($this->getCacheDirectory()) || !is_writable($this->getCacheDirectory())) {
            if (!@chmod($this->getCacheDirectory(), 0775)) {
                throw new \Exception($this->getCacheDirectory() . ' must be readable and writeable');
            }
        }
        return true;
    }
    /**
     * Cache file extension Setter
     * 
     * @param string $ext
     * @return object
     */
    public function setExtension($ext) {
        if($ext[0] != ".")
            $ext = ".".$ext;
        $this->_extension = $ext;
        return $this;
    }

    /**
     * Cache file extension Getter
     * 
     * @return string
     */
    public function getExtension() {
        return $this->_extension;
    }
    /**
     * Register the global cache directory path
     * @param string $path the cache directory path
     * @param boolean $validate should validate the path existance?
     * @throws \zinux\kernel\exceptions\notFoundException if cache directory not found
     */
    public static function RegisterCachePath($path, $validate = 1)
    {
        if(!strlen($path))
            throw new \zinux\kernel\exceptions\notFoundException("Cache directory address is empty ...");
        if($path[strlen($path)-1]!=DIRECTORY_SEPARATOR)
            $path = $path.DIRECTORY_SEPARATOR;
        self::$_cachedirectory=$path;
        if($validate)
        {
            $c = new self;
            $c->_checkCacheDir();
        }
    }
    /**
     * Set cache direectory path
     * @param string $path The cache directory path
     * @param boolean $validate should validate the path existance?
     */
    public function setCachePath($path, $validate = 1)
    {
        self::RegisterCachePath($path, $validate);
    }

    /**
     * Cache path Getter
     * 
     * @return string
     */
    public function getCacheDirectory() {
        return self::$_cachedirectory;
    }
    /**
     * Get cache name
     * @param string $name
     */
    public function setCacheName($name)
    {
        parent::setCacheName($name);
    }
    /**
     * Get internal cached caches
     * @return array
     */
    public static function getInternalCaches() { return self::$_soft_cache; }
    /**
     * Get default cache directory
     * @return string 
     */
    public static function getDefaultCacheDirectory()
    {
        return sys_get_temp_dir()."/zinux-cache";
    }
}