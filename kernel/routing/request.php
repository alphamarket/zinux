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
	 */
	public $module;
	/**
	 * hold relative controller name with requested URI
	 */
	public $controller;
	/**
	 * hold relative action name with requested URI
	 */
	public $action;
	/**
	 * Holds correspond view's name
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
            $this->module = "default";
            $this->controller = "indexController";
            $this->action = "indexAction";
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
	{return;
            extract(array('root'=>'imvc', 'path'=>__METHOD__, 'name'=>'module'));
            require_once iMVC_ROOT.'kernel/caching/fileCache.php';
            $fc = new \iMVC\kernel\caching\fileCache(__CLASS__);
            if($fc->isCached(__METHOD__))
            {
                # catch maintaining optz. 
            }
            # all folders in ../modules folders considered a module folder
            $module_dir = iMVC_ROOT."../modules";
            # fail-safe for module dir existance
            if(!file_exists($module_dir))
                die("Couldn't find modules directory");
            
	}

	/**
	 * Fetch controller name according to URI
	 */
	protected function RetrieveControllerName()
	{
	}

	/**
	 * Fetch action name according to URI
	 */
	protected function RetrieveActionName()
	{
	}

	/**
	 * Fetch view name according to action's name
	 */
	protected function RetrieveViewName()
	{
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