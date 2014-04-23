<?php
namespace zinux\kernel\routing;

require_once (dirname(__FILE__).'/../../baseZinux.php');

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class request extends \zinux\baseZinux
{
        /**
        * hold relative module name with requested URI
        * @var \zinux\kernel\mvc\module
        */
	public $module;
        /**
        * hold relative controller name with requested URI
        * @var \zinux\kernel\mvc\controller
        */
	public $controller;
	/**
        * hold relative action name with requested URI
        * @var \zinux\kernel\mvc\action
        */
	public $action;
        /**
        * Holds correspond view's name
        * @var \zinux\kernel\mvc\view
        */
	public $view;
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
        public $indexed_param;
        /**
         * check if current instance has been proccessed or not
         * @var boolean
         */
        protected $is_proccessed;
	/**
        * Get requested uri string
        */
	protected $requested_uri;
         
	public function __construct(request $request = NULL)
	{
            # set the current URI by default
            $this->SetURI($_SERVER['REQUEST_URI']);
            # initiate the initiation
            $this->Initiate();
            # if request was set
            if($request)
            {
                # clone the request in $this
                foreach($request as $name=> $value)
                {
                    # clone the $name => $value
                    $this->$name =$value;
                }
            }
	}
        /**
         * Initializing the instance
         */
        public function Initiate()
        {
            $this->type = "html";
            $this->params = array();
            $this->indexed_param = array();
            $this->GET = array();
            $this->POST = array();
            $this->is_proccessed = false;
            $this->action = NULL;
            $this->controller = NULL;
            $this->module = NULL;
            $this->view = NULL;
        }

	/**
        * Set requested URI
        */
	public function SetURI($uri)
	{
            $this->is_proccessed = false;
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
            $this->Initiate();
            $this->TokenizeURI();
            $this->FetchModuleName();
            $this->FetchControllerName();
            $this->FetchActionName();
            $this->FetchViewName();
            $this->FetchParams();
            $this->is_proccessed = true;
	}

	/**
        * Depart and normalize the requested URI
        */
	protected function TokenizeURI()
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
            \zinux\kernel\utilities\_array::array_normalize($this->_parts);
            # fetch page type
            if(count($this->_parts) && \zinux\kernel\utilities\string::Contains($this->_parts[count($this->_parts)-1], "."))
            {
                $dpos = strpos($this->_parts[count($this->_parts)-1], ".");
                $this->type = substr($this->_parts[count($this->_parts)-1], $dpos+ 1);
                $this->_parts[count($this->_parts)-1] = substr($this->_parts[count($this->_parts)-1], 0, $dpos);
            }
        }

        /**
        * Fetch module name according to URI
        * @return type
        * @throws \zinux\kernel\exceptions\notFoundException
        */
	protected function FetchModuleName()
	{
__LOADING_CACHE:
            # all folders in ../modules folders considered a module folder
            # define module root dir
            defined('MODULE_ROOT') || define('MODULE_ROOT',  \zinux\kernel\utilities\fileSystem::resolve_path(ZINUX_ROOT.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'modules'));
            # define default module
            $this->module = new \zinux\kernel\mvc\module("default", MODULE_ROOT."defaultModule");
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
            # don't use xCache it will overload the session file
            $fc = new \zinux\kernel\caching\fileCache(__CLASS__);
            # checking if module cached
            if($fc->isCached(__FUNCTION__))
            {
                # catch maintaining optz. 
                $mc = $fc->fetch(__FUNCTION__);
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
            # fetching namespace prefixes for modules
            $namespace_prex = \trim(\str_replace(array(PROJECT_ROOT, \DIRECTORY_SEPARATOR), array("", "\\"), MODULE_ROOT), "\\");
            # foreach module found 
            foreach($modules as $module)
            {
                # add current module into our module collections
                # except default module every other module
                # has namespace with the module's name prefix
                $m = new \zinux\kernel\mvc\module(basename($module), $module, $namespace_prex);
                $mc->modules[] = $m;
            }
            # now module collection is ready
            # caching module collections data
            $fc->save(__FUNCTION__, $mc);
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
                        $fc->deleteAll();
                        # throw exception
                        throw new \zinux\kernel\exceptions\notFoundException("Wired! `{$module->module_name}` not found at `{$module->module_path}`");
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
	protected function FetchControllerName()
	{
            # default controller
            $this->controller = new \zinux\kernel\mvc\controller("index", $this->module);
            # head for locating controller
            if(isset($this->_parts[0]) &&
                ($file = \zinux\kernel\utilities\fileSystem::resolve_path($this->controller->GetRootDirectory().$this->_parts[0]."Controller.php")))
            {
                # updating target controller's info
                $this->controller = new \zinux\kernel\mvc\controller($this->_parts[0], $this->module);
                array_shift($this->_parts);
            }
            # try to locate the actual indexController IO address
            elseif(($file = \zinux\kernel\utilities\fileSystem::resolve_path($this->controller->GetRootDirectory()."indexController.php")))
            {
                $this->controller = new \zinux\kernel\mvc\controller("index", $this->module);
            }
            # we found target file
            # validating controller
            if(!$this->controller->Load() || !$this->controller->CheckControllerExists())
            {
                # we don't have our class
                throw new \zinux\kernel\exceptions\notFoundException("The controller `{$this->controller->GetClassFullName()}` does not exists at `{$this->controller->GetPath()}`!");
            }
            if(!$this->controller->IsValid())
            {
                throw new \ReflectionException("The controller `{$this->controller->full_name}` is not instanceof `\zinux\kernel\controller\baseController`");
            }
	}

        /**
        * Fetch action name according to URI
        */
	protected function FetchActionName()
	{
            $this->action = new \zinux\kernel\mvc\action("index", $this->controller);
            # the class is safe and loaded & checked in FetchControllerName
            # check for method existance
            if(isset($this->_parts[0]) && method_exists($this->controller->GetInstance(), "{$this->_parts[0]}Action"))
            {
                # update action info
                $this->action = new \zinux\kernel\mvc\action("{$this->_parts[0]}Action", $this->controller);
                array_shift($this->_parts);
            }
            # if also the index method does not exists 
            elseif(!method_exists($this->controller->GetInstance(), "indexAction"))
            {
                # throw exception
                throw new \zinux\kernel\exceptions\notFoundException("Ambiguous action call");
            }
            # validating the action
            if(!$this->action->IsActionCallable())
            {
                throw new \zinux\kernel\exceptions\invalideOperationException("The action `{$this->action->full_name}` is not callable!");
            }
	}

        /**
        * Fetch view name according to action's name
        */
	protected function FetchViewName()
	{
            # we will gain view's info by info fetched for action 
            $this->view = new \zinux\kernel\mvc\view($this->action->name, $this->action);
	}

	/**
        * Fetch params according to URI
        */
	protected function FetchParams()
	{
            $this->GET = $_GET;
            $this->POST = $_POST;
            # balancing the parts' count
            if(count($this->_parts) % 2 == 1)
                $this->_parts[] = NULL;
            # while there are parts
            while(count($this->_parts))
            {
                # add to the $params
                $this->params[$this->_parts[0]] = $this->_parts[1];
                # remove fetched parts
                array_shift($this->_parts);
                array_shift($this->_parts);
            }
            # add to items into indexed params
            $this->GenerateIndexedParams();
            # merging $_GET, $_POST into $params 
            # we need to do it at the end of fetching 
            # params 'cause its imposible to use
            # GetIndexedParam()
            $this->params += array_merge($_GET, $_POST);
            # we don't need this var anymore >:)
            unset($this->_parts);
	}
        /**
        * Generates an indexed params array base on `$this->params`
        * @return array The index params
        */
        public function GenerateIndexedParams()
        {
            # clear the indexed params
            $this->indexed_param = array();
            # add to items into indexed params
            foreach($this->params as $key => $param)
            {
                $this->indexed_param[] = $key;
                if($param)
                    $this->indexed_param[] = $param;
            }
            # return the indexed params
            return $this->indexed_param;
        }
        /**
        * Get URI params base on its index
        * @param integer $index
        * @return string
        */
        public function GetIndexedParam($index)
        {
            if(!is_integer($index)) throw new \zinux\kernel\exceptions\invalideArgumentException;
            return $this->indexed_param[$index];
        }
        /**
        * Set URI params base on its index
        * @param integer $index
        * @return string
        */
        public function SetIndexedParam($index, $value)
        {
            if(!is_integer($index)) throw new \zinux\kernel\exceptions\invalideArgumentException;
            $this->indexed_param[$index] = $value;
        }
        /**
        * Get count of URI params base on its index
        * @return integer
        */
        public function CountIndexedParam()
        {
            return count($this->indexed_param);
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
        public function InvokeAction()
        {
            $c = $this->GetControllerInstance();
            $c->$this->action->GetPath();
        }
}