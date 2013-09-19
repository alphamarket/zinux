<?php
namespace zinux;

if(!defined("ZINUX_ROOT") || !defined('PROJECT_ROOT'))
{
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
            foreach(kernel\application\plugin::$plug_lists as $dir)
            {
                # include once the class' file using dynamic path finder!
                $path = kernel\utilities\fileSystem::resolve_path("$dir"."$c.php");
                # if file exists
                if($path)
                {
                    # include it
                    require_once $path;
                    break;
                }
            }
        },1,1);
}
if(!defined("BASEZINUX_CLASS_DECLARED"))
{
    define("BASEZINUX_CLASS_DECLARED", 1);
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
}