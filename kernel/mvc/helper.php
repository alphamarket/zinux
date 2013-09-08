<?php
namespace zinux\kernel\mvc;

require_once 'mvc.php';

/**
 * Description of helper
 *
 * @author dariush
 */
class helper extends mvc
{
 /**
     *
     * @var module
     */
    public $relative_module;
    
    public function Initiate(){}
    
    public function __construct($name, module $module, $auto_load = 1)
    {
        if(preg_match('/(\w+)helper$/i', $name))
        {
            $name=preg_replace('/helper$/i', "", $name);
        }
        parent::__construct($name, "{$name}Helper");
        $this->relative_module = $module;
        $this->SetPath("{$this->relative_module->GetPath()}".DIRECTORY_SEPARATOR.
            "views".DIRECTORY_SEPARATOR.
            "helper".DIRECTORY_SEPARATOR.
            "{$this->full_name}{$this->extension}");
         if($auto_load)
             $this->Load();
    }

    public function GetNameSpace()
    {
        return $this->relative_module->GetNameSpace()."\\views\\helper";
    }
    
    public function CheckHelperExists()
    {
        return file_exists($this->GetPath());
    }
}
