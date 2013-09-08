<?php
namespace zinux;

if(!defined("zinux_ROOT") || !defined('PROJECT_ROOT'))
{
    # define zinux ROOT
    defined('zinux_ROOT') || define("zinux_ROOT", dirname(__FILE__)."/");
    # dfine project ROOT
    defined("PROJECT_ROOT") || define("PROJECT_ROOT", dirname(zinux_ROOT).DIRECTORY_SEPARATOR);
    # set include path to project root
    # every class' namespace should be a map from project root
    ini_set('include_path', implode(PATH_SEPARATOR, array(ini_get('include_path'),  PROJECT_ROOT)));
    spl_autoload_register(
        function ($class) {
            # fetch relative path using namespace map
            $c = str_replace("\\", DIRECTORY_SEPARATOR, $class);
            require_once 'kernel/utilities/fileSystem.php';
            # include once the class' file using dynamic path finder!
            $path = kernel\utilities\fileSystem::resolve_path(PROJECT_ROOT.$c.".php");
            if($path)
                include_once $path;
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
    abstract public function Initiate();
}
?>
