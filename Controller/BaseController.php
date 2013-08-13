<?php
namespace iMVC\Controller;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseController
 *
 * @author dariush
 */
require_once 'BaseMVC.php';
require_once 'View/BaseView.php';
use iMVC\View;
abstract class BaseController extends \iMVC\BaseMVC 
{    
    public abstract function IndexAction();
    /**
     * Holds current view handler's instance
     * @var \iMVC\View\BaseView
     */
    public $view;
    /**
     * Holds current layout handler's instance
     * @var \iMVC\Layout\BaseLayout
     */
    public $layout;
    /**
     * Holds current request instance
     * @var \iMVC\Routing\Request 
     */
    public $request;
    /**
     * Dispose current controller 
     */
    public function Dispose()
    {
        $this->request->Dispose();
        $this->view->Dispose();
        $this->layout->Dispose();
        parent::Dispose();   
    }
    public function ToRespond($call_back_function)
    {
        $call_back_function(strtolower($this->request->TYPE), $this);
    }
    
    public function RenderSerialized($obj)
    {
        $this->layout->SuppressLayout();
        echo serialize($obj);
    }
    
    public function RenderJSON($obj)
    {
        $this->layout->SuppressLayout();
        echo json_encode($obj);
    }
}

?>
