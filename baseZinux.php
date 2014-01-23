<?php
namespace zinux;

if(!defined("ZINUX_ROOT") || !defined('PROJECT_ROOT'))
{
    defined("ZINUX_BUILD_VERSION") || define("ZINUX_BUILD_VERSION", "3.4.3");

    defined("ZINUX_BUILD_PHP_VERSION") || define("ZINUX_BUILD_PHP_VERSION", "5.3.10");

    if(version_compare(PHP_VERSION, ZINUX_BUILD_PHP_VERSION, "<"))
    {
        echo ("The minimal PHP version required is ".ZINUX_BUILD_PHP_VERSION."!<br />");
        die("Your PHP version is: ".PHP_VERSION);
    }
    # define zinux ROOT
    defined('ZINUX_ROOT') || define("ZINUX_ROOT", dirname(__FILE__).DIRECTORY_SEPARATOR);
    # dfine project ROOT
    defined("PROJECT_ROOT") || define("PROJECT_ROOT", dirname(ZINUX_ROOT).DIRECTORY_SEPARATOR);
    # set include path to project root
    # every class' namespace should be a map from project root
    ini_set('include_path', implode(PATH_SEPARATOR, array(ini_get('include_path'),  PROJECT_ROOT)));
    # require plugin file
    require_once 'kernel/application/plugin.php';
    # initiate a new plugin
    $plugin = new kernel\application\plugin();
    # treat current project as a plugin
    $plugin->registerPlugin("PROJECT_ROOT");
    # dispose the instance
    unset($plugin);
    /**
     * caching suppression flag
     */
    $suppress_caching = 0;
    # register the general autoloader
    spl_autoload_register(
        function ($class) {
            global $suppress_caching;
            # fetch relative path using namespace map
            $c = str_replace("\\", DIRECTORY_SEPARATOR, $class);
            require_once 'kernel/utilities/fileSystem.php';
            if(!$suppress_caching)
            {
                require_once "kernel/caching/memCache.php";
                # set a cache sig.
                $cache_sig = PROJECT_ROOT."spl_autoload_register";
                # flag if memcache is supported in system
                $mem_cache_supported = \zinux\kernel\caching\memCache::Is_memCache_Supported();
                # if memcache not supported
                if(!$mem_cache_supported)
                {
                    # load filecache 
                    require_once "kernel/caching/fileCache.php";
                    # open up a memcache cache socket
                    $cache = new \zinux\kernel\caching\fileCache($cache_sig);
                    # fetch current cache directory
                    $current_cache_path = $cache->getCacheDirectory();
                    # switch cache directory to zinux's default cache directory
                    $cache->setCachePath($cache->getDefaultCacheDirectory());
                }
                else
                {
                    # open up a memcache cache socket
                    $cache = new \zinux\kernel\caching\memCache($cache_sig);
                }
                # check if the class has been cached
                if($cache->isCached($class))
                {
                    # if so just require it
                    if(@include_once $cache->fetch($class))
                        # if cached data is valid
                        return;
                    # if cache data is not valid, delete the invalid data
                    $cache->delete($class);
                }
            }
            # look into plugins
            foreach(kernel\application\plugin::$plug_lists as $dir)
            {
                # include once the class' file using dynamic path finder!
                $path = kernel\utilities\fileSystem::resolve_path("$dir"."$c.php");
                # if file exists
                if($path)
                {
                    if(!$suppress_caching)
                    {
                        # add the class with the its required path to cache
                        $cache->save($class, $path);
                    }
                    # include it
                    require_once $path;
                    break;
                }
            }
            if(!$suppress_caching && !$mem_cache_supported)
            {
                # switch back to previous cache direcotry
                $cache->setCachePath($current_cache_path);
            }
        },1,1);
        /**
         * deletes zinux autoloader cache data
         */
        function delete_zinux_autoloader_caches()
        {
            # set a cache sig.
            $cache_sig = PROJECT_ROOT."spl_autoload_register";
            # flag if memcache is supported in system
            $mem_cache_supported = \zinux\kernel\caching\memCache::Is_memCache_Supported();
            # if memcache not supported
            if(!$mem_cache_supported)
            {
                # load filecache 
                require_once "kernel/caching/fileCache.php";
                # open up a memcache cache socket
                $cache = new \zinux\kernel\caching\fileCache($cache_sig);
                # fetch current cache directory
                $current_cache_path = $cache->getCacheDirectory();
                # switch cache directory to zinux's default cache directory
                $cache->setCachePath($cache->getDefaultCacheDirectory());
            }
            else
            {
                # open up a memcache cache socket
                $cache = new \zinux\kernel\caching\memCache($cache_sig);
            }
            $cache->deleteAll();
            if(!$mem_cache_supported)
            {
                # switch back to previous cache direcotry
                $cache->setCachePath($current_cache_path);
            }
        }
        function suppress_zinux_autoloader_caching($should_suppress = 1)
        {
            global $suppress_caching;
            $suppress_caching = $should_suppress ? TRUE : FALSE;
        }
}
/**
 * This is a base class for all zinux classes
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:21
 */
abstract class baseZinux extends \stdClass
{
    /**
     * This will dispose any temp attributes which added by __set()
     */
    public function Dispose()
    {
        foreach($this as $key => $value)
        {
            unset($this->$key);
            unset($value);
        }
    }

    /**
     * The initiation works on loading class
     */
    public function Initiate(){}
    }
