<?php
namespace zinux\kernel\utilities;

/**
* Allows for multi-dimensional ini files.
*
* The native parse_ini_file() function will convert the following ini file:...
*
* [production]
* localhost.database.host = 1.2.3.4
* localhost.database.user = root
* localhost.database.password = abcdef
* debug.enabled = false
*
* [development : production]
* localhost.database.host = localhost
* debug.enabled = true
*
* ...into the following array:
*
* array
*   'localhost.database.host' => 'localhost'
*   'localhost.database.user' => 'root'
*   'localhost.database.password' => 'abcdef'
*   'debug.enabled' => 1
*
* This class allows you to convert the specified ini file into a multi-dimensional
* array. In this case the structure generated will be:
*
* array
*   'localhost' =>
*     array
*       'database' =>
*         array
*           'host' => 'localhost'
*           'user' => 'root'
*           'password' => 'abcdef'
*   'debug' =>
*     array
*       'enabled' => 1
*
* As you can also see you can have sections that extend other sections (use ":" for that).
* The extendable section must be defined BEFORE the extending section or otherwise
* you will get an exception.
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class iniParser extends \zinux\kernel\application\baseConfigLoader
{
    /**
     * Internal storage array
     * @var array
     */
    private static $_result = array();

    /**
     * Loads in the ini file specified in filename, and returns the settings in it as
     * an associative multi-dimensional array
     * @param string $filename          The filename of the ini file being parsed
     * @param boolean $section_name By setting the process_sections parameter to
     * TRUE, you get a multidimensional array, with the section names and settings
     * included. The default for process_sections is FALSE
     * @param string $process_sections      Specific section name to extract upon
     * processing
     * @return array|boolean
     * 
     * @param filename
     * @param process_sections
     * @param section_name
     */
    public static function parse($filename, $section_name = NULL, $process_sections = true)
    {
            // load the raw ini file
            $ini = parse_ini_file($filename, $section_name);
            
            // fail if there was an error while processing the specified ini file
            if ($ini === false) {
                return false;
            }

            // reset the result array
            self::$_result = array();

            if ($process_sections) {
                // loop through each section
                foreach ($ini as $section => $contents) {
                    // process sections contents
                    self::_processSection($section, $contents);
                }
            } else {
                // treat the whole ini file as a single section
                self::$_result = self::_processSectionContents($ini);
            }
            //  extract the required section if required
            if ($process_sections) {
                if ($section_name) {
                    // return the specified section contents if it exists
                    if (isset(self::$_result[$section_name])) {
                        return self::$_result[$section_name];
                    } else {
                        return array();
                    }
                }
            }

            // if no specific section is required, just return the whole result
            return self::$_result;
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
            // the section does not extend another section
            if (stripos($section, ':') === false) {
                self::$_result[$section] = self::_processSectionContents($contents);

            // section extends another section
            } else {
                // extract section names
                list($ext_target, $ext_source) = explode(':', $section);
                $ext_target = trim($ext_target);
                $ext_source = trim($ext_source);

                // check if the extended section exists
                if (!isset(self::$_result[$ext_source])) {
                    throw new Exception('Unable to extend section ' . $ext_source . ', section not found');
                }

                // process section contents
                self::$_result[$ext_target] = self::_processSectionContents($contents);

                // merge the new section with the existing section values
                self::$_result[$ext_target] = self::_arrayMergeRecursive(self::$_result[$ext_source], self::$_result[$ext_target]);
            }
    }

        /**
            * Process contents of a section
            *
            * @param array $contents Section contents
            * @return array
            */
        private static function _processSectionContents(array $contents)
        {
            $result = array();

            // loop through each line and convert it to an array
            foreach ($contents as $path => $value) {
                // convert all a.b.c.d to multi-dimensional arrays
                $process = self::_processContentEntry($path, $value);

                // merge the current line with all previous ones
                $result = self::_arrayMergeRecursive($result, $process);
            }

            return $result;
        }


        /**
            * Convert a.b.c.d paths to multi-dimensional arrays
            *
            * @param string $path Current ini file's line's key
            * @param mixed $value Current ini file's line's value
            * @return array
            */
        private static function _processContentEntry($path, $value)
        {
            $pos = strpos($path, '.');

            if ($pos === false) {
                return array($path => $value);
            }

            $key = substr($path, 0, $pos);
            $path = substr($path, $pos + 1);

            $result = array(
                $key => self::_processContentEntry($path, $value),
            );

            return $result;
        }


        /**
            * Merge two arrays recursively overwriting the keys in the first array
            * if such key already exists
            *
            * @param mixed $a Left array to merge right array into
            * @param mixed $b Right array to merge over the left array
            * @return mixed
            */
        private static function _arrayMergeRecursive($a, $b)
        {
            // merge arrays if both variables are arrays
            if (is_array($a) && is_array($b)) {
                // loop through each right array's entry and merge it into $a
                foreach ($b as $key => $value) {
                    if (isset($a[$key])) {
                        $a[$key] = self::_arrayMergeRecursive($a[$key], $value);
                    } else {
                        if($key === 0) {
                            $a= array(0 => self::_arrayMergeRecursive($a, $value));
                        } else {
                            $a[$key] = $value;
                        }
                    }
                }
            } else {
                // one of values is not an array
                $a = $b;
            }

            return $a;
        }

    public function Initiate(){}

    public function Execute()
    {
        return $this->parse($this->file_address, $this->section_name, $this->process_sections);
    }

    /**
     * @param string $config_file_address config file address
     * @param string $section_name section_name in ini file
     * @throws \zinux\kernel\exceptions\invalidArgumentException
     */
    public function __construct($config_file_address, $section_name = NULL)
    {
        $this->file_address  = \zinux\kernel\utilities\fileSystem::resolve_path($config_file_address);

        if(!$this->file_address)
            throw new \zinux\kernel\exceptions\invalidArgumentException("config file not found at '$config_file_address'");

        $this->process_sections = isset($section_name);

        $this->section_name = $section_name;
    }
}