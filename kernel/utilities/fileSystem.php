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
    /**
     * resolve the path with insensitive behavioral pattern
     * @param string $path target path to resolve
     * @param boolean $convert_to_real_path should the function convert the ultimate to realpath?
     * @param boolean $cache_built_path should enable caching during the building path?
     * @return string|null is path exists the resolved path will be returned otherwise null
     */
    public static function resolve_path($path, $convert_to_real_path = 1, $cache_built_path = 0)
    {
        # check if string is valid
        if(!strlen($path)) return FALSE;
        # a primary check
        if(file_exists($path)) return $path;
        # open the cache file
        require_once ZINUX_ROOT.'kernel/caching/xCache.php';
        # we will use fileCache to cache
        # don't use xCache it will overload the session file
        $fc = new \zinux\kernel\caching\fileCache(__CLASS__);
        # create a base cache signiture
        $base_cache_sig = __FUNCTION__."@";
        # create the cache sig
        $cache_sig ="$base_cache_sig".strtolower($path);
        # check cache file and validate it
        if($fc->isCached($cache_sig) && file_exists($fc->fetch($cache_sig)))
        {
            # it was a HIT!
            return $fc->fetch($cache_sig);
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
        if($fc->isCached($this_cache_sig))
        {
            # fetch the file's directory
            $resolved_path = $fc->fetch($this_cache_sig);
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
                if($fc->isCached($this_cache_sig))
                {
                    $target_path = $fc->fetch($this_cache_sig);
                    if($target_path[strlen($target_path)-1]==DIRECTORY_SEPARATOR)
                        $target_path = strlen($target_path,0,  strlen($target_path)-1);
                }
                # check target path
                if(file_exists($target_path))
                {
                    if($cache_built_path)
                        $fc->save($this_cache_sig, $target_path);
                    $resolved_path = $target_path;
                    continue;
                }
                else
                    # delete any possible miss-formed cache data regarding to current path
                    $fc->delete($this_cache_sig);
                
                # a fail safe
                if(!is_readable($resolved_path)) return false;
                
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
                        $fc->save($this_cache_sig,  $target_path);
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
        $fc->save($cache_sig, $resolved_path);
        
        return $resolved_path;
    }
    /**
     * Recursive glob
     * @param string $pattern search pattern
     * @param int $flags GLOB_* flag
     * @param string $path path to search
     * @return array() search result
     */
    public static function rglob($pattern='*', $flags = 0, $path='')
    {
        $paths=  glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
        $files=  glob($path.$pattern, $flags);
        foreach ($paths as $path) { $files=array_merge($files,self::rglob($pattern, $flags, $path)); }
        return $files;
    }
}