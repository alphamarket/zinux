<?php
namespace iMVC\kernel\utilities;

require_once dirname(__FILE__).'/../../baseiMVC.php';
/**
 * Description of fileSystem
 *
 * @author dariush
 */
class fileSystem extends \iMVC\baseiMVC
{
    public static function resolve_path($path)
    {
        # check if string is valid
        if(!strlen($path)) return FALSE;
        # a primary check
        if(file_exists($path)) return $path;
        # create a cache signiture
        $cache_sig = __METHOD__."@$path";
        # open the cache file
        $fc = new \iMVC\kernel\caching\fileCache(__CLASS__);
        # check cache file and validate it
        if($fc->isCached($cache_sig) && file_exists($fc->retrieve($cache_sig)))
        {
            # it was a HIT!
            return $fc->retrieve($cache_sig);
        }
        # if it is ab
        $is_absolute_path = ($path[0] == DIRECTORY_SEPARATOR);
        # depart the path
        $path_parts = array_filter(explode(DIRECTORY_SEPARATOR, strtolower($path)));
        # normalizing array's parts
        $path_parts = count($path_parts)? array_chunk($path_parts, count($path_parts)) : array();
        $path_parts = count($path_parts[0])?$path_parts[0]:array();
        # UNIX fs style
        $resolved_path = $is_absolute_path ? "" : ".";
        # WINNT fs style
        if(string::Contains($path_parts[0], ":"))
        {
            $is_absolute_path = 1;
            $resolved_path = $is_absolute_path ? "" : ".".DIRECTORY_SEPARATOR;
        }
        # do a BFS in subdirz
        foreach ($path_parts as $part)
        {
            if (!empty($part))
            {
                $target_path = $resolved_path.DIRECTORY_SEPARATOR.$part;
                if(file_exists($target_path))
                {
                    $resolved_path = $target_path;
                    continue;
                }
                $files = scandir($resolved_path);

                $match_found = FALSE;

                foreach ($files as $file)
                {	
                    if (strtolower($file) == $part)
                    {
                        $match_found = TRUE;
                        $resolved_path = $resolved_path.DIRECTORY_SEPARATOR.$file;
                        break;
                    }
                }
                if (!$match_found)
                {
                    return FALSE;
                }
            }
        }
        # cache the result
        $fc->store($cache_sig, $resolved_path);
        # retrun the resolved path
        return $resolved_path;
    }

    public function Initiate(){}
}