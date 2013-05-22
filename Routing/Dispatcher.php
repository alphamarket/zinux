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
require_once 'View/BaseView.php';
require_once 'Layout/BaseLayout.php';
require_once 'Request.php';
use iMVC\Routing;
use iMVC\Tools;
class Dispatcher extends \iMVC\BaseMVC 
{
    public function Initiate()
    {
    }
    /**
     * Initiate the dispacther processes
     * @param Request $request 
     */
    public function Process(Request $request)
    {        
        // init this
        $this->Initiate();
        // create new controller
        $c = new $request->controller;
        // set request as a property in controller
        $c->request = $request;
        // set view object
        $c->view = new \iMVC\View\BaseView($request);
        // set layout object
        $c->layout = new \iMVC\Layout\BaseLayout($c->view);
        // init controller
        $c->Initiate();
        // call the action method
        $c->{$request->action}();
        // render : layout ~> view
        $c->layout->Render();
        // dispose controller
        $c->Dispose();
        // dispose this
        $this->Dispose();
    }
}

?>
