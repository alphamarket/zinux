<?php
namespace zinux\kernel\mvc;

require_once 'mvc.php';
/**
 * Description of layout
 *
 * @author dariush
 */
class layout extends mvc
{
    /**
     *
     * @var module
     */
    public $relative_module;
    
    public function Initiate(){}
    
    public function __construct($name, module $module)
    {
        $this->extension = ".phtml";
        if(preg_match('/\w+(layout)('.$this->extension.')?$/i', $name))
        {
            $name=preg_replace('/layout('.$this->extension.')?$/i', "", $name);
        }
        parent::__construct($name, "{$name}Layout");
        $this->relative_module = $module;
        $this->setPath("{$this->relative_module->GetPath()}".
            DIRECTORY_SEPARATOR.
            "views".
            DIRECTORY_SEPARATOR.
            "layout".
            DIRECTORY_SEPARATOR.
            "{$this->full_name}{$this->extension}", 0);
    }

    public function GetNameSpace()
    {
        return $this->relative_module->GetNameSpace()."\\layout";
    }
    
    public function Load()
    {
        require $this->GetPath();
    }
}
