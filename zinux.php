<?php
namespace zinux;

if(!defined("ZINUX_ROOT") || !defined('PROJECT_ROOT'))
{
    defined("ZINUX_BUILD_VERSION") || define("ZINUX_BUILD_VERSION", "5.3.0");

    defined("ZINUX_BUILD_PHP_VERSION") || define("ZINUX_BUILD_PHP_VERSION", "5.5.8");

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
    $plugin = new \zinux\kernel\application\plugin();
    # treat current project as a plugin
    $plugin->registerPlugin("PROJECT_ROOT");
    # dispose the instance
    unset($plugin);
    # caching suppression flag
    $suppress_caching = 0;
    # defines caching method in zinux autoloader
    # set to FALSE to use fileSystem method
    # set to TRUE to use memCache method
    $use_memcache = 0;
    # options container for memcache
    $mem_cache_options = array();
    # register the general autoloader
    spl_autoload_register(
        function ($class) {
            global $suppress_caching, $use_memcache, $mem_cache_options;
            # fetch relative path using namespace map
            $c = str_replace("\\", DIRECTORY_SEPARATOR, $class);
            if(!$suppress_caching)
            {
                # set a cache sig.
                static $cache_sig = NULL;
                if(!$cache_sig)
                    $cache_sig = PROJECT_ROOT."spl_autoload_register";
                require_once "kernel/caching/memCache.php";
                # flag if memcache is supported in system
                $mem_cache_supported = $use_memcache && \zinux\kernel\caching\memCache::Is_memCache_Supported();
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
                    $cache = new \zinux\kernel\caching\memCache($cache_sig, $mem_cache_options);
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
                require_once 'kernel/utilities/fileSystem.php';
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
            global $use_memcache;
            # set a cache sig.
            $cache_sig = PROJECT_ROOT."spl_autoload_register";
            # flag if memcache is supported in system
            $mem_cache_supported = $use_memcache && \zinux\kernel\caching\memCache::Is_memCache_Supported();
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
        /**
         * suppress autoloader caching functionality
         */
        function suppress_zinux_autoloader_caching($should_suppress = 1)
        {
            global $suppress_caching;
            $suppress_caching = $should_suppress ? TRUE : FALSE;
        }
        /**
         * set autloader caching method to memcache
         * @param boolean $set_to_memcache set TRUE to let memcache handle things, otherwise filesystem will handler the caching
         */
        function set_zinux_autoloader_caching_handler($set_to_memcache = 0)
        {
            global $use_memcache;
            $use_memcache = $set_to_memcache ? TRUE : FALSE;
        }
        /**
         * get memcache options for zinux autloader
         */
        function set_zinux_autoloader_memCache_options(array $options)
        {
            global $mem_cache_options;
            $mem_cache_options = $options;
        }
}
