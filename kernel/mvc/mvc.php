<?php
namespace iMVC\kernel\mvc;

require_once (dirname(__FILE__).'/../../baseiMVC.php');

/**
 * Description of action
 *
 * @author dariush
 */
abstract class mvc extends \iMVC\baseiMVC
{ 
    /**
     *
     * @var string
     */
    public $name;
    /**
     *
     * @var string
     */
    public $full_name;
    /**
     *
     * @var string
     */
    private $path;
    /**
     *
     * @var string
     */
    protected $extension = ".php";
    
    public function __construct($name, $full_name = "")
    {
        $this->name = $name;
        $this->full_name = $name;
        if(strlen($full_name))
            $this->full_name = $full_name;
    }
    
    public abstract function GetNameSpace();
    
    public function GetRootDirectory(){return dirname($this->path).DIRECTORY_SEPARATOR;}
    
    public function GetExtention(){ return $this->extension; }
    
    public function  SetPath($path, $validate = 1)
    {
        $this->path = \iMVC\kernel\utilities\fileSystem::resolve_path($path);
        if($validate && !file_exists($this->path))
        {
            throw new \iMVC\exceptions\notFoundException("`{$path}` does not exists ...");
        }
    }
    public function GetPath(){ return $this->path; }
    
    public function Load(){ return require_once $this->GetPath(); }
}
