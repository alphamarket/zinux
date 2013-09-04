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
	}

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
	 * Initiate the dispacther processes
	 * 
	 * @param request
	 */
	public function Process(request $request)
	{
	}

}
?>