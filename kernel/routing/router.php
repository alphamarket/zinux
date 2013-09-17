<?php
namespace zinux\kernel\routing;

require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class router extends \zinux\baseZinux
{
    protected  static $_boostraps = array();
    
    public function __construct()
    {
        $this->Initiate();
    }
    
    public function Initiate(){}
    
    public function Process(request &$request)
    {
        $this->RunBootstrap($request);
        $request->Process();
        $this->request = $request;
    }
    /**
     * Route the passed request
     * 
     * @param request
     */
    public function Run()
    {        
        $this->request->Process();
        // pre-dispatcher
        $predisp = new preDispatcher();
        $predisp->Process($this->request);
        // dispatcher
        $disp = new dispatcher();
        $disp->Process($this->request);
        // post-dispatcher
        $postdisp = new postDispatcher();
        $postdisp->Process($this->request);  
    }
    
    protected function RunBootstrap(request &$request)
    {
        foreach(self::$_boostraps as $routerBootstrap)
        {
            $routerBootstrap->Fetch();
            if($routerBootstrap->process($request))
                break;
        }
    }
    
    public static function RegisterBootstrap(routerBootstrap $routerBootstrap, $name = "")
    {
        if(strlen($name))
            self::$_boostraps[$name] = $routerBootstrap;
        else
            self::$_boostraps[] = $routerBootstrap;
    }
    
    public static function UnregisterBootstrap($name)
    {
        unset(self::$_boostraps[$name]);
    }
}
?>