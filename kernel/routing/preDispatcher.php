<?php
namespace zinux\kernel\routing;

require_once 'request.php';
require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:23
 */
class preDispatcher extends \zinux\baseZinux
{
    public function Initiate()
    {
        ;
    }

    /**
     * Initiate the pre-dispacther processes
     * 
     * @param request
     */
    public function Process(request $request)
    {
        $request->Process();
        $this->RunBootstarp($request);
    }
    /**
     * Runs every function in {MODULE}\BootStrap.php function which end with 'Init' or starts with 'pre_' in function's name
     * @param Request $request 
     */
    protected function RunBootstarp(request &$request)
    {
        # both possible naming for bootstrap
        $bsfiles_path = array();
        $bsfiles_path[] = array($request->module->full_name, $request->module->GetPath()."/{$request->module->full_name}Bootstrap.php");
        $bsfiles_path[] = array($request->module->name, $request->module->GetPath()."/{$request->module->name}Bootstrap.php");
        foreach($bsfiles_path as $bs)
        {
            $bs[1] = \zinux\kernel\utilities\fileSystem::resolve_path($bs[1]);
            if(file_exists($bs[1]))
            {
                require_once $bs[1];
                $cname = "{$request->module->GetNameSpace()}\\{$bs[0]}Bootstrap";
                if(class_exists($cname))
                {
                    $c = new $cname;
                    $m = get_class_methods($c);
                    foreach($m as $method)
                    {
                        if(\zinux\kernel\utilities\string::endsWith(strtoupper($method), "INIT") || \zinux\kernel\utilities\string::startsWith(strtoupper($method),"PRE_"))
                        {
                            if(!is_callable(array($c, $method)))
                            {
                                trigger_error("The method $method found in bootstrap file `{$bs[1]}` but is not callable");
                            }
                            else
                                $c->$method($request);
                        }
                    }
                }
            }  
        }
    }
}
?>