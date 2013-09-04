<?php


namespace iMVC\kernel\security;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:10
 */
class secureCookie
{

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * Set a secure cookie
	 * 
	 * @param name
	 * @param value
	 * @param expire_from_now
	 * @param path
	 */
	public static function set(string $name, string $value, integer $expire_from_now, string $path = "/")
	{
	}

	/**
	 * Delete a cookie
	 * 
	 * @param name
	 */
	public function delete(string $name)
	{
	}

	/**
	 * Check if a cookie exists or not
	 * 
	 * @param name
	 */
	public function contains(string $name)
	{
	}

}
?>