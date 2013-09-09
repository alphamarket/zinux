<?php
namespace zinux\kernel\utilities;

require_once dirname(__FILE__).'/../../baseZinux.php';
/**
 * Description of fileSystem
 *
 * @author dariush
 */
class fileSystem extends \zinux\baseZinux
{
    /**
     * Get all stored all data that cached buy this class
     * @return array
     */
    public static function GetCaches()
    {
        $fc = new \zinux\kernel\caching\fileCache(__CLASS__);
        return $fc->fetchAll();
    }
    public static function resolve_path($path, $convert_to_real_path = 1)
    {
        # check if string is valid
        if(!strlen($path)) return FALSE;
        # a primary check
        if(file_exists($path)) return $path;
        # create a cache signiture
        $cache_sig = __METHOD__."@$path";
        # open the cache file
        require_once zinux_ROOT.'kernel/caching/xCache.php';
        $xc = new \zinux\kernel\caching\xCache(__CLASS__);
        # check cache file and validate it
        if($xc->isCached($cache_sig) && file_exists($xc->fetch($cache_sig)))
        {
            # it was a HIT!
            return $xc->fetch($cache_sig);
        }
        # if it is ab
        $is_absolute_path = ($path[0] == DIRECTORY_SEPARATOR);
        # depart the path
        # normalizing array's parts
        require_once '_array.php';
        $path_parts = _array::array_normalize(explode(DIRECTORY_SEPARATOR, strtolower($path)));
        # UNIX fs style
        $resolved_path = $is_absolute_path ? "" : ".";
        # WINNT fs style
        require_once 'string.php';
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
        if($convert_to_real_path)
            $resolved_path = realpath($resolved_path);
        
        # retrun the resolved path
        if(is_dir($resolved_path))
            $resolved_path .= DIRECTORY_SEPARATOR;
        
        # cache the result
        $xc->save($cache_sig, $resolved_path);
        
        return $resolved_path;
    }

    public function Initiate(){}
}