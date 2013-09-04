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
	 * Route the passed request
	 * 
	 * @param request
	 */
	public function Run(request $request)
	{
	}

}
?>