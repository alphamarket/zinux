<?php
namespace zinux\kernel\routing;

require_once ('request.php');
require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class router extends \zinux\baseZinux
{
    protected  static $_boostraps;
    
    public function __construct()
    {
        $this->Initiate();
    }
    
    public function Initiate()
    {
        self::$_boostraps = array();
    }
    /**
     * Route the passed request
     * 
     * @param request
     */
    public function Run(request &$request)
    {
        $this->RunBootstrap($request);
        
        $request->Process();
        // pre-dispatcher
        $predisp = new preDispatcher();
        $predisp->Process($request);
        // dispatcher
        $disp = new dispatcher();
        $disp->Process($request);
        // post-dispatcher
        $postdisp = new postDispatcher();
        $postdisp->Process($request);  
    }
    protected function RunBootstrap(request &$request)
    {
        foreach(self::$_boostraps as $callback)
        {
            call_user_func_array($callback, array($request));
        }
    }
    public static function RegisterBootstrap($callback, $name = "default")
    {
        self::$_boostraps[$name] = $callback;
    }
    
    public static function UnregisterBootstrap($name = "default")
    {
        unset(self::$_boostraps[$name]);
    }
}
?>