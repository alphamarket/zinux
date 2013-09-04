<?php
namespace iMVC\kernel\helper;

require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:21
 */
class baseHelper extends \iMVC\baseiMVC
{

	/**
	 * holds loaded model  
	 */
	public static $loaded_helpers_table;

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
	 * Loaded a helper in a module
	 * 
	 * @param helper_name
	 * @param module_name
	 */
	public static function LoadHelper(string $helper_name, string $module_name = NULL)
	{
	}

	/**
	 * Check if specified helper is loaded or not
	 * 
	 * @param helper_name
	 * @param module_name
	 */
	public static function IsHelperLoaded(string $helper_name, string $module_name = NULL)
	{
	}

	/**
	 * Mark a helper as loaded
	 * 
	 * @param helper_name
	 * @param module_name
	 */
	protected function MarkAsLoaded(string $helper_name, string $module_name = NULL)
	{
	}

}
?>