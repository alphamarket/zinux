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
use iMVC\Routing;
class Postdispatcher extends \iMVC\BaseMVC 
{
    /**
     * Initiate post dispatcher's values 
     */
    public function Initiate()
    {
        if(!isset($GLOBALS['CONFIGS']["dispatch"]['post']))
            $this->_pd = array();
        else
            $this->_pd = $GLOBALS['CONFIGS']["dispatch"]['post'];
    }
    /**
     * Initiate post-dipatcher processes 
     * @param Request $request
     * @throws iMVC\Exceptions\AppException 
     */
    public function Process(Request $request)
    {
        $this->Initiate();
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
        $this->Dispose();
    }
}

?>
