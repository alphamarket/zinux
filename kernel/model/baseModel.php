<?php
require_once ('..\..\BaseiMVC.php');

namespace iMVC\kernel\model;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:07
 */
class baseModel extends BaseiMVC
{

	/**
	 * holds loaded model  
	 */
	public static $loaded_models_table;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * Loaded a model in a module
	 * 
	 * @param model_name
	 * @param module_name
	 */
	public static function LoadModel(string $model_name, string $module_name = NULL)
	{
	}

	/**
	 * Check if specified model is loaded or not
	 * 
	 * @param model_name
	 * @param module_name
	 */
	public static function IsModelLoaded(string $model_name, string  $module_name = NULL)
	{
	}

	/**
	 * Mark a model as loaded
	 * 
	 * @param model_name
	 * @param module_name
	 */
	protected function MarkAsLoaded(string $model_name, string $module_name = NULL)
	{
	}

}
?>