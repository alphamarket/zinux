<?php
namespace iMVC\kernel\routing;

require_once (dirname(__FILE__).'/../../baseiMVC.php');

class entity
{
    /**
     * the entity's name
     * @var string
     */
    public $name;
    /**
     * the entity's path
     * @var string
     */
    public $path;
        
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
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
	 * Contains type of request
	 * @example
	 * if URI is /fooModule/BarController/zooAction.json/blah/blah?f=u
	 * the $type would be 'json'
	 */
	public $type;

	function __construct()
	{
            $this->Initiate();
	}

	function __destruct()
	{
            $this->Dispose();
	}

        public function Initiate()
        {
            $this->SetURI($_SERVER['REQUEST_URI']);
            $this->module = new entity("default", iMVC_ROOT."../modules");
            $this->controller = new entity("index", "{$this->module->path}/controllers/indexController.php");
            $this->action = new entity("index", "indexAction");
            $this->view = new entity("index", "{$this->module->path}/views/view/{$this->module->name}/indexView.phtml");
            $this->type = "html";
            // defines how many part of URI is matched with pattern
            $this->_URI_Accept_Level = 0;
            // defines URI index processed by the request handler
            $this->_process_level = 0;
            $this->partial_params = array();
            $this->params = array();
        }
        public function Dispose()
        {
            parent::Dispose();
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
	public function ProcessRequest()
	{
            $this->DepartURI();
            $this->RetrieveModuleName();
            $this->RetrieveNamespace();
            $this->RetrieveControllerName();
            $this->RetrieveActionName();
            $this->RetrieveViewName();
            $this->RetriveParams();
            #$this->Checkpoint();
	}

	/**
	 * Depart and normalize the requested URI
	 */
	protected function DepartURI()
	{
            $parts = array_filter(\explode('?', $this->requested_uri));
            if(count($parts)===0)
            {
                $this->_parts = array();
                return;
            }
            $parts = \explode('/', $parts[0]);
            /*
             * Normalizing the $parts arrays
             */
            $parts = array_filter($parts, 'strlen');
            $parts = count($parts)? array_chunk($parts, count($parts)) : array();
            $parts = count($parts)? $parts[0] : array();
            # fetch page type
            if(count($parts) && \iMVC\utilities\string::Contains($parts[count($parts)-1], "."))
            {
                $dpos = strpos($parts[count($parts)-1], ".");
                $this->type = substr($parts[count($parts)-1], $dpos+ 1);
                $parts[count($parts)-1] = substr($parts[count($parts)-1], 0, $dpos);
            }
            $this->_parts = $parts;
	}

	/**
	 * Fetch module name according to URI
	 * @throws \iMVC\Exceptions\NotFoundException 
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
                $mc->modules[] = new entity(basename($module), realpath($module));;
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
                    # removing modules name from URI parts
                    array_shift($this->_parts);
                }
            }
	}
    
	protected function RetrieveNamespace()
        {
            # except default module every other module
            # has namespace with the module's name prefix
            $this->namespace = "";
            if($this->module->name != "default")
                $this->namespace = $this->module->name;
        }

	/**
	 * Fetch controller name according to URI
	 */
	protected function RetrieveControllerName()
	{
            # default controller
            $this->controller = new entity("index", "{$this->module->path}/controllers/indexController.php");
            # controller directory name
            $controller_dir = dirname($this->controller->path)."/";
            # foreach file in controller's directory
            foreach (array_diff(scandir($controller_dir), array(".", "..")) as $file)
            {
                # we are looking for files
                if(!is_file($controller_dir.$file)) continue;
                # we now processing a file
                if(strtolower($this->_parts[0]."controller.php") == strtolower($file))
                {
                    # updating target controller's info
                    $this->controller->name = $this->_parts[0];
                    $this->controller->path = dirname($this->controller->path)."/$file";
                    # we found target file
                    # checking for class declaration
                    require_once $this->controller->path;
                    $namespace = "{$this->namespace}\\controller";
                    if(!class_exists("$namespace\\{$this->controller->name}controller"))
                    {
                        # we don't have our class
                        throw new \iMVC\exceptions\notFoundException("The controller `{$this->controller->name}` does not exists");
                    }
                    array_shift($this->_parts);
                }
            }
	}

	/**
	 * Fetch action name according to URI
	 */
	protected function RetrieveActionName()
	{
            $this->action = new entity("index", "indexAction");
            $controller = "{$this->namespace}\\controller\\{$this->controller->name}controller";
            $co = new $controller;
            if(method_exists($co, "{$this->_parts[0]}Action"))
            {
                $this->action->name = $this->_parts[0];
                $this->action->path = "{$this->_parts[0]}Action";
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
            $this->view->name = $this->action->name;
            $this->view->path = "{$this->module->path}/views/view/{$this->controller->name}/{$this->action->name}View.pthml";
	}

	/**
	 * Fetch params according to URI
	 */
	protected function RetriveParams()
	{
	}

	/**
	 * Check if the current request has $_POST values
	 */
	public function IsPOST()
	{
	}

	/**
	 * check if it is a GET request or not
	 */
	public function IsGET()
	{
	}

}
?>