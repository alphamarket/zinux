<?php
namespace zinux\kernel\application;

require_once (dirname(__FILE__).'/../../baseZinux.php');

/**
 * A http request handler
 * @author dariush
 * @version 1.0
 * @created 16-Jun-214 02:07:27
 */
class httpRequest extends \zinux\baseZinux
{
    protected $uri;
    protected $method;
    protected $GET = array();
    protected $POST = array();
    protected $COOKIE = array();
    /**
     * @param type $uri
     * @param string $method The supported methods are 
     * {OPTIONS|GET|HEAD|POST|PUT|DELETE|TRACE|CONNECT} according to HTTP/1.1 standard from the below link
     * @link http://www.ietf.org/rfc/rfc2616.txt The standard HTTP/1.1 document
     * @return \zinux\kernel\application\httpRequest Return $this
     */
    public function __construct($uri, $method = "GET")
    {
        # normalize the URI, if URI
        $this->__normalize_URI($uri);
        # normalize the method
        $method = strtoupper($method);
        # assign the URI
        $this->uri = $uri;
        # assign the method
        $this->method = $method;
        # validate the method
        switch($method) {
            case "OPTIONS":
            case "GET":
            case "HEAD":
            case "POST":
            case "PUT":
            case "DELETE":
            case "TRACE":
            case "CONNECT":
                break;
            default: throw new \InvalidArgumentException("The method `$method` is not supported");
        }
        # retrun $this instance
        return $this;
    }
    /**
     * set GET array of the request
     * @param array $GET
     * @return \zinux\kernel\application\httpRequest $this
     */
    public function setGET(array $GET) {
        $this->GET = $GET;
        return $this;
    }
    /**
     * set POST array of the request
     * @param array $POST
     * @return \zinux\kernel\application\httpRequest $this
     */
    public function setPOST(array $POST) {
        $this->POST = $POST;
        return $this;
    }
    /**
     * set COOKIE array of the request
     * @param array $COOKIE
     * @return \zinux\kernel\application\httpRequest $this
     */
    public function setCOOKIE(array $COOKIE) {
        $this->COOKIE = $COOKIE;
        return $this;
    }
    /**
     * Send the request
     * @param boolean $auto_echo Should we auto-echo the fetched contexts?
     * @return string The fetched contexts
     */
    public function send($auto_echo = 1)
    {
        # normaliz the params
        $this->__normalize_params();
        # prepare the request options
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                                    "Cookie: {$this->COOKIE}\r\n",
                'method'  => $this->method,
                'content' => $this->POST,
            ),
        );
        # close current session file
        session_write_close();
            # send/get the request
            $content = file_get_contents($this->uri, false, stream_context_create($options));
        # re-open the session file 
        session_start();
        # if there were any failure the content will be FALSE
        if(!$content)
            throw new \RuntimeException("Unable to fetch the request...");
        # if auto echo?
        if($auto_echo) echo $content;
        # otherwise
        else return $content;
    }
    /**
     * Prepares the params for sending requests
     */
    protected function __normalize_params()
    {
        if(!\zinux\kernel\utilities\string::Contains($this->uri, "?"))
            $this->uri .= "?";
        $get_str = http_build_query($this->GET);
        if(strlen($get_str))
            $this->uri .= "&$get_str";
        if(count($this->COOKIE))
            $this->COOKIE = str_replace("&", "; ", http_build_query($this->COOKIE));
        else $this->COOKIE = "";
        if(count($this->POST))
            $this->POST = http_build_query($this->POST);
        else $this->POST = "";
    }
    /**
     * Normalizes the $uri
     * @param string $uri(pased-by-ref.) The uri to normalize
     * @return string Also retruns the normalized uri
     * @throws \InvalidArgumentException If the uri is invalid
     */
    protected function __normalize_URI(&$uri) {
        # if the URI is not string or empty?
        if(!is_string($uri) || !strlen($uri))
            throw new \InvalidArgumentException("The URL should be string and cannot be empty.");
        # if this is an internal request?
        if($uri[0] === "/")
            # figure out the current server's name and protocol
            $uri = ("http".($_SERVER['SERVER_PORT']==443?"s":"")."://").$_SERVER['SERVER_NAME'].$uri;
        # any URL should start with a letter
        $uri = preg_replace("#^[^a-z]*#i", "", $uri);
        # if no schema provided by default choose HTTP schema
        if(!parse_url($uri, PHP_URL_SCHEME))
                $uri = "http://$uri";
        # final validation for URL
        if(!filter_var($uri, FILTER_VALIDATE_URL))
                throw new \InvalidArgumentException("The `<b>$uri</b>` is not a valid URL.");
        # also return the normalized uri
        return $uri;
    }
}