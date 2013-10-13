<?php
namespace zinux\kernel\routing;

require_once (dirname(__FILE__).'/../../baseZinux.php');

abstract class routerBootstrap extends \zinux\baseZinux
{
    private $routes = array();
    
    public abstract function Fetch();
    
    public function Initiate()
    {
        $this->routes = array();
    }
    
    public function __construct()
    {
        $this->Initiate();
    }
    
    private function regexPath($path)
    {
        return '#' . preg_replace(array("/:int:/i", "/:string:/i", "/[$]\d+/i", "#^/#", "#/$#"), array("(\d+)", "(.*)", "(.*)", "", ""), $path) . '#i';
    }

    public function addRoute($pattern, $target)
    {
        $pattern = trim($pattern, "/");
        $target = trim($target, "/");
        $this->routes[$pattern] = $target;
    }
    
    public function process(request &$request)
    {
        $matched = false;
        foreach($this->routes as $key=> $value)
        {
            $pp = $this->regexPath($key);
            $uri = trim($request->GetURI(), "/");
            if(preg_match($pp, $uri))
            {
                $request->SetURI("/".preg_replace($pp, $value, $uri));
                $matched = true;
            }
        }
        return $matched;
    }
}