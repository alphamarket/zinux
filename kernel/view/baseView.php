<?php
namespace iMVC\kernel\view;

require_once (dirname(__FILE__).'/../layout/baseLayout.php');
require_once (dirname(__FILE__).'/../helper/baseHelper.php');
require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:24:30
 */
class baseView extends \iMVC\baseiMVC
{

	/**
	 * Holds the current request instance
	 */
	public $request;
	/**
	 * is view flagged as rendered?
	 */
	protected $view_rendered;
	/**
	 * is view flagged as suppressed?
	 */
	protected $suppress_view;
	/**
	 * related layout instance
	 */
	protected $layout;
	/**
	 * a helper loader 
	 */
	public $helper;

	function __destruct()
	{
	}
    
        public function Initiate()
        {
            ;
        }
        public function Dispose()
        {
            parent::Dispose();
        }


	/**
	 * Construct a view instance according to passed request
	 */
	public function __construct()
	{
	}

	/**
	 * Set target view's name
	 * 
	 * @param view_name
	 */
	public function setView(string $view_name)
	{
	}

	/**
	 * Set or Unset view suppression value
	 * 
	 * @param should_suppressed
	 */
	public function suppressView($should_suppressed = 1)
	{
	}

	/**
	 * Check status of view suppression 
	 */
	public function IsViewSuppressed()
	{
	}

	/**
	 * Render a proper view
	 */
	public function Render()
	{
	}

	/**
	 * Get current view's name
	 */
	public function GetViewName()
	{
	}

	/**
	 * Get current view path
	 */
	public function GetViewPath()
	{
	}

	/**
	 * Partially loads a view
	 * 
	 * @param view_name
	 * @param partial_view_params
	 */
	public function RenderPartial(string $view_name, array $partial_view_params = array())
	{
	}

}