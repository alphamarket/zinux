<?php
namespace iMVC\kernel\controller;

require_once ('..\routing\request.php');
require_once ('..\..\baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
abstract class baseController extends baseiMVC
{

	/**
	 * Holds current layout handler's instance
	 */
	protected $layout;
	/**
	 * Holds current request instance
	 */
	protected $request;
	/**
	 * Holds current view handler's instance
	 */
	protected $view;

	function __construct()
	{
	}

	function __destruct()
	{
	}

	abstract public function IndexAction();

}
?>