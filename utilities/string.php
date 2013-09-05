<?php
namespace iMVC\utilities;


/**
 * Some handy string operation goes here
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:24
 */
class string
{
     /**
        * Check if $haystack starts with $needle
        * @param string $haystack
        * @param string $needle
        * @return boolean
        */
       public static function startsWith($haystack, $needle)
       {
           return !strncmp($haystack, $needle, strlen($needle));
       }
       /**
        * Check if $haystack ends with $needle
        * @param string $haystack
        * @param string $needle
        * @return boolean
        */
       public static function endsWith($haystack, $needle)
       {
           $length = strlen($needle);
           if ($length == 0) {
               return true;
           }

           return (substr($haystack, -$length) === $needle);
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

}
?>