<?php
namespace zinux\kernel\mvc;

require_once 'mvc.php';

/**
 * Description of action
 *
 * @author dariush
 */
class action extends mvc
{ /**
     *
     * @var module
     */
    public $relative_module;
    /**
     *
     * @var controller
     */
    public $relative_controller;
    
    public function Initiate(){} 
    
    public function __construct($name, controller $controller)
    {
        if(preg_match('/(\w+)action$/i', $name))
        {
            $name=preg_replace('/action$/i', "", $name);
        }
        parent::__construct($name, "{$name}Action");
        $this->relative_controller = $controller;
        $this->relative_module = $controller->relative_module;
        $this->SetPath($this->relative_controller->GetPath());
    }

    public function GetNameSpace()
    {
        return $this->relative_controller->GetNameSpace();
    }
    
    public function IsActionCallable()
    {
        return is_callable(array($this->relative_controller->GetInstance(), $this->full_name));
    }
    public function InvokeAction(&$obj, array $param_arr = array())
    {
        return call_user_func_array(array($obj, $this->full_name), $param_arr);
    }
}
