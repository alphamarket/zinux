<?php
namespace iMVC\kernel\mvc;

require_once 'mvc.php';

/**
 * Description of module
 *
 * @author dariush
 */
class module extends mvc
{    
    public function Initiate(){}
    
    public function __construct($name, $path = "")
    {
        if(preg_match('/(\w+)module$/i', $name))
        {
            $name=preg_replace('/module$/i', "", $name);
        }
        parent::__construct($name, "{$name}Module");
        $this->SetPath($path);
    }

    public function GetNameSpace()
    {
        return "modules\\{$this->full_name}";
    }
}