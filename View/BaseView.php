<?php
namespace iMVC\View;
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
require_once 'Routing/Request.php';
class BaseView extends \iMVC\BaseMVC 
{
    /**
     * Holds the current request instance
     * @var \iMVC\Routing\Request
     */
    public $request;
    /**
     * is view rendered flag
     * @var boolean
     */
    protected $view_rendered;
    /**
     * is view suppressed flag
     * @var boolean
     */
    protected $suppress_view;
    /**
     * Construct a view instance according to passed request
     * @param \iMVC\Routing\Request $request 
     */
    public function __construct(\iMVC\Routing\Request $request)
    {
        $this->request = $request;
        $this->Initiate();
    }
    /**
     * Initialize the current view instance with proper values 
     */
    public function Initiate()
    {
        $this->SetView(str_replace("Action", "View", $this->request->action));
        $this->view_rendered = false;
        $this->suppress_view = false;
        
    }
    /**
     * Set target view's name 
     * @param string $view_name 
     */
    public function SetView($view_name)
    {
        if(!\iMVC\Tools\String::Contains(strtolower($view_name),"view"))
            $view_name .= "View";
        $this->view_name = str_replace(".phtml","", $view_name);
    }
    /**
     * Set or Unset view suppression value
     * @param boolean $value 
     */
    public function SuppressView($value = true)
    {
        $this->suppress_view = $value;
    }
    /**
     * Check status of view suppression 
     * @return boolean 
     */
    public function IsViewSuppressed()
    {
        return $this->suppress_view;
    }
    /**
     * Render a proper view
     * @throws \iMVC\Exceptions\NotFoundException If the not found
     * @throws iMVC\Exceptions\AppException If the view is loaded before
     */
    public function Render()
    {
        if($this->suppress_view)
            return;
     
        if(!$this->view_rendered)
        {
            if(!file_exists($this->GetViewPath()))
            {
                throw new \iMVC\Exceptions\NotFoundException("The view '".$this->GetViewName()."' not found!");
            } 
            require $this->GetViewPath();
            $this->view_rendered = true;
        }
        else
        {
            throw new iMVC\Exceptions\AppException("The view has been rendered previously...");
        }
    }
    /**
     * Get current view's name
     * @return string
     */
    public function GetViewName()
    {
        return $this->view_name;
    }
    
    /**
     * Get current view path
     * @return string 
     */
    public function GetViewPath()
    {
        $p = MODULE_PATH.$this->request->module.'/Views/View/'.str_replace("Controller", "", $this->request->controller).'/';
        if (($handle = opendir($p))) {
            while (false !== ($file = readdir($handle))) {
                if(strtolower($file) == strtolower($this->GetViewName().".phtml"))
                {
                    closedir($handle);
                    return $p.$file;
                }
            }
            closedir($handle);
        }
        else
            throw new \iMVC\Exceptions\InvalideOperationException("Could not open directory '$p'");
        
        # return MODULE_PATH.$this->request->module.'/Views/View/'.str_replace("Controller", "", $this->request->controller).'/'.$this->GetViewName().'.phtml';
    }
    /**
     * Partially loads a view
     * @param string $view_name view's name
     */
    public function RenderPartial($view_name, array $partial_view_params = null)
    {
        if($view_name == $this->request->view)
            throw new \iMVC\Exceptions\InvalideOperationException("Cannot partially load the currently loaded view...");
        
        // create a fake view handler
        $nv = new \iMVC\View\BaseView($this->request);
        // set view's name
        $nv->SetView($view_name);
        // is any args are set load it.
        if(isset($partial_view_params))
        {
            // import variables
            foreach($partial_view_params as $key => $value)
            {
                $nv->$key = $value;
            }
        }
        // render fake view which is going to be our partial view
        $nv->Render();
        // dispose values
        $nv->Dispose();
    }
}