<?php
namespace zinux\kernel\routing;

require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class dispatcher extends \zinux\baseZinux
{
        public function Initiate() {}
        
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
            // init controller
            $c->Initiate();
            // set request as a property in controller
            $c->request = $request;
            // set view object
            $c->view = new \zinux\kernel\view\baseView($request);
            // set layout object
            $c->layout = new \zinux\kernel\layout\baseLayout($c->view);
            $c->view->layout = $c->layout;
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