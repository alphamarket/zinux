<?php
namespace iMVC\Routing;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Predispatcher
 *
 * @author dariush
 */
require_once 'BaseMVC.php';
require_once 'Request.php';
require_once 'Tools/String.php';
class Predispatcher extends \iMVC\BaseMVC 
{
    /**
     * Initiate pre-dispatcher values 
     */
    public function Initiate()
    {
        if(!isset($GLOBALS[CONFIGS]["dispatch"]['pre']))
            $this->_pd = array();
        else
            $this->_pd = $GLOBALS[CONFIGS]["dispatch"]['pre'];
    }
    public function Process(Request &$request)
    {
        $this->Initiate();
        $this->RunBootstarp($request);
        $this->RunINI($request);
        $this->RunReservedURI($request);
        $this->Dispose();
    }
    /**
     * Runs every function in {MODULE}\BootStrap.php function which end with 
     * 'Init' in function's name.
     * @param Request $request 
     */
    protected function RunBootstarp(Request &$request)
    {
        $bsfile_path = MODULE_PATH.$request->module."/{$request->module}Bootstrap.php";
        if(file_exists($bsfile_path))
        {
            require_once $bsfile_path;
            $cname = "{$request->module}Bootstrap";
            if(class_exists($cname))
            {
                $c = new $cname;
                $m = get_class_methods($c);
                foreach($m as $key => $method)
                {
                    if(\iMVC\Tools\String::endsWith($method, "Init"))
                    {
                        $c->$method($request);
                    }
                }
            }
            
        }
    }
    /**
     * Runs ini config file's methods
     * @param Request $request
     * @throws iMVC\Exceptions\AppException 
     */
    protected function RunINI(Request &$request)
    {
        foreach($this->_pd as $key => $value)
        {
            /* fetch & normalize all method's values */ 
            $m = array_values(array($value['method']));
            if(is_array($m[0]))
                $m = $m[0];
            if(array_key_exists('file',$value))
                require_once $value['file'];
            foreach($m as $index => $value)
            {
                $p = explode("::", $value);
                if(!class_exists($p[0]))
                    throw new iMVC\Exceptions\AppException("The predispatch class '${p[0]}' does not exists...");
                $o = new $p[0];
                if(!method_exists($o, $p[1]))
                    throw new iMVC\Exceptions\AppException("The predispatch method '${value}' does not exists...");
                $o->$p[1]($request);
            }
        }
    }
    protected function RunReservedURI(Request &$request)
    {
        if(!isset($GLOBALS[CONFIGS]['route']))
            return;
        $route = $GLOBALS[CONFIGS]['route'];
        foreach($route as $name => $uri_dir)
        {
            if(strtolower($uri_dir['URI'])==strtolower($request->getRequestURI()))
            {
                $request->setURI($uri_dir['RDR']);
                $request->Initiate();
                return;
            }
        }
    }
}

?>
