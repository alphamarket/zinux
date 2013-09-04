<?php
namespace iMVC\kernel\security;

require_once ('..\..\baseiMVC.php');


	/**
	 * This will provide operations on security in request with will provide the
	 * secure GET/POST in target destination
	 * @author dariush
	 * @version 1.0
	 * @updated 04-Sep-2013 17:23:16
	 */
class security extends baseiMVC
{

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * Get a secure get string compatible with POST/GET queries
	 * 
	 * @param based_upon    an array to create secure string based upon it
	 */
	public static function GetSecureString(array $based_upon = array())
	{
	}

	/**
	 * Checks if URI is secure with provided parameters
	 * 
	 * @param target_array    Target array to examine parameters
	 * @param existance_array    array to check for item existance in target array
	 * @param check_opt_array    do a operation like ` $key($value) `  foreach item in
	 * this array!
	 * @param check_sum_array    do a checksum on elements like ` $key == $value `
	 * @param throw_exception    Throw exception if any error occures while processing
	 * $target_array
	 * @param verbose_exception    check if when throwing exceptions the message
	 * should be verbose or not
	 */
	public static function IsSecure(array $target_array, array $existance_array = array(), array $check_opt_array = array(), array $check_sum_array = array(), $throw_exception = 1, $verbose_exception = 0)
	{
	}

	/**
	 * check if passed string is secured compatible operations in `GetSecureString()`
	 * function
	 * 
	 * @param target_string    target value to examine
	 * @param based_upon    an array to create secure string based upon it
	 */
	public static function IsStringSecured(string $target_string, array $based_upon = array())
	{
	}

}
?>