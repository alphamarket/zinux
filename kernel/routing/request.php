<?php
namespace iMVC\kernel\routing;

require_once (dirname(__FILE__).'/../../baseiMVC.php');

/**
 * A class that holds MVC entities info
 */
class entity
{
    /**
     * the entity's name
     * @var string
     */
    public $name;
    /**
     * the entity's namespace
     * @var string
     */
    public $namespace;
    /**
     * the entity's path
     * @var string
     */
    public $path;
        
    public function __construct($name, $path, $namespace = "")
    {
        $this->name = $name;
        $this->path = $path;
        $this->namespace = $namespace;
    }
}
/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class request extends \iMVC\baseiMVC
{
        /**
        * hold relative module name with requested URI
        * @var entity
        */
	public $module;
        /**
        * hold relative controller name with requested URI
        * @var entity
        */
	public $controller;
	/**
        * hold relative action name with requested URI
        * @var entity
        */
	public $action;
        /**
        * Holds correspond view's name
        * @var entity
        */
	public $view;
        /**
        * relative namespace with current request
        * @var string
        */
        public $namespace;
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
            $this->module = new entity("default", iMVC_ROOT."../modules");
            $this->controller = new entity("index", "{$this->module->path}/controllers/indexController.php");
            $this->action = new entity("index", "indexAction");
            $this->view = new entity("index", "{$this->module->path}/views/view/{$this->module->name}/indexView.phtml");
            $this->type = "html";
            $this->params = array();
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
            $this->DepartURI();
            $this->RetrieveModuleName();
            $this->RetrieveControllerName();
            $this->RetrieveActionName();
            $this->RetrieveViewName();
            $this->RetrieveNamespace();
            $this->RetriveParams();
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
            $this->_parts = array_filter($this->_parts, 'strlen');
            $this->_parts = count($this->_parts)? array_chunk($this->_parts, count($this->_parts)) : array();
            $this->_parts = count($this->_parts)? $this->_parts[0] : array();
            # fetch page type
            if(count($this->_parts) && \iMVC\utilities\string::Contains($this->_parts[count($this->_parts)-1], "."))
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
            $this->module = new entity("default", iMVC_ROOT."../modules/default");
            $module_dir = dirname($this->module->path);
            # module collection instance
            $mc = new \stdClass();
            # fail-safe for module dir existance
            if(!file_exists($module_dir))
                die("Couldn't find modules directory");
            # fetch all modules directory paths
            $modules = glob($module_dir."/*",GLOB_ONLYDIR);
            # fail-safe for module lackness
            if(!count($modules))
                die("No module found.");
            # if not parts provided picking up default module
            if(!count($this->_parts)) return;
            # checking if modules has been cached or not
            $fc = new \iMVC\kernel\caching\fileCache(__CLASS__);
            if($fc->isCached(__METHOD__))
            {
                # catch maintaining optz. 
                $mc = $fc->retrieve(__METHOD__);
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
                $mc->modules[] = new entity(
                                                            basename($module), 
                                                            realpath($module), 
                                                            strtolower(basename($module))=='default'?"":basename($module));
            }
            # now module collection is ready
            # caching module collections data
            $fc->store(__METHOD__, $mc);
            # fetching related modules accoring to requested URI
__FETCHING_MODULES:
            foreach($mc->modules as $module)
            {
                # checking if first part of URI matches with any modules
                if(strtolower($module->name)==strtolower($this->_parts[0]))
                {
                    # this is should NEVER ever MATCH TRUE condition
                    if(!file_exists($module->path))
                    {
                        # delete cached data
                        $fc->eraseAll();
                        # throw exception
                        throw new \iMVC\exceptions\notFoundException("Wired! `{$module->module_name}` not found at `{$module->module_path}`");
                    }
                    # saving target modules
                    $this->module = $module;
                    $this->namespace = $this->module->namespace;
                    # removing modules name from URI parts
                    array_shift($this->_parts);
                    break;
                }
            }
	}
        /**
        * Fetch relative namespace according to module's name
        * @note the <b>default</b> module has no prefix namespace since the <i>default</i> is a keywork
        */
	protected function RetrieveNamespace()
        {
            $this->namespace = $this->module->namespace;
            $this->controller->namespace = $this->namespace."\\controller";
            $this->action->namespace = $this->namespace."\\controller";
            $this->view->namespace = $this->namespace."\\view";
        }

        /**
        * Fetch controller name according to URI
        */
	protected function RetrieveControllerName()
	{
            # default controller
            $this->controller = new entity("index", "{$this->module->path}/controllers/indexController.php", $this->namespace."\\controller");
            # controller directory name
            $controller_dir = dirname($this->controller->path)."/";
            # foreach file in controller's directory
            foreach (array_diff(scandir($controller_dir), array(".", "..")) as $file)
            {
                # we are looking for files
                if(!is_file($controller_dir.$file)) continue;
                # we now processing a file
                if(isset($this->_parts[0]) && strtolower($this->_parts[0]."controller.php") == strtolower($file))
                {
                    # updating target controller's info
                    $this->controller->name = $this->_parts[0];
                    $this->controller->path = dirname($this->controller->path)."/$file";
                    break;
                    
                }
                # try to locate the actual indexController IO address
                elseif(strtolower("indexcontroller.php") == strtolower($file))
                {
                    $this->controller->path = dirname($this->controller->path)."/$file";  
                }
            }
            # we found target file
            # checking for class declaration
            require_once $this->controller->path;
            if(!class_exists("{$this->controller->namespace}\\{$this->controller->name}controller"))
            {
                # we don't have our class
                throw new \iMVC\exceptions\notFoundException("The controller `{$this->controller->name}` does not exists");
            }
            array_shift($this->_parts);
	}

        /**
        * Fetch action name according to URI
        */
	protected function RetrieveActionName()
	{
            $this->action = new entity("index", "indexAction", $this->namespace."\\controller");
            $controller = "{$this->action->namespace}\\{$this->controller->name}controller";
            if(!class_exists($controller))
                throw new \iMVC\exceptions\notFoundException("`$controller` not found!");
            $co = new $controller;
            if(isset($this->_parts[0]) && method_exists($co, "{$this->_parts[0]}Action"))
            {
                $this->action->name = $this->_parts[0];
                $this->action->path = "{$this->_parts[0]}Action";
                array_shift($this->_parts);
            }
            elseif(!method_exists($co, "indexAction"))
            {
                throw new \iMVC\exceptions\notFoundException("Ambiguous action call");
            }
	}

        /**
        * Fetch view name according to action's name
        */
	protected function RetrieveViewName()
	{
            $this->view = new entity($this->action->name, 
                    "{$this->module->path}/views/view/{$this->controller->name}/{$this->action->name}View.pthml", 
                    $this->namespace."\\view");
	}

	/**
        * Fetch params according to URI
        */
	protected function RetriveParams()
	{
            $this->GET = $_GET;
            $this->POST = $_POST;
            $this->params = $_GET;
            $this->params = array_merge($this->params, $_POST);
            if(count($this->_parts) % 2 == 1)
                $this->_parts[] = NULL;
            while(count($this->_parts))
            {
                $this->params[$this->_parts[0]] = $this->_parts[1];
                $this->indexed_param[] = $this->_parts[0];
                # due to opration in `if` statement before current `while`
                # if NULL appears, should appears in secondary part 
                # so we only check this 
                if($this->_parts[1])
                    $this->indexed_param[] = $this->_parts[1];
                array_shift($this->_parts);
                array_shift($this->_parts);
            }
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
            return isset($_POST) && count($_POST);
	}

	/**
        * check if it is a GET request or not
        */
	public function IsGET()
	{
            # if it is not a POST the it is a GET
            return !$this->IsPOST();
	}

}
?>