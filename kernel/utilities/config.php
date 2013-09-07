<?php
namespace iMVC\kernel\utilities;

require_once dirname(__FILE__).'/../../baseiMVC.php';

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:21
 */
class config extends \iMVC\baseiMVC
{
    public static $load_cache_sig;
    /**
     * Fetch config file's values
     * @param string $file_address
     * @param string $process_sections
     * @param string $section_name
     * @return array fetched configs
     */
    public function Load($file_address, $process_sections = false, $section_name = null)
    {
        if(!($file_address = fileSystem::resolve_path($file_address)))
            throw new \iMVC\exceptions\notFoundException("The config file does not exists...");
        self::$load_cache_sig = __METHOD__."@".\iMVC\kernel\security\hash::Generate($file_address.$process_sections.$section_name);
         # open cache
        $xc = new \iMVC\kernel\caching\xCache(__CLASS__);
        if($xc->isCached(self::$load_cache_sig))
        {
            $c = $xc->retrieve(self::$load_cache_sig);
            if($c->modif_time==filemtime($file_address))
                goto __RETURN;
        }
        $c = new \stdClass();
        $c->config = iniParser::parse($file_address, $process_sections, $section_name);
        $c->modif_time = filemtime($file_address); 
        $xc->store(self::$load_cache_sig, $c);
__RETURN:
        return $c->config;
    }
    
    public static function GetConfig($path, $_)
    {
        $conf = $this->GetAll();
        foreach(func_get_args() as $arg)
        {
            $conf = $conf[$arg];
        }
        return $conf;
    }
    
    public static function GetAll()
    {
        $xf = new \iMVC\kernel\caching\xCache(__CLASS__);
        if(!$xf->isCached(self::$load_cache_sig))
            throw new \iMVC\exceptions\invalideOperationException("The config file has not been loaded");
        return $xf->retrieve(self::$load_cache_sig);
    }

    public function Initiate(){}
}
