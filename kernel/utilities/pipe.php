<?php
namespace zinux\kernel\utilities;

require_once dirname(__FILE__).'/../../baseZinux.php';

/**
 * Simulates a pipe functionality using session
 */
class pipe extends \zinux\baseZinux
{
    /**
     * A session cache handler
     * @var \zinux\kernel\caching\sessionCache
     */
    protected $session_cache;
    /**
     * Construct a new pipe or Open an already existed pipe
     * @param string $pipe_name the pipe name
     */
    public function __construct($pipe_name = "default")
    {
        $this->session_cache = new \zinux\kernel\caching\sessionCache("pipe@".$pipe_name);
    }
    /**
     * Finalize the pipe line destruction
     */
    public function __destruct()
    {
        if(isset($this->session_cache) && $this->session_cache && !$this->hasFlow())
            $this->session_cache->deleteAll();
    }
    /**
     * Write some data into pipe
     * @param mixed $data
     */
    public function write($data, $expiration = 0)
    {
        $this->session_cache->save($this->session_cache->count() + 1, $data, $expiration);
    }
    /**
     * Read an item from pipe and remove it from pipe
     * @return mixed
     */
    public function read()
    {
        foreach($this->session_cache->fetchAll() as $key=> $value)
        {
            $expired = $this->session_cache->isExpired($key);                
            $this->session_cache->delete($key);
            if($expired)
                continue;
            return $value;
        }
        return NULL;
    }
    /**
     * Check if pipe contains any data
     * @return boolean TRUE if there are some data in pipe; otherwise FALSE
     */
    public function hasFlow()
    {
        return $this->count() > 0;
    }
    /**
     * Get the count of data exist in pipe
     * @return integer
     */
    public function count()
    {
        return $this->session_cache->count();
    }
    /**
     * Dispose the pipe and its data
     */
    public function Dispose()
    {
        $this->session_cache->deleteAll();
    }
}