<?php
namespace iMVC\kernel\routing;

require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:13:38
 */
class httpRequest extends \iMVC\baseiMVC
{

	protected $_backedup_vars;

	/**
        * construct a new fake request
        * 
        * @param uri
        * @param GET
        * @param POST
        */
	function __construct($uri, $method = "GET",array $GET = array(), array $POST = array(), array $COOKIE = array())
	{
            $this->uri = $uri;
            $this->method = $method;
            $this->GET = $GET;
            $this->POST = $POST;
            $this->COOKIE = $COOKIE;
            $method = strtoupper($method);
            switch($method)
            {
                case 'GET':
                case 'POST':
                    break;
                default:
                    throw new \InvalidArgumentException("The method `$method` is not supported");
            }
	}
        public function Initiate()
        {
            ;
        }
	
	/**
	 * send the fake request
	 * 
	 * @param auto_echo
	 * @param throw_exception
	 */
	public function send($auto_echo = 1, $throw_exception = 0)
	{
            ob_start();
            try
            {
                $this->setVars();;
                $this->innerSend();
            }
            catch(\Exception $e)
            {
                if($throw_exception)
                    throw $e;
                ?>
    <div class="" style="color:darkred">
        <strong>Sending request failed : </strong><br />
        <?php echo $e->getMessage(); ?>
    </div>
                <?php
            }
            $content = ob_get_contents();
            ob_end_clean();
            if($auto_echo) echo $content;
            return $content;
	}
	
	/**
	 * set global vars for the fake request
	 */
	protected function setVars()
	{
            if(\iMVC\kernel\utilities\string::startsWith($this->uri, "/"))
            {
                # figure out the server protocol
                $prot = "http".($_SERVER['SERVER_PORT']==443?"s":"")."://";
                $this->uri = $prot.$_SERVER['SERVER_NAME'].$this->uri;
            }
            if(!\iMVC\kernel\utilities\string::Contains($this->uri, "?"))
                $this->uri .= "?";
            $this->uri .= "&".http_build_query($this->GET);
            $this->COOKIE = str_replace("&", "; ", http_build_query($this->COOKIE));
            $this->POST = http_build_query($this->POST);
	}

	/**
	 * An inner send procedure
	 */
	protected function innerSend()
	{
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
                                        "Cookie: {$this->COOKIE}\r\n",
                    'method'  => $this->method,
                    'content' => $this->POST,
                ),
            );
            $c = file_get_contents($this->uri, false, stream_context_create($options));
            if(!$c)
                throw new \Exception("Unable to fetch the request...");
            echo $c;
	}
}