<?php
namespace zinux;

if(!defined("ZINUX_ROOT") || !defined('PROJECT_ROOT'))
{
    defined("ZINUX_BUILD_VERSION") || define("ZINUX_BUILD_VERSION", "3.2.7");

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
    # register the general autoloader
    spl_autoload_register(
        function ($class) {
            # fetch relative path using namespace map
            $c = str_replace("\\", DIRECTORY_SEPARATOR, $class);
            require_once 'kernel/utilities/fileSystem.php';
            require_once "kernel/caching/fileCache.php";
            # open up a file cache socket
            $fc = new \zinux\kernel\caching\fileCache(PROJECT_ROOT."spl_autoload_register");
            # fetch current cache directory
            $current_cache_path = $fc->getCacheDirectory();
            # switch cache directory to zinux's default cache directory
            $fc->setCachePath($fc->getDefaultCacheDirectory());
            # check if the class has been cached
            if($fc->isCached($class))
            {
                # if so just require it
                require_once $fc->fetch($class);
            }
            else foreach(kernel\application\plugin::$plug_lists as $dir)
            {
                # include once the class' file using dynamic path finder!
                $path = kernel\utilities\fileSystem::resolve_path("$dir"."$c.php");
                # if file exists
                if($path)
                {
                    # add the class with the its required path to cache
                    $fc->save($class, $path);
                    # include it
                    require_once $path;
                    break;
                }
            }
            # switch back to previous cache direcotry
            $fc->setCachePath($current_cache_path);
        },1,1);
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
