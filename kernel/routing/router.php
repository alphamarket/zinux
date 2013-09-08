<?php
namespace iMVC\kernel\routing;

require_once ('request.php');
require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class router extends \iMVC\baseiMVC
{
        public function Initiate()
        {
            ;
        }
	/**
	 * Route the passed request
	 * 
	 * @param request
	 */
	public function Run(request $request)
	{
            $request->Process();
            // pre-dispatcher
            $predisp = new preDispatcher();
            $predisp->Process($request);
            // dispatcher
            $disp = new dispatcher();
            $disp->Process($request);
            // post-dispatcher
            $postdisp = new postDispatcher();
            $postdisp->Process($request);  
	}

}
?>