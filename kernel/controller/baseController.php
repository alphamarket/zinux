<?php
require_once ('..\routing\request.php');
require_once ('..\..\BaseiMVC.php');

namespace iMVC\kernel\controller;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:06
 */
abstract class baseController extends BaseiMVC
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



	abstract public function IndexAction()
	{
	}

}
?>