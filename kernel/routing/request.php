<?php
namespace iMVC\kernel\routing;

require_once ('..\..\baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class request extends baseiMVC
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
	}

	function __destruct()
	{
	}



	/**
	 * Set requested URI
	 */
	public function SetURI()
	{
	}

	/**
	 * Get requested URI
	 */
	public function GetURI()
	{
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
	}

	/**
	 * Depart and normalize the requested URI
	 */
	protected function DepartURI()
	{
	}

	/**
	 * Fetch module name according to URI
	 * @throws \iMVC\Exceptions\NotFoundException 
	 */
	protected function RetrieveModuleName()
	{
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