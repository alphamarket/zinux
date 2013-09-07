<?php
namespace iMVC\kernel\routing;

require_once (dirname(__FILE__).'/../../baseiMVC.php');

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class request extends \iMVC\baseiMVC
{
        /**
        * hold relative module name with requested URI
        * @var \iMVC\kernel\mvc\module
        */
	public $module;
        /**
        * hold relative controller name with requested URI
        * @var \iMVC\kernel\mvc\controller
        */
	public $controller;
	/**
        * hold relative action name with requested URI
        * @var \iMVC\kernel\mvc\action
        */
	public $action;
        /**
        * Holds correspond view's name
        * @var \iMVC\kernel\mvc\view
        */
	public $view;
	/**
        * Get requested uri string
        */
	public $requested_uri;
	/**
        * Holds $_GET's value
        */
	public $GET;
	/**
        * Holds $_POST's value
        */
	public $POST;
        /**
         *  Holds params sended by $_POST, $_GET, URI 
         * @var array
         */
        public $params;
	/**
	 * Contains type of request
	 * @example
	 * if URI is /fooModule/BarController/zooAction.json/blah/blah?f=u
	 * the $type would be 'json'
	 */
	public $type;
        /**
        * hold params by index
        */
        protected $indexed_param;
        /**
         * check if current instance has been proccessed or not
         * @var boolean
         */
        protected $is_proccessed;
         
	function __construct()
	{
            $this->Initiate();
	}

	function __destruct()
	{
            $this->Dispose();
	}
        /**
         * Initializing the instance
         */
        public function Initiate()
        {
            $this->SetURI($_SERVER['REQUEST_URI']);
            $this->type = "html";
            $this->params = array();
            $this->is_proccessed = false;
        }

	/**
        * Set requested URI
        */
	public function SetURI($uri)
	{
            $this->requested_uri = $uri;
	}
        /**
        * Get requested URI
        */
	public function GetURI()
	{
            return $this->requested_uri;
	}

	/**
        * Processes the Request.
        * Extract the currect value of following attributes:
        *   Requested Module's Name
        *   Requested Controller's Name
        *   Requested Actions's Name
        *   Requested View's Name
        *   Sended GET/POST params
        * Check for final validation
        */
	public function Process()
	{
            if($this->is_proccessed) 
                return;
            $this->DepartURI();
            $this->RetrieveModuleName();
            $this->RetrieveControllerName();
            $this->RetrieveActionName();
            $this->RetrieveViewName();
            $this->RetriveParams();
            $this->is_proccessed = true;
	}

	/**
        * Depart and normalize the requested URI
        */
	protected function DepartURI()
	{
            $this->_parts = array_filter(\explode('?', $this->requested_uri));
            if(count($this->_parts)===0)
            {
                $this->_parts = array();
                return;
            }
            $this->_parts = \explode('/', $this->_parts[0]);
            /*
             * Normalizing the $this->_parts arrays
             */
            \iMVC\kernel\utilities\_array::array_normalize($this->_parts);
            # fetch page type
            if(count($this->_parts) && \iMVC\kernel\utilities\string::Contains($this->_parts[count($this->_parts)-1], "."))
            {
                $dpos = strpos($this->_parts[count($this->_parts)-1], ".");
                $this->type = substr($this->_parts[count($this->_parts)-1], $dpos+ 1);
                $this->_parts[count($this->_parts)-1] = substr($this->_parts[count($this->_parts)-1], 0, $dpos);
            }
        }

        /**
        * Fetch module name according to URI
        * @return type
        * @throws \iMVC\exceptions\notFoundException
        */
	protected function RetrieveModuleName()
	{
__LOADING_CACHE:
            # all folders in ../modules folders considered a module folder
            # define module root dir
            defined('MODULE_ROOT') || define('MODULE_ROOT',  \iMVC\kernel\utilities\fileSystem::resolve_path(iMVC_ROOT.'/../modules/')."/");
            # define default module
            $this->module = new \iMVC\kernel\mvc\module("default", MODULE_ROOT."defaultModule");
            $module_dir = dirname($this->module->GetPath());
            # module collection instance
            $mc = new \stdClass();
            # fail-safe for module dir existance
            if(!file_exists($module_dir))
                die("Couldn't find modules directory");
            # fetch all modules directory paths$name
            $modules = glob($module_dir."/*",GLOB_ONLYDIR);
            # fail-safe for module lackness
            if(!count($modules))
                die("No module found.");
            # checking if modules has been cached or not
            $xc = new \iMVC\kernel\caching\xCache(__CLASS__);
            if($xc->isCached(__METHOD__))
            {
                # catch maintaining optz. 
                $mc = $xc->retrieve(__METHOD__);
                # if the module directory has been updated
                if($mc->modified_time !=filemtime($module_dir))
                {
                    # update the catch data
                    goto __LOAD_MODULES;
                    
                }
                goto __FETCHING_MODULES;
            }
            # loading modules directories from hard drive
__LOAD_MODULES:
            # modules collections
            $mc->modules = array();
            # save directory modified time
            $mc->modified_time = filemtime($module_dir);
            # foreach module found 
            foreach($modules as $module)
            {
                # add current module into our module collections
                # except default module every other module
                # has namespace with the module's name prefix
                $m = new \iMVC\kernel\mvc\module(basename($module));
                $m->SetPath(realpath($module));
                $mc->modules[] = $m;
            }
            # now module collection is ready
            # caching module collections data
            $xc->store(__METHOD__, $mc);
            # fetching related modules accoring to requested URI
__FETCHING_MODULES:
            # if not parts provided picking up default module
            if(!count($this->_parts)) return;
            foreach($mc->modules as $module)
            {
                # checking if first part of URI matches with any modules
                if(strtolower($module->name)==strtolower($this->_parts[0]))
                {
                    # this is should NEVER ever MATCH TRUE condition
                    if(!file_exists($module->GetPath()))
                    {
                        # delete cached data
                        $xc->eraseAll();
                        # throw exception
                        throw new \iMVC\exceptions\notFoundException("Wired! `{$module->module_name}` not found at `{$module->module_path}`");
                    }
                    # saving target modules
                    $this->module = $module;
                    # removing modules name from URI parts
                    array_shift($this->_parts);
                    break;
                }
            }
	}

        /**
        * Fetch controller name according to URI
        */
	protected function RetrieveControllerName()
	{
            # default controller
            $this->controller = new \iMVC\kernel\mvc\controller("Index", $this->module);
            # head for locating controller
            if(isset($this->_parts[0]) &&
                ($file = \iMVC\kernel\utilities\fileSystem::resolve_path($this->controller->GetRootDirectory().$this->_parts[0]."Controller.php")))
            {
                # updating target controller's info
                $this->controller = new \iMVC\kernel\mvc\controller($this->_parts[0], $this->module);
            }
            # try to locate the actual indexController IO address
            elseif(($file = \iMVC\kernel\utilities\fileSystem::resolve_path($this->controller->GetRootDirectory()."IndexController.php")))
            {
                $this->controller = new \iMVC\kernel\mvc\controller("Index", $this->module);
            }
            # we found target file
            # validating controller
            if(!$this->controller->Load() || !$this->controller->CheckControllerExists())
            {
                # we don't have our class
                throw new \iMVC\exceptions\notFoundException("The controller `{$this->controller->full_name}` does not exists");
            }
            if(!$this->controller->IsValid())
            {
                throw new \ReflectionException("The controller `{$this->controller->full_name}` is not instanceof `\iMVC\kernel\controller\baseController`");
            }
            array_shift($this->_parts);
	}

        /**
        * Fetch action name according to URI
        */
	protected function RetrieveActionName()
	{
            $this->action = new \iMVC\kernel\mvc\action("Index", $this->controller);
            # the class is safe and loaded & checked in RetrieveControllerName
            # check for method existance
            if(isset($this->_parts[0]) && method_exists($this->controller->GetInstance(), "{$this->_parts[0]}Action"))
            {
                # update action info
                $this->action = new \iMVC\kernel\mvc\action("{$this->_parts[0]}Action", $this->controller);
                array_shift($this->_parts);
            }
            # if also the index method does not exists 
            elseif(!method_exists($this->controller->GetInstance(), "indexAction"))
            {
                # throw exception
                throw new \iMVC\exceptions\notFoundException("Ambiguous action call");
            }
            # validating the action
            if(!$this->action->IsActionCallable())
            {
                throw new \iMVC\exceptions\invalideOperationException("The action `{$this->action->full_name}` is not callable!");
            }
	}

        /**
        * Fetch view name according to action's name
        */
	protected function RetrieveViewName()
	{
            # we will gain view's info by info fetched for action 
            $this->view = new \iMVC\kernel\mvc\view($this->action->name, $this->action);
	}

	/**
        * Fetch params according to URI
        */
	protected function RetriveParams()
	{
            $this->GET = $_GET;
            $this->POST = $_POST;
            # merging $_GET, $_POST into $params
            $this->params = $_GET;
            $this->params = array_merge($this->params, $_POST);
            # balancing the parts' count
            if(count($this->_parts) % 2 == 1)
                $this->_parts[] = NULL;
            # while there are parts
            while(count($this->_parts))
            {
                # add to the $params
                $this->params[$this->_parts[0]] = $this->_parts[1];
                # add to indexed params
                $this->indexed_param[] = $this->_parts[0];
                # due to opration in `if` statement before current `while`
                # if NULL appears, should appears in secondary part 
                # so we only check this 
                if($this->_parts[1])
                    $this->indexed_param[] = $this->_parts[1];
                # remove fetched parts
                array_shift($this->_parts);
                array_shift($this->_parts);
            }
            # we don't need this var anymore >:)
            unset($this->_parts);
	}
    
        /**
        * Get URI params base on its index
        * @param integer $index
        * @return string
        */
        public function GetIndexedParam($index)
        {
            if(!is_integer($index)) throw new \iMVC\exceptions\invalideArgumentException;
            return $this->indexed_param[$index];
        }

	/**
        * Check if the current request has $_POST values
        */
	public function IsPOST()
	{
            return strtoupper($_SERVER['REQUEST_METHOD']) === "POST";
	}

	/**
        * check if it is a GET request or not
        */
	public function IsGET()
	{
            return strtoupper($_SERVER['REQUEST_METHOD']) === "GET";
	}
        /**
         * Get instance of current related controller 
         * @return \iMVC\kernel\controller\baseController
         */
        public function GetControllerInstance()
        {
            $r = $this;
            $c = $r->controller->namespace.'\\'.$r->controller->name."Controller";
            return new $c;
        }
        public function InvokeAction()
        {
            $c = $this->GetControllerInstance();
            $c->$this->action->GetPath();
        }
}