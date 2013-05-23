<?php
namespace iMVC\Routing;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Router
 *
 * @author dariush
 */
require_once 'BaseMVC.php';
require_once 'Request.php';
require_once 'Predispatcher.php';
require_once 'Dispatcher.php';
require_once 'Postdispatcher.php';
class Router extends \iMVC\BaseMVC 
{
    public function Initiate(){}
    /**
     * Route the passed request
     * @param \iMVC\Routing\Request $request 
     */
    public function Run($request)
    {
        // register the current request
        parent::SetRequest($request);
        // pre-dispatcher
        $predisp = new Predispatcher();
        $predisp->Process($request);
        // dispatcher
        $disp = new Dispatcher();
        $disp->Process($request);
        // post-dispatcher
        $postdisp = new Postdispatcher();
        $postdisp->Process($request);
        
    }
}

?>
