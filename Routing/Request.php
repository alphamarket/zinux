<?php
namespace iMVC\Routing;

require_once 'BaseMVC.php';

class Request extends \iMVC\BaseMVC 
{
    /**
     * Holds fragmented parameters 
     * @var array 
     */
    public $partial_params;
    /**
     * Hold hashed paramaters with named index-value
     * @var array
     */
    public $params;
    /**
     * hold relative module name with requested URI
     * @var string
     */
    public $module;
    /**
     * hold relative controller name with requested URI
     * @var string
     */
    public $controller;
    /**
     * hold relative action name with requested URI
     * @var string
     */
    public $action;
    /**
     * Holds correspond view's name
     * @var string
     */
    public $view;
    /**
     * Get requested uri string
     * @var string
     */
    public $requestURI;
    /**
     * Holds $_GET's value
     * @var array
     */
    public $GET;
    /**
     * Holds $_POST's value
     * @var array
     */
    public $POST;
    /**
     * Contains type of request
     * @example 
     * if URI is /fooModule/BarController/zooAction.json/blah/blah?f=u
     * the $TYPE would be 'json'
     * @var string
     */
    public $TYPE;


    public function __construct()
    {
        $this->Initiate();
    }
    /**
     * Initilize a new request according to passed request instance
     * @param Request $request 
     */
    public static function WithRequest(Request $request)
    {
        $r = new Request();
        $r->module = $request->module;
        $r->controller = $request->controller;
        $r->action = $request->action;
        $r->partial_params = $request->partial_params;
        $r->params = $request->params;
        return $r;
    }
    /**
     * Initilize & calculate Module/Controller/Action/Params values
     */
    public function Initiate()
    {
        $this->setURI($_SERVER['REQUEST_URI']);
        $this->module = "Default";
        $this->controller = "IndexController";
        $this->action = "IndexAction";
        // defines how many part of URI is matched with pattern
        $this->_URI_Accept_Level = 0;
        // defines URI index processed by the request handler
        $this->_process_level = 0;
        $this->partial_params = array();
        $this->params = array();
        $this->ProcessRequest();
    }
    /**
     * Set requested URI
     * @param string $uri 
     */
    public function setURI($uri)
    {
        $this->requestURI = $uri;
        $_SERVER['REQUEST_URI'] = $uri;
    }
    /**
     * Get requested URI
     * @return string
     */
    public function getRequestURI()
    {
        return $this->requestURI;
    }
    /**
     * Processes the Request.
     * Extract the currect value of following attributes:
     *      Requested Module's Name
     *      Requested Controller's Name
     *      Requested Actions's Name
     *      Requested View's Name
     *      Sended GET/POST params
     * Check for final validation
     */
    protected function ProcessRequest()
    {
        $this->DepartURI();
        $this->RetrieveModuleName();
        $this->RetrieveControllerName();
        $this->RetrieveActionName();
        $this->RetrieveViewName();
        $this->RetriveParams();
        $this->Checkpoint();
        $this->Dispose();
    }
    public function LoadControllerFile()
    {
        return $this->ImportControllerFile($this->controller);
    }
    /**
     * Checks if the ultimate Module/Controller/Action URI is a currect one?
     */
    protected function Checkpoint()
    {
        if(!$this->LoadControllerFile())
            throw new \iMVC\Exceptions\NotFoundException("The requested URI does not exists...");
    }
    /**
     * Depart and normalize the requested URI 
     */
    protected function DepartURI()
    {
        $parts = array_filter(\explode('?', $this->requestURI));
        $parts = \explode('/', $parts[0]);
        /*
         * Normalizing the $parts arrays
         */
        $parts = array_filter($parts);
        $parts = count($parts)? array_chunk($parts, count($parts)) : array();
        $parts = count($parts)? $parts[0] : array();
        $this->_parts = $parts;
    }
    /**
     * Fetch module name according to URI 
     * @throws \iMVC\Exceptions\NotFoundException 
     */
    protected function  RetrieveModuleName()
    {
        if(count($this->_parts)==0)
            return;
        $checked = 0;
        /** read online modules **/
        unset($GLOBALS['CONFIGS']['imvc']['module']);
        $h = opendir(MODULE_PATH);
        while(($c = readdir($h))!=NULL)
        {
            if($c == '.' || $c=='..' || \iMVC\Tools\String::startsWith($c, '__'))
                continue;            
            $GLOBALS['CONFIGS']['imvc']['module'][] = $c;
        }
        foreach($GLOBALS['CONFIGS']['imvc']['module'] as $index => $module)
        {
            //  check normalized
            if(strtolower($module)==strtolower($this->_parts[0]))
            {
                if(!file_exists(MODULE_PATH.$module))
                {
                    throw new \iMVC\Exceptions\NotFoundException("Wired! $module not found at '".MODULE_PATH."$module' ...!");
                }
                $this->module = $module;
                if(strtolower($this->module)!="default")
                    $this->controller = "{$module}_{$this->controller}";
                    
                // defines how many part of URI is matched with pattern
                $this->_URI_Accept_Level++;
                $checked = true;
                break;
            }
        }
        if(!$checked)
        {
            $this->_URI_Accept_Level = 0;
        }
        $this->_process_level = 1;
        $this->module;
    }
    
    /**
     * Fetch controller name according to URI 
     */
    protected function  RetrieveControllerName()
    {   
        $this->_process_level++;
        if(count($this->_parts) > $this->_URI_Accept_Level && ($c = $this->_parts[$this->_URI_Accept_Level]))
        {
            if($this->CheckControllerClass($c))
            {
                // if $c is a valid controller class 
                // increase URI matched level 
                $this->_URI_Accept_Level++;
            }
            else
            {
                // if any controller named $c
                // go on with default controller for now.
                $this->CheckControllerClass($this->controller);
            }
        }
    }
    
    /**
     * Fetch action name according to URI 
     */
    protected function RetrieveActionName()
    {
        $this->_process_level++;
        if(count($this->_parts) > $this->_URI_Accept_Level && ($c = $this->_parts[$this->_URI_Accept_Level]))
        {
            # fetch page type
            if(\iMVC\Tools\String::Contains($c, "."))
            {
                $dpos = strpos($c, ".");
                $this->TYPE = substr($c, $dpos+ 1);
                $c = substr($c, 0, $dpos);
            }
            if(method_exists($this->controller, "${c}Action"))
            {
                if(!is_callable(array($this->controller, "${c}Action")))
                {
                    throw new \iMVC\Exceptions\NotFoundException("The method '".$this->controller."::${c}Action' exists. but not callable [ this may the result of mothod's incorrect modifier... ]");
                }
                $this->_URI_Accept_Level++;
                $this->action = "${c}Action";
            }
        }
    }
    /**
     * Fetch view name according to action's name 
     */
    protected function RetrieveViewName()
    {
        $this->view = str_replace("Action", "View", $this->action);
    }
    /**
     * Fetch params according to URI 
     */
    protected function RetriveParams()
    {
        $this->partial_params = array();
        $this->params = array();
        $this->_process_level++;
        if(count($this->_parts) > $this->_URI_Accept_Level)
        {
            for($i=$this->_URI_Accept_Level;$i<count($this->_parts);$i++)
            {
                // extract GET values from last params
                $this->_parts[$i] = explode("?",$this->_parts[$i]);
                if(strlen($this->_parts[$i][0])!=0)
                {
                    $this->partial_params[] = $this->_parts[$i][0];
                }
            }
            for($i=0;$i<count($this->partial_params);$i++)
            {
                $spart = NULL;
                if(count($this->partial_params) > $i+1)
                    $spart = $this->partial_params[$i+1];
                $this->params[$this->partial_params[$i]] = $spart;
                if($spart == NULL)
                    break;
                else
                    $i++;
            }
        }
        // add $_GET values as request params
        $this->GET = $_GET;
        // add $_POST values as request params
        $this->POST = $_POST;
        // if $TYPE didin't get set, set to default;
        if(!$this->TYPE)
            $this->TYPE = "html";
    }
    /**
     * Check if passed controller class exists
     * @param string $c
     * @return true if controller's class exists
     * @throws \iMVC\Exceptions\NotFoundException 
     */
    protected function CheckControllerClass($c)
    {
        // normalize the controller's class's name
        $c = str_replace("Controller", "", $c);
        $c = "${c}Controller";
        
        if(!\iMVC\Tools\String::Contains(strtolower($c), strtolower($this->module)) && strtolower($this->module)!="default")
        {
            $c = "{$this->module}_{$c}";
        }
        
        if($this->ImportControllerFile($c))
        {
            if(class_exists($c))
            {
                $this->controller = $c;
                return true;

            }
            else
                throw new \iMVC\Exceptions\NotFoundException("The file '{$c}Controller.php' found but no sign of '${c}Controller' class");
        }
        else
            return false;
    }
    /**
     * Imports passed controller file using provided module name
     * @param string $c
     * @return boolean returns true if file imported nice; otherwise false
     */
    protected function ImportControllerFile($c)
    {
        $c_path = \MODULE_PATH.$this->module."/Controllers/${c}.php";
        if(file_exists($c_path))
        {
            require_once $c_path;
            return true;
        }
        return false;
    }
    /**
     * Check if the current request has $_GET values
     * @return boolean
     */
    public function IsGET()
    {
        return isset($_GET) && count($_GET)!=0;
    }
    /**
     * Get $_GET's value;
     * @return array 
     */
    public function GetGET()
    {
        return $_GET;
    }
    /**
     * Check if the current request has $_POST values
     * @return boolean
     */
    public function IsPOST()
    {
        return isset($_POST) && count($_POST)!=0;
    }
    /**
     * Get $_POST's value;
     * @return array 
     */
    public function GetPOST()
    {
        return $_POST;
    }
    /**
     * Send an internal request
     * The system with thread the requested /module/controller/action as a 
     * completely new request.
     * But you cannot post GET/POST values.
     * Any GET values passed with $_args_string like '?foo=bar&id=123' will 
     * convert to '/foo/bar/id/123' so in target action function these params
     * can be accessed by Request::params values.
     * For example:
     * <code>
     * <?php
     *      // in some (*)View.phtml     
     *      $this->request->SendInteralRequest('AjaxCall', NULL, NULL, "/foo/bar/id/123");
     *      // or 
     *      $this->request->SendInteralRequest('AjaxCall', NULL, NULL, "/foo/bar?id=123");
     *      // or 
     *      $this->request->SendInteralRequest('AjaxCall', NULL, NULL, "?foo=bar&id=123");
     * ?>
     * </code>
     * Will call 'AjaxCallAction' in current loaded /Module/Controller's class 
     * @param string $action target action's name(required)
     * @param string $controller target controller name; if not supplied the 
     * target controller would be the one which is currently loaded. (non-required)
     * @param string $module target module name; if not supplied the 
     * target module would be the one which is currently loaded. (non-required)
     * @param string $_args_string passed arguments with URI
     * @throws \iMVC\Exceptions\InvalideArgumentException The action cannot be NULL
     * @deprecated since version 1.0.1.1
     */
    public function SendInternalRequest($action, $controller = NULL, $module = NULL, $_args_string = NULL)
    {
        trigger_error("This function is deprecated, please use \iMVC\Routing\FakeRequest", E_USER_NOTICE);
        
        if(!isset($action))
            throw new \iMVC\Exceptions\InvalideArgumentException("The \$action name is not setted ...");
        if(!isset($controller))
            $controller = $this->controller;
        if(!isset($module))
            $module = $this->module;
        if(!isset($_args_string))
            $_args_string = "";
        if(!is_string($_args_string))
            throw new \iMVC\Exceptions\InvalideArgumentException("The \$_args_string should be a string");
        $action = str_replace("Action", "", $action);
        $controller = str_replace("Controller", "", $controller);
        $r = new Request();
        /**
         * Backup currently request URI 
         */
        $srvr_rq = $this->getRequestURI();
        /**
         * Normalize $_args_string
         */
        $_args_string = str_replace("?", "", $_args_string);
        $_args_string = str_replace("=", "/", $_args_string);
        $_args_string = str_replace("&", "/", $_args_string);
        /**
         * Set new request URI 
         */
        $r->setURI("/".implode('/', array($module, $controller, $action, $_args_string)));
        // process the fake request given
        $r->ProcessRequest();
        /**
         * Create fake request 
         */
        $router = new Router();
        /**
         * Run the fake router with fake request! 
         */
        $router->Run($r);
        /**
         * Restore backuped request URI 
         */
        $this->setURI($srvr_rq);
        /**
         * Reset current request 
         */
        parent::SetRequest($this);
    }
}