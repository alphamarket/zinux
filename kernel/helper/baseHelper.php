<?php


namespace iMVC\kernel\helper;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:06
 */
class baseHelper
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