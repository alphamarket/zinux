<?php
namespace zinux\kernel\mvc;

require_once 'mvc.php';

/**
 * Description of module
 *
 * @author dariush
 */
class module extends mvc
{    
    public function Initiate(){}
    
    public function __construct($name, $path = "", $namespace_prefix = "modules")
    {
        if(preg_match('/(\w+)module$/i', $name))
        {
            $name=preg_replace('/module$/i', "", $name);
        }
        parent::__construct($name, "{$name}Module");
        $this->SetPath(realpath($path));
        $this->namespace_prex = $namespace_prefix;
    }

    public function GetNameSpace()
    {
        return "{$this->namespace_prex}\\{$this->full_name}";
    }
}