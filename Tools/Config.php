<?php
namespace iMVC\Tools;

/**
 * Configurations handler
 *
 * @author dariush
 */
require_once 'BaseMVC.php';
require_once 'Ini_Parser.php';
class Config extends \iMVC\BaseMVC 
{
    public function Initiate() 
    {
    }
    /**
     * Fetch config file's values
     * @param string $file_address
     * @param string $process_sections
     * @param string $section_name
     * @return array fetched configs
     */
    public function Load($file_address, $process_sections = false, $section_name = null)
    {
        $GLOBALS[CONFIGS] = \iMVC\Tools\Ini_Parser::parse($file_address, $process_sections, $section_name);
        return $GLOBALS[CONFIGS];
    }
}