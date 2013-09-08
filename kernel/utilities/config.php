<?php
namespace zinux\kernel\utilities;

require_once dirname(__FILE__).'/../../baseZinux.php';

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:21
 */
class config extends \zinux\baseZinux
{
    public static $load_cache_sig;
    
    public function __construct($file_address)
    {
        if(!($file_address = fileSystem::resolve_path($file_address)))
            throw new \zinux\kernel\exceptions\notFoundException("The config file does not exists...");
        $this->file_address = $file_address;
    }
    /**
     * Fetch config file's values
     * @param string $file_address
     * @param string $process_sections
     * @param string $section_name
     * @return array fetched configs
     */
    public function Load($section_name = null, $process_sections = false)
    {
        self::$load_cache_sig = __METHOD__."@".$section_name.\zinux\kernel\security\hash::Generate($this->file_address.$process_sections);
         # open cache
        $xc = new \zinux\kernel\caching\xCache(__CLASS__);
        if($xc->isCached(self::$load_cache_sig))
        {
            $c = $xc->fetch(self::$load_cache_sig);
            if($c->modif_time==filemtime($this->file_address))
                goto __RETURN;
        }
        $c = new \stdClass();
        $c->config = iniParser::parse($this->file_address, $process_sections, $section_name);
        $c->modif_time = filemtime($this->file_address); 
        $xc->save(self::$load_cache_sig, $c);
__RETURN:
        return $c->config;
    }
    
    public static function GetConfig($path, $_)
    {
        $conf = self::GetAll();
        foreach(func_get_args() as $arg)
        {
            $conf = $conf[$arg];
        }
        return $conf;
    }
    
    public static function GetAll()
    {
        $xf = new \zinux\kernel\caching\xCache(__CLASS__);
        if(!$xf->isCached(self::$load_cache_sig))
            throw new \zinux\kernel\exceptions\invalideOperationException("The config file has not been loaded");
        $c = $xf->fetch(self::$load_cache_sig);
        return $c->config;
    }

    public function Initiate(){}
}
