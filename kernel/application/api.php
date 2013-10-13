<?php
namespace zinux\kernel\application;

require_once (dirname(__FILE__).'/../../baseZinux.php');

/**
 * A zinux API handler
 */
class api extends \zinux\baseZinux
{
    /**
     * Initiate an api call
     * @param string $uri target api to call
     * @param array $GET GET array to send to target api 
     * @param array $POST POST array to send to target api 
     * @return boolean returns true if api-call was success otherwise an exception will be thrown
     */
    public static function call($uri, array $GET = array(), array $POST = array())
    {
        # verify the uri arg
        if(!$uri || !is_string($uri) || !strlen($uri))
            throw new \zinux\kernel\exceptions\invalideArgumentException("Invalid \$uri supplied....");
        if(filter_var($uri, FILTER_VALIDATE_URL))
            throw new \zinux\kernel\exceptions\invalideOperationException("The '$uri' cannot be a direct link!");
        # fail safe for GET arg
        if(!$GET) $GET = array();
        # fail safe for POST arg
        if(!$POST) $POST = array();
        # parse the api uri
        $_uri = parse_url($uri);
        # if any inline GET exists in uri
        if(isset($_uri['query']))
            # fetch and convert to an array
            parse_str($_uri['query'], $_uri);
        else
            # otherwise no inline GET arg
            $_uri = array();
        # back up the following global varibales
        #   $_GET
        #   $_POST
        #   $_SERVER
        #   $GLOBALS
        $get = $_GET;
        $post = $_POST;
        $server = $_SERVER;
        $globals = $GLOBALS;
        # fail safe for module directory definition
        if(!defined("MODULE_ROOT"))
            die("MODULE_ROOT is not defined");
        /**
         * In this section we will assure that we don't get  caught-up with recursive calls
         */
        # generate a request instance for currently requested uri
        $current_req = new \zinux\kernel\routing\request;
        # process the current request 
        $current_req->Process();
        # setup the target api-call uri
        $_SERVER['REQUEST_URI'] = preg_replace("#(.*)(\?.*)$#i", "$1", $uri)."?".http_build_query($_GET);
        # generate a request instance for requested api-call uri
        $api_req = new \zinux\kernel\routing\request;
        # process the api-call request 
        $api_req->Process();
        # make sure we don't endup the same action that $current_req points to it
        if($current_req->module->full_name == $api_req->module->full_name)
            if($current_req->controller->full_name == $api_req->controller->full_name)
                if($current_req->action->full_name == $api_req->action->full_name)
                    throw new \zinux\kernel\exceptions\invalideOperationException("Cannot do a recursive api-call to current action!");
        /**
         * End of recursive api-call fail safe
         */
        # at this point we have everything good to go 
        # modify $_GET array for api call
        # it also appends an 'api_call' flag to target api-call's GET arg
        # to indicate an api_call happened!
        $_GET = array_merge($_uri, $GET, array("api_call" => 1));
        # modify $_POST array for api call
        $_POST = $POST;
        # create an application with given module directory
        $app = new \zinux\kernel\application\application(MODULE_ROOT);
        # process the application instance
        $app 
                # init the application's optz.
                ->Startup()
                # run the application 
                ->Run()
                # shutdown the application
                ->Shutdown();

        # restore the following global varibales to their origin values
        #   $_GET
        #   $_POST
        #   $_SERVER
        #   $GLOBALS
        $_GET = $get;
        $_POST = $post;
        $_SERVER = $server;
        $GLOBALS = $globals;
        # at this point api-call was a success
        return true;
    }
}