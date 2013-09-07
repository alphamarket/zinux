<?php
namespace iMVC\kernel\caching;
require_once 'cache.php';
/**
 * Uses layered caching system
 */
class xCache extends cache
{
    /**
     *
     * @var sessionCache
     */
    protected $_session_cache;
    /**
     *
     * @var fileCache
     */
    protected $_file_cache;
    /**
     * hold number of the times that session cache get hit
     * @var int
     */
    protected static $session_hit_count;
    /**
     * hold number of the times that file cache get hit
     * @var int
     */
    protected static $file_hit_count;
    /**
     * hold number of missed cache
     * @var int
     */
    protected static $missed_count;
    /**
     * Default constructor
     *
     * @param string [optional] $config
     * @return void
     */
    public function __construct($cache_name = 'default') {
        require_once 'fileCache.php';
        require_once 'sessionCache.php';
        $this->_session_cache = new sessionCache($cache_name);
        $this->_file_cache = new fileCache($cache_name);
    }
    /**
     * Load appointed cache
     * 
     * @return mixed
     */
    protected function _loadCache() { throw new \iMVC\exceptions\notImplementedException; }

    protected function _saveData(array $cacheData){ throw new \iMVC\exceptions\notImplementedException; }
    
    public function store($key, $data, $expiration = 0)
    {
        $this->_session_cache->store($key, $data, $expiration);
        $this->_file_cache->store($key, $data, $expiration);
    }
    public function retrieve($key, $timestamp = false)
    {
        if($this->_session_cache->isCached($key))
        {
            self::$session_hit_count++;
            if(!$this->_file_cache->isCached($key))
                # sync file with session
                $this->_file_cache->store($key, $this->_session_cache->retrieve($key));
        }
        elseif($this->_file_cache->isCached($key))
        {
            self::$file_hit_count++;
            # sync session with file
            $this->_session_cache->store($key, $this->_file_cache->retrieve($key));
        }
        else
        {
            self::$missed_count++;
            # no key found
            return NULL;
        }
        # we made sure that the $_SESSION & FILE is synced
        return $this->_session_cache->retrieve($key, $timestamp);
    }
    
    public function erase($key)
    {
        $this->_session_cache->erase($key);
        $this->_file_cache->erase($key);
    }

    public function eraseAll()
    {
        $this->_session_cache->eraseAll();
        $this->_file_cache->eraseAll();
    }
    
    public function eraseExpired()
    {
        $this->_session_cache->eraseExpired();
        $this->_file_cache->eraseExpired();
    }
    
    public function isCached($key)
    {
        return $this->_session_cache->isCached($key) ||
            $this->_file_cache->isCached($key);
    }
    public function retrieveAll($meta = false)
    {
        return array_merge($this->_session_cache->retrieveAll($meta), $this->_file_cache->retrieveAll($meta));
    }
    public function getCache()
    {
        return $this->_session_cache->getCache();
    }
    public function getCachePath()
    {
        return $this->_file_cache->getCachePath();
    }
    public function setCache($name)
    {
        $this->_session_cache->setCache($name);
        $this->_file_cache->setCache($name);
    }
    public function setCachePath($path)
    {
        $this->_file_cache->setCachePath($path);
    }
    public static function GetStatisticReportString()
    {
        $tot = self::$file_hit_count+self::$session_hit_count+self::$missed_count;
        if(!self::$missed_count) self::$missed_count = 0;
        if(!self::$file_hit_count) self::$file_hit_count = 0;
        if(!self::$session_hit_count) self::$session_hit_count = 0;
        foreach(array(
                        'Session HIT Count' => self::$session_hit_count." ( ".((self::$session_hit_count/$tot)*100)."% )", 
                        'File HIT Count' => self::$file_hit_count." ( ".((self::$file_hit_count/$tot)*100)."% )", 
                        'Missed Count' => self::$missed_count." ( ".((self::$missed_count/$tot)*100)."% )", 
                ) as $tag => $value)
        {
            echo "$tag = $value<br />";
        }
    }
}