<?php
namespace iMVC\Routing;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InternalRequest
 *
 * @author dariush
 */
class FakeRequest extends \iMVC\BaseMVC
{
    /**
     * Hold a space for backing up $_POST
     * @var array
     */
    protected $_POST_BACKUP;
    /**
     * Hold a space for backing up $_GET
     * @var array
     */
    protected $_GET_BACKUP;
    /**
     * Hold a space for backing up $GLOBAL
     * @var array
     */
    protected $_GLOBAL_BACKUP;
    /**
     * Hold a space for backing up $GLOBAL
     * @var array
     */
    protected $_FILES_BACKUP;
    /**
     * Hold a space for backing up $_REQUEST
     * @var array
     */
    protected $_REQUEST_BACKUP;
    /**
     * Hold a space for backing up parent::GetRequest()
     * @var \iMVC\Routing\Request
     */
    protected $_REQUEST_REQUEST;
    /**
     * Hold a space for fake $_GET
     * @var array
     */
    protected $GET;
    /**
     * Hold a space for fake $_POST
     * @var array
     */
    protected $POST;
    /**
     * Hold a space for fake URI
     * @var array
     */
    protected $URI;


    public function Initiate()
    {
        ;
    }
    public function __construct($uri, array $GET = NULL, array $POST = NULL)
    {
        if(!isset($GET))
            $GET = array();
        if(!isset($POST))
            $POST = array();
        if(!\iMVC\Tools\String::startsWith($uri, "/") && false)
            throw new \iMVC\Exceptions\InvalideOperationException("'$uri' cannot be a internal request.");
        $this->URI = $uri;
        $this->GET = $GET;
        $this->POST= $POST;
    }
    protected function BackupGlobals()
    {
        $this->_POST_BACKUP = $_POST;
        $this->_GET_BACKUP = $_GET;
        $this->_GLOBAL_BACKUP = $GLOBALS;
        $this->_FILES_BACKUP = $_FILES;
        $this->_REQUEST_BACKUP = $_REQUEST;
        $this->_REQUEST_REQUEST = parent::GetRequest();
    }
    protected function RestorGlobals()
    {
        $_POST = $this->_POST_BACKUP;
        $_GET = $this->_GET_BACKUP;
        $GLOBALS = $this->_GLOBAL_BACKUP;
        $_FILES = $this->_FILES_BACKUP;
        $_REQUEST = $this->_REQUEST_BACKUP;
        parent::SetRequest($this->_REQUEST_REQUEST);
    }
    /**
     * @return string the request result
     */
    public function Send()
    {
        ob_start();
        try
        {
            $this->BackupGlobals();
            $this->SetGlobals();
            $this->SecurityCheck();
            $this->RunInternalRequest();
            $this->RestorGlobals();
        }
        catch(\Exception $e)
        {
            ?>
<div class="" style="color:darkred">
    <strong>Sending fake request failed : </strong><br />
    <?php echo $e->getMessage(); ?>
</div>
            <?php
        }
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    protected function RunInternalRequest()
    {
        $app = new \iMVC\APP\Application();
        
        $app->Startup('../Config/config.ini');
        
        $app->Run();
        
        $app->Shutdown();
    }
    protected function SetGlobals()
    {
        $_POST = $this->POST;
        $_GET = $this->GET;
        $_REQUEST = array_merge($_GET, $_POST);
        $_SERVER['REQUEST_URI'] = $this->URI;
    }
    protected function SecurityCheck()
    {
        /**
         * check for recursive calling
         */
        $request = new Request();
        $cr = parent::GetRequest();
        if($request->module==$cr->module)
        {
            if($request->controller==$cr->controller)
            {
                if($request->action == $cr->action)
                {
                    throw new \iMVC\Exceptions\InvalideOperationException("Cannot recursivly call current action.");
                }
            }
        }
    }
}