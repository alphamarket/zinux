<?php
namespace iMVC\kernel\security;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:21:13
 */
class caching
{
        /**
        * Set a cach
        * 
        * @param type $root
        * @param type $path
        * @param type $name
        * @param string $value
        */
	public static function set($root = 'root', $path = "global", $name = "no-name",  $value = "")
	{
            $_SESSION['cach'][$root][$path][$name] = serialize($value);
	}
        /**
         * Get a cach
         * @param type $root
         * @param type $path
         * @param type $name
         * @return null
         */
        public static function get($root = 'root', $path = "global", $name = "no-name")
        {
            if(!self::contains($root, $path, $name)) return NULL;
            return unserialize($_SESSION['cach'][$root][$path][$name]);
        }

	/**
	 * Delete a cach
	 * 
	 * @param name
	 */
	public static function delete($root = 'root', $path = "global", $name = "no-name")
	{
            unset($_SESSION['cach'][$root][$path][$name]);
	}

	/**
	 * Check if a cach exists or not
	 * 
	 * @param name
	 */
	public static function contains($root = 'root', $path = "global", $name = "no-name")
	{
            return isset($_SESSION['cach'][$root][$path][$name]);
	}

}