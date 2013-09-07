<?php
namespace iMVC\kernel\mvc;

require_once 'mvc.php';

/**
 * Description of controller
 *
 * @author dariush
 */
class controller extends mvc
{ 
    /**
     *
     * @var module
     */
    public $relative_module;
    
    public function Initiate(){}
    
    public function __construct($name, module $module)
    {
        if(preg_match('/(\w+)controller$/i', $name))
        {
            $name=preg_replace('/controller$/i', "", $name);
        }
        parent::__construct($name, "{$name}Controller");
        $this->relative_module = $module;
        $this->SetPath("{$this->relative_module->GetPath()}".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."{$this->full_name}{$this->extension}");
    }

    public function GetNameSpace()
    {
        return $this->relative_module->GetNameSpace()."\\controllers";
    }
    
    public function CheckControllerExists()
    {
        return class_exists($this->GetClassFullName());
    }
    
    public function GetClassFullName()
    {
        return $this->GetNameSpace()."\\".$this->full_name;
    }
    /**
     * 
     * @return \iMVC\kernel\controller\baseController
     */
    public function GetInstance()
    {
        $c = $this->GetClassFullName();
        return new $c;
    }
}