<?php
namespace zinux\kernel\controller;

require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:20
 */
abstract class baseController extends \zinux\baseZinux
{

	/**
        * Holds current layout handler's instance
        *
        * @var \zinux\kernel\layout\baseLayout
        */
	public $layout;
	/**
        * Holds current request instance
        *
        * @var \zinux\kernel\routing\request
        */
	public $request;
	/**
        * Holds current view handler's instance
        *
        * @var \zinux\kernel\view\baseView
        */
	public $view;
        /**
         * Dispose the controller
         */
        public function Dispose()
        {
            $this->view->Dispose();
            $this->layout->Dispose();
            parent::Dispose();   
        }
        /**
         * The indexAction abstract
         */
	abstract public function IndexAction();
}
?>