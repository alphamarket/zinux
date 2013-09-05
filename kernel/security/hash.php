<?php
namespace iMVC\kernel\security;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class hash
{
    /**
     * generate a unique to project hash content
     * 
     * @param string $content
     * @param boolean $regular_shuf if the ouput component string should shuffle [ note: the shuffle is const. based on the generated hash
     * @param boolean $long_version check if ouput should be in a long version or not
     * @return string Unique hash function
     */
    public static function Generate($content, $regular_shuf = 1, $long_version = 0)
    {
            $h = md5(sha1('@ha5Anbo0r'.md5($content).'dAr!u5h~#'));
            if($long_version)
                $h.=md5($content);
            $o = $h;
            if($regular_shuf || $long_version) 
            {
                $m=  strlen($content)/4;
                // $h = |A|B|C|D
                // $o = |D|B|A|C
                $o = "";
                $o .= substr($h, 3*$m, $m);
                $o .= substr($h, 1*$m, $m);
                $o .= substr($h, 0*$m, $m);
                $o .= substr($h, 2*$m, $m);
            }
            return $o;
    }

}
?>