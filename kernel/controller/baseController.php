<?php
namespace iMVC\kernel\controller;

require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
abstract class baseController extends \iMVC\baseiMVC
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
            $this->Dispose();
	}
   
        public function Initiate()
        {
            $this->layout =  new \iMVC\kernel\layout\baseLayout();
            $this->request = new \iMVC\kernel\routing\request();
            $this->view = new \iMVC\kernel\view\baseView();
        }
        public function Dispose()
        {
            $this->request->Dispose();
            $this->view->Dispose();
            $this->layout->Dispose();
            parent::Dispose();   
        }

	abstract public function IndexAction();

}
?>