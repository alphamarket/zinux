<?php


namespace iMVC\utilities;


/**
 * Allows for multi-dimensional ini files.  The native parse_ini_file() function
 * will convert the following ini file:...  [production] localhost.database.host =
 * 1.2.3.4 localhost.database.user = root localhost.database.password = abcdef
 * debug.enabled = false  [development : production] localhost.database.host =
 * localhost debug.enabled = true  ...into the following array:  array 'localhost.
 * database.host' => 'localhost' 'localhost.database.user' => 'root' 'localhost.
 * database.password' => 'abcdef' 'debug.enabled' => 1  This class allows you to
 * convert the specified ini file into a multi-dimensional array. In this case the
 * structure generated will be:  array 'localhost' => array 'database' => array
 * 'host' => 'localhost' 'user' => 'root' 'password' => 'abcdef' 'debug' => array
 * 'enabled' => 1  As you can also see you can have sections that extend other
 * sections (use ":" for that). The extendable section must be defined BEFORE the
 * extending section or otherwise you will get an exception.
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class iniParser
{

	/**
	 * Internal storage array
	 * @var array
	 */
	private static $_result = array();

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * Loads in the ini file specified in filename, and returns the settings in it as
	 * an associative multi-dimensional array
	 * @param string $filename          The filename of the ini file being parsed
	 * @param boolean $process_sections By setting the process_sections parameter to
	 * TRUE, you get a multidimensional array, with the section names and settings
	 * included. The default for process_sections is FALSE
	 * @param string $section_name      Specific section name to extract upon
	 * processing
	 * @return array|boolean
	 * 
	 * @param filename
	 * @param process_sections
	 * @param section_name
	 */
	public static function parse($filename, $process_sections = false, $section_name = null)
	{
	}

	/**
	 * Process contents of the specified section
	 * @param string $section Section name
	 * @param array $contents Section contents
	 * @return void
	 * 
	 * @param section
	 * @param contents
	 */
	private static function _processSection($section, array $contents)
	{
	}

	/**
	 * Process contents of a section
	 * @param array $contents Section contents
	 * @return array
	 * 
	 * @param contents
	 */
	private static function _processSectionContents(array $contents)
	{
	}

	/**
	 * Convert a.b.c.d paths to multi-dimensional arrays
	 * @param string $path Current ini file's line's key
	 * @param mixed $value Current ini file's line's value
	 * @return array
	 * 
	 * @param path
	 * @param value
	 */
	private static function _processContentEntry($path, $value)
	{
	}

	/**
	 * Merge two arrays recursively overwriting the keys in the first array if such
	 * key already exists
	 * @param mixed $a Left array to merge right array into
	 * @param mixed $b Right array to merge over the left array
	 * @return mixed
	 * 
	 * @param a
	 * @param b
	 */
	private static function _arrayMergeRecursive($a, $b)
	{
	}

}
?>