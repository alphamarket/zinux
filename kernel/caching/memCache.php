<?php
namespace zinux\kernel\caching;
require_once 'cache.php';

class memCache
{
    const DEFAULT_PORT = 11211;
    
    const DEFAULT_HOST = "127.0.0.1";
    
    protected $_cachename;
    protected $_host;
    protected $_port;
    /**
     *
     * @var \Memcache
     */
    protected $mem_cache_instace = NULL;
    
    public function __construct($name = "default", $options = array())
    {
        $this->_cachename = $name;
        if (!class_exists('\Memcache')) {
            \trigger_error("Memcache is not enabled check <a href='http://www.php.net/manual/en/book.memcache.php' target='__blank'>this</a> for future information.", E_USER_ERROR);
        }
        if(!isset($options["host"]))
            $options["host"] = self::DEFAULT_HOST;
        if(!isset($options["port"]))
            $options["port"] = self::DEFAULT_PORT;
        $this->_host = $options["host"];
        $this->_port = $options["port"];
        $this->mem_cache_instace = new \Memcache;
    }
    protected function connect()
    {
        $this->mem_cache_instace->connect($this->_host, $this->_port);
    }
    protected function disconnect()
    {
        $this->mem_cache_instace->flush();
        $this->mem_cache_instace->close();
    }

    public function save($key, $data, $expiration = 0)
    {
        $this->connect();
        $this->mem_cache_instace->set($this->_cachename.$key, $data, $expiration);
        # check if any RACE CONDITION happened?
        if($this->mem_cache_instace->get($key) != $data)
        {
            $this->disconnect();
            # return failure
            return FALSE;
        }
        $this->disconnect();
        # return success
        return TRUE;
    }
    
    public function delete($key)
    {
        $this->connect();
        $this->mem_cache_instace->delete($key);
        $this->disconnect();
    }
    
    public function fetch($key)
    {
        $this->connect();
        $value = $this->mem_cache_instace->get($key);
        $this->disconnect();
        return $value;
    }
}

