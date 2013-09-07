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
            $request->Process();
            // init this
            $this->Initiate();
            // create new controller
            $c = $request->controller->GetInstance();
            // set request as a property in controller
            $c->request = $request;
            // set view object
            $c->view = new \iMVC\kernel\view\baseView($request);
            // set layout object
            $c->layout = new \iMVC\kernel\layout\baseLayout($c->view);
            $c->view->layout = $c->layout;
            // init controller
            $c->Initiate();
            // call the action method
            $c->request->action->InvokeAction($c);
            // render : layout ~> view
            $c->layout->Render();
            // dispose controller
            $c->Dispose();
            // dispose this
            $this->Dispose();
	}

}
?>