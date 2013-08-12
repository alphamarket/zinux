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
    
    function IsSecure(array $array, array $existance_array = array(), array $check_sum_array = array(), $do_exception = 1, $verbose_exceptions = 0)
    {
        if(!isset($array))
            if($do_exception)
                throw new \InvalidArgumentException($verbose_exceptions?"The array is not setted":"");
            else return false;
        
        if(!count($check_sum_array) && !count($existance_array))
            throw new \InvalidArgumentException("\$existance_array is not supplied but demads operation on \$check_sum_array!!");
        
        if(count($existance_array) && !count($existance_array))
            if($do_exception)
                throw new \InvalidArgumentException($verbose_exceptions?"The target array in empy!":"");
            else return false;
        
        foreach($existance_array as $value)
        {
            if(!isset($array[$value]))
                if($do_exception)
                    throw new \InvalidArgumentException($verbose_exceptions?"The argumen `$value` didn't supplied":"");
                else return false;
        }
        foreach($check_sum_array as $key=> $value)
        {
            if($array[$key] != $value)
                if($do_exception)
                    throw new \InvalidArgumentException($verbose_exceptions?"The `$key`'s value didn't match with `$value`":"");
                return false;
        }
        return true;
    }
}

?>
