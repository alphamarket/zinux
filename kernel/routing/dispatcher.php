<?php
namespace iMVC\kernel\routing;

require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class dispatcher extends \iMVC\baseiMVC
{

	function __construct()
	{
            $this->Initiate();
	}

	function __destruct()
	{
	}

        public function Initiate() {}
        
        public function Dispose()
        {
            parent::Dispose();
        }
        
	/**
	 * Initiate the dispacther processes
	 * 
	 * @param request
	 */
	public function Process(request $request)
	{
            // init this
            $this->Initiate();
            // create new controller
            $c = new $request->controller;
            // set request as a property in controller
            $c->request = $request;
            // set view object
            $c->view = new \iMVC\View\BaseView($request);
            // set layout object
            $c->layout = new \iMVC\Layout\BaseLayout($c->view);
            // init controller
            $c->Initiate();
            // call the action method
            $c->{$request->action}();
            // render : layout ~> view
            $c->layout->Render();
            // dispose controller
            $c->Dispose();
            // dispose this
            $this->Dispose();
	}

}
?>