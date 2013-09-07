<?php
namespace iMVC\kernel\caching;
require_once 'cache.php';

class fileCache extends cache
{
    /**
     * The path to the cache file folder
     *
     * @var string
     */
    protected static $_cachepath = '';
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
            $this->setCache($name);
        $path = self::$_cachepath;
        if(!isset($path) || !is_string($path) || !strlen($path))
        {
            $path = sys_get_temp_dir()."/iMVC-cache";
            $this->setCachePath($path);
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
    protected function _saveData(array $cacheData)
    {
          self::$_soft_cache[$this->getCache()] = $cacheData;
          $cacheData['hash-sum'] = $this->_getHash(serialize($cacheData));
          $cacheData = serialize($cacheData);
          file_put_contents($this->getCacheDir(), $cacheData);
    }
    /**
     * Erase all cached entries
     * 
     * @return object
     */
    public function eraseAll() {
        $cacheDir = $this->getCacheDir();
        # delete the cache file
        unlink($cacheDir);
        # free the soft cache
        unset(self::$_soft_cache[$this->getCache()]);
        return $this;
    }

    /**
     * Load appointed cache
     * 
     * @return mixed
     */
      protected function _loadCache() {
            # relative caching 
            if(isset(self::$_soft_cache[$this->getCache()]))
                return self::$_soft_cache[$this->getCache()];
            if (true === file_exists($this->getCacheDir())) {
                $file = file_get_contents($this->getCacheDir());
                $u = unserialize($file);
                if(!isset($u['hash-sum']))
                {
                    unlink($this->getCacheDir());
                    trigger_error("cache data miss-hashed, cache file deleted...");
                    return false;
                }
                $h = $u['hash-sum'];
                unset($u['hash-sum']);
                if($h != $this->_getHash(serialize($u)))
                {
                    unlink($this->getCacheDir());
                    trigger_error("cache data miss-hashed, cache file deleted...");
                    return false;
                }  
                # cache the cache!
                self::$_soft_cache[$this->getCache()] = $u;
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
    public function getCacheDir() {
        if (true === $this->_checkCacheDir()) {
            $filename = $this->getCache();
            $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
            return $this->getCachePath() . $this->_getHash($filename) . $this->getExtension();
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
        if (!is_dir($this->getCachePath()) && !mkdir($this->getCachePath(), 0775, true)) {
            throw new \Exception('Unable to create cache directory ' . $this->getCachePath());
        } elseif (!is_readable($this->getCachePath()) || !is_writable($this->getCachePath())) {
            if (!chmod($this->getCachePath(), 0775)) {
                throw new \Exception($this->getCachePath() . ' must be readable and writeable');
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
    
    public static function RegisterCacheDir($path)
    {
        if($path[strlen($path)-1]!=DIRECTORY_SEPARATOR)
            $path = $path.DIRECTORY_SEPARATOR;
        self::$_cachepath=$path;
    }
    
    public function setCachePath($path)
    {
        if($path[strlen($path)-1]!=DIRECTORY_SEPARATOR)
            $path = $path.DIRECTORY_SEPARATOR;
        self::$_cachepath=$path;
    }

    /**
     * Cache path Getter
     * 
     * @return string
     */
    public function getCachePath() {
        return self::$_cachepath;
    }
}