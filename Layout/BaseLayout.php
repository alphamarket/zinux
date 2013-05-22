<?php
namespace iMVC\Layout;
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
class BaseLayout extends \iMVC\BaseMVC 
{
    /**
     * View object related to layout
     * @var \iMVC\View\BaseView
     */
    public $view;
    /**
     * Construct a new layout with given view instance
     * @param \iMVC\View\BaseView $view 
     */
    public function __construct(\iMVC\View\BaseView $view)
    {
        $this->view = $view;
        $this->Initiate();
    }
    /**
     * Initiate the layout vars 
     */
    public function Initiate()
    {
        $this->SetDefaultLayout();
        $this->layout_rendered = false;
        $this->suppress_layout = false;
    }
    /**
     * Renders Layout and View
     * @throws \Exceptions\AppException 
     */
    public function Render()
    {
        if(!$this->layout_rendered)
        {
            ob_start();
                $this->view->Render();
                $this->content = ob_get_contents();
            ob_end_clean();
            if(!file_exists($this->GetLayoutPath()))
            {
                echo "<center><h2>Layout not loaded ...<br />The layout '".$this->GetLayoutName ()."' not found!</center></h2>";
                $this->suppress_layout = true;
            }
            if(!$this->view->IsViewSuppressed() && !$this->suppress_layout)
            {
                require $this->GetLayoutPath();
            }
            else
                echo $this->content;
            $this->layout_rendered = true;
        }
        else
        {
            throw new \Exceptions\AppException("The view has been rendered previously...");
        }
    }
    /**
     * Sets layout's name to its default
     */
    public function SetDefaultLayout()
    {
        $this->SetLayout('default');
    }
    /**
     * Set layout name
     * @param string $layout_name 
     */
    public function SetLayout($layout_name)
    {
        $this->layout_name = str_replace(".phtml","", $layout_name);
        $this->SuppressLayout(false);
    }
    /**
     * Set or Unset layout suppression value
     * @param boolean $value 
     */
    public function SuppressLayout($value = true)
    {
        $this->suppress_layout = $value;
    }
    /**
     * Check status of view suppression 
     * @return boolean 
     */
    public function IsLayoutSuppressed()
    {
        return $this->suppress_layout;
    }
    /**
     * Get layout's file's name
     * @return string
     */
    public function GetLayoutName()
    {
        return $this->layout_name;
    }
    /**
     * Get layout full path
     * @return string
     */
    public function GetLayoutPath()
    {
        return MODULE_PATH.$this->view->request->module.'/Views/Layout/'.$this->GetLayoutName().'.phtml';
    }
}