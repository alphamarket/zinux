<?php
namespace zinux\kernel\utilities;

require_once dirname(__FILE__).'/../../baseZinux.php';

/**
 * Some handy string operation goes here
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class string extends \zinux\baseZinux
{
     /**
        * Check if $haystack starts with $needle
        * @param string $haystack
        * @param string $needle
        * @return boolean
        */
       public static function startsWith($haystack, $needle)
       {
            return substr($haystack, 0, strlen($needle)) == $needle;
       }
       /**
        * Check if $haystack ends with $needle
        * @param string $haystack
        * @param string $needle
        * @return boolean
        */
       public static function endsWith($haystack, $needle, $case_sensitive = 1)
       {
            if(!$case_sensitive)
            {
                $haystack = strtolower($haystack);
                $needle = strtolower($needle);
            }
            return substr($haystack, strlen($haystack) - strlen($needle)) == $needle;
       }
       /**
        * Check if $haystack contains $needle
        * @param string $haystack
        * @param string $needle
        * @return boolean
        */
       public static function Contains($haystack, $needle)
       {
           return (strpos($haystack, $needle) !== false);
       }

    public function Initiate(){}  
}