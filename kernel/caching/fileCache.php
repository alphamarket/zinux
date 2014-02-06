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
    public function __construct($cache_name = "default", $serialize_func = "serialize", $unserialize_func = "unserialize"){
        if(isset($cache_name) && strlen($cache_name))
            $this->setCacheName($cache_name);
        $path = self::$_cachedirectory;
        if(!isset($path) || !is_string($path) || !strlen($path))
        {
            $this->setCachePath(self::getDefaultCacheDirectory());
        }
        # one-time check functions
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
    protected function _saveData(array $cacheData)
    {
          self::$_soft_cache[$this->getCacheName()] = $cacheData;
          $cacheData = \call_user_func($this->serialize_func, $cacheData);
          static $error_tracks = array();
          # check if file exists?
          if(!\file_exists($this->getCacheFile()))
          {
              # if not, create new one
              \touch($this->getCacheFile());
              # set permissions to global rw access
              \chmod($this->getCacheFile(), 0666);
          }
          # fail safe for file streaming
          if(!@file_put_contents($this->getCacheFile(), $cacheData) && !@$error_tracks[$this->getCacheFile()])
          {
                \trigger_error("<b>Permission denied for writing into</b> {$this->getCacheFile()} <b>[ any following errors on this file suppressed ]</b>", E_USER_WARNING);
                $error_tracks[$this->getCacheFile()] = 1;
          }
    }
    /**
     * Erase all cached entries
     * 
     * @return object
     */
    public function deleteAll() {
        $cache = $this->getCacheFile();
        if(file_exists($cache))
        {
            # delete the cache file
            \exec("rm -f '$cache'");
        }
        # free the soft cache
        unset(self::$_soft_cache[$this->getCacheName()]);
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
            # cache the cache!
            self::$_soft_cache[$this->getCacheName()] = \call_user_func($this->unserialize_func, $file);
            return self::$_soft_cache[$this->getCacheName()];
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
            $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', $this->getCacheName());
            return $this->getCacheDirectory() . $this->_getHash($filename) . $this->getExtension();
        }
    }

    /**
     * Check if a writable cache directory exists and if not create a new one
     * 
     * @return boolean
     */
    protected function _checkCacheDir() {
        static $checked = array();
        if(isset($checked[$this->getCacheDirectory()])) return true;
        $old = \umask(0);
        if (!@(is_dir($this->getCacheDirectory()) || mkdir($this->getCacheDirectory(), 0777, true))) {
            \umask($old);
            throw new \Exception('Unable to create cache directory ' . $this->getCacheDirectory());
        } elseif (!is_readable($this->getCacheDirectory()) || !is_writable($this->getCacheDirectory())) {
            if (!@chmod($this->getCacheDirectory(), 0777)) {
                \umask($old);
                throw new \Exception($this->getCacheDirectory() . ' must be readable and writeable');
            }
        }
        \umask($old);
        $checked[$this->getCacheDirectory()] = true;
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