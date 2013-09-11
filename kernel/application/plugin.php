<?php
    namespace zinux\kernel\application;

    require_once (dirname(__FILE__).'/../../baseZinux.php');
    require_once ZINUX_ROOT."/kernel/exceptions/notFoundException.php";
    require_once ZINUX_ROOT."/kernel/utilities/fileSystem.php";

    /**
     * @author dariush
     * @version 1.0
     * @created 04-Sep-2013 15:50:20
     */
    class plugin extends \zinux\baseZinux
    {        
        
        public static $raw_list = array();
        
        public static $plug_lists = array();
        
        public function __construct()
        {
            $this->Initiate();
        }


        public function Initiate(){}
        /**
         * Add a plugin address
         * @param type $plugin_addres
         * @throws \zinux\kernel\exceptions\notFoundException if plugin's directory not found
         */
        public function registerPlugin($name, $plugin_addres = "")
        {
            # first posible place
            if(!($rpath = \zinux\kernel\utilities\fileSystem::resolve_path(PROJECT_ROOT.$plugin_addres)))
            {
                throw new \zinux\kernel\exceptions\notFoundException("'$plugin_addres' not found!");
            }  
            
            if(is_file($rpath))
                $rpath = dirname($rpath);
            
            if($rpath[strlen($rpath)-1]!=DIRECTORY_SEPARATOR)
                $rpath .= DIRECTORY_SEPARATOR;
            
            self::$raw_list[$rpath] = $rpath;
            
            self::$plug_lists[$name] = $rpath;
            
            return $this;
        }
        /**
         * get a plugin's address
         * @param string $name plugin's name
         * @return boolean
         * @throws \zinux\kernel\exceptions\notFoundException if plugin has not registered
         */
        public static function getPluginAddress($name)
        {
            if(isset(self::$plug_lists[$name]))
                return self::$plug_lists[$name];
            throw new \zinux\kernel\exceptions\notFoundException("Plugin $name not found...");
        }
        /**
         * check if a plug in with a name registered?
         * @param string $name plugin's name
         * @return boolean
         */
        public function isPluginRegistered($name_or_path)
        {
            if(isset(self::$plug_lists[$name_or_path]))
                return self::$plug_lists[$name_or_path];
            $name_or_path = \zinux\kernel\utilities\fileSystem::resolve_path($name_or_path);
            if(isset(self::$raw_list[$name_or_path]))
                return $name_or_path;
            return FALSE;
        }
        public function Dispose()
        {
            // i override this to prevent from $plug_lists and $raw_lists to be disposed by :
            # parent::Dispose()
        }
    }