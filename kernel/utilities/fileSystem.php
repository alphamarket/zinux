<?php
namespace zinux\kernel\utilities;

require_once dirname(__FILE__).'/../../baseZinux.php';
require_once '_array.php';
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
        # open the cache file
        require_once ZINUX_ROOT.'kernel/caching/xCache.php';
        # we will use xCache to cache
        $xc = new \zinux\kernel\caching\xCache(__CLASS__);
        # create a base cache signiture
        $base_cache_sig = __FUNCTION__."@";
        # create the cache sig
        $cache_sig ="$base_cache_sig".strtolower($path);
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
        # a fail safe 
        if(!count($path_parts)) return false;
        # UNIX fs style
        $resolved_path = $is_absolute_path ? "" : ".";
        # WINNT fs style
        require_once 'string.php';
        if(string::Contains($path_parts[0], ":"))
        {
            $is_absolute_path = 1;
            $resolved_path = $is_absolute_path ? "{$path_parts[0]}:" : ".".DIRECTORY_SEPARATOR;
        }
        # normalize the array
        $depart = _array::array_normalize(explode(DIRECTORY_SEPARATOR, $path));
        # fetch the target file's name
        $file = $depart[count($depart)-1];
        # unset the file's name
        unset($depart[count($depart)-1]);
        # create a cache sig
        $this_cache_sig = $base_cache_sig.strtolower(DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $depart));
        # check for cache for files directory
        if($xc->isCached($this_cache_sig))
        {
            # fetch the file's directory
            $resolved_path = $xc->fetch($this_cache_sig);
            # through a hunch for file's address
            $hunch = $resolved_path.DIRECTORY_SEPARATOR.$file;
            if(file_exists($hunch))
            {
                $resolved_path = $hunch;
                goto __RETURN;
            }
            # update the directory which bellow FOREACH will search under
            $path_parts = array($path_parts[count($path_parts)-1]);
        }
        # do a BFS in subdirz
        foreach ($path_parts as $part)
        {
            if (!empty($part))
            {
                $target_path = $resolved_path.DIRECTORY_SEPARATOR.$part;
                # create cache sig for this path
                $this_cache_sig = $base_cache_sig.strtolower($target_path);
                # check for fore head cache existance
                if($xc->isCached($this_cache_sig))
                {
                    $target_path = $xc->fetch($this_cache_sig);
                    if($target_path[strlen($target_path)-1]==DIRECTORY_SEPARATOR)
                        $target_path = strlen($target_path,0,  strlen($target_path)-1);
                }
                # check target path
                if(file_exists($target_path))
                {
                    $xc->save($this_cache_sig, $target_path);
                    $resolved_path = $target_path;
                    continue;
                }
                else
                    # delete any possible miss-formed cache data regarding to current path
                    $xc->delete($this_cache_sig);
                
                $files = scandir($resolved_path);

                $match_found = FALSE;
                
                foreach ($files as $file)
                {	
                    if (strtolower($file) == strtolower($part))
                    {
                        # flag found
                        $match_found = TRUE;
                        # update target path
                        $target_path = $resolved_path.DIRECTORY_SEPARATOR.$file;
                        # update cache sig for this file
                        $this_cache_sig = $base_cache_sig.strtolower($resolved_path.DIRECTORY_SEPARATOR.$part);
                        # cache the path
                        $xc->save($this_cache_sig,  $target_path);
                        # update resolved path
                        $resolved_path = $target_path;
                        # goto for next file iter.
                        break;
                    }
                }
                if (!$match_found)
                {
                    return FALSE;
                }
            }
        }
__RETURN:
        if($convert_to_real_path)
            $resolved_path = realpath($resolved_path);
        
        # cache the result
        $xc->save($cache_sig, $resolved_path);
        
        return $resolved_path;
    }

    public function Initiate(){}
}