<?php
namespace zinux\kernel\mvc;

require_once 'mvc.php';

/**
 * Description of view
 *
 * @author dariush
 */
class view extends mvc
{
    /**
     *
     * @var module
     */
    public $relative_module;
    /**
     *
     * @var controller
     */
    public $relative_controller;
    /**
     *
     * @var action
     */
    public $relative_action;
    
    public function Initiate(){}
    
    public function __construct($name, action $action)
    {
        if(preg_match('/(\w+)view$/i', $name))
        {
            $name=preg_replace('/view$/i', "", $name);
        }
        parent::__construct($name, "{$name}View");
        $this->relative_action = $action;
        $this->relative_controller = $action->relative_controller;
        $this->relative_module = $action->relative_module;
        $this->extension = ".phtml";
        $this->SetPath("{$this->relative_module->GetPath()}".
            DIRECTORY_SEPARATOR.
            "views".
            DIRECTORY_SEPARATOR.
            "view".
            DIRECTORY_SEPARATOR.
            "{$this->relative_controller->name}".
            DIRECTORY_SEPARATOR.
            "{$this->full_name}{$this->extension}");
    }

    public function GetNameSpace()
    {
        return $this->relative_module->GetNameSpace()."\\view";
    }
    
    public function Load()
    {
        return require $this->GetPath();
    }
}