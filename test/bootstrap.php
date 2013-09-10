<?php
    
    define("RUNNING_ENV", "TEST");
    
    define("ZINUX_ROOT", realpath(dirname(__FILE__)."/../")."/");
    
    $_SERVER['REQUEST_URI'] = "/";
    
    require_once ZINUX_ROOT."baseZinux.php";
    
    \zinux\kernel\caching\fileCache::RegisterCachePath(ZINUX_ROOT."../cache/test");
    # overwriting PHPUnit autoloader 
    # by pushing it autloader's stack
    # this should be on top of other 
    # autoloader to detect phpunit's 
    # class' soon and preventing php
    # to lookup to other autloaders
    spl_autoload_register(
        function ($class) {
            # fetch relative path using namespace map
            $c = str_replace("_", DIRECTORY_SEPARATOR, $class);
            if(!file_exists("/usr/share/php/$c.php")) return;
            include_once "/usr/share/php/$c.php";
        },1,1);