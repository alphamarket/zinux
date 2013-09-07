<?php
namespace iMVC\kernel\view;

require_once (dirname(__FILE__).'/../../baseiMVC.php');

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:24:30
 */
class baseView extends \iMVC\baseiMVC
{

	/**
	 * Holds the current request instance 
         * @var \iMVC\kernel\routing\request
         */
	public $request;
        /**
         * holds view's name
         * @var string
         */
	public $view_name;
	/**
	 * is view flagged as rendered?
	 */
	protected $view_rendered = 0;
	/**
	 * is view flagged as suppressed?
	 */
	protected $suppress_view = 0;
	/**
	 * related layout instance
	 */
	public $layout;
	/**
	 * a helper loader 
	 */
	public $helper;

    
        public function Initiate()
        {
            $this->suppress_view = 0;
            $this->view_rendered = 0;
            $this->view_name = "";
        }

	/**
	 * Construct a view instance according to passed request
	 */
	public function __construct(\iMVC\kernel\routing\request $request)
	{
            $request->Process();
            $this->Initiate();
            $this->request = $request;
	}

	function __destruct()
	{
            $this->Dispose();
	}
    
        public function Dispose()
        {
            parent::Dispose();
        }

	/**
	 * Set target view's name
	 * 
	 * @param view_name
	 */
	public function setView(string $view_name)
	{
            $this->view_name = $view_name;
	}

	/**
	 * Set or Unset view suppression value
	 * 
	 * @param should_suppressed
	 */
	public function suppressView($should_suppressed = 1)
	{
            $this->suppress_view = $should_suppressed;
	}

	/**
	 * Check status of view suppression 
	 */
	public function IsViewSuppressed()
	{
            return $this->suppress_view;
	}

       /**
        * Render a proper view
        * @param boolean $echo_ouput wheter the output should be echoed or not!
        * @return string if $echo_ouput == 1 the result of view rendering get returned
        */
	public function Render($echo_ouput = 1)
	{
            if($this->suppress_view)
                return;
            if(!file_exists($this->GetViewPath()))
                throw new \iMVC\exceptions\notFoundException("The view `{$this->GetViewName()}` not found at `{$this->GetViewPath()}`");
            if(!$this->view_rendered)
            {
                ob_start();
                    # invoking view's file
                    require $this->GetViewPath();
                    $this->content = ob_get_contents();
                ob_end_clean();
                if($echo_ouput) echo $this->content;
                else return $this->content;
            }
            else
            {
                throw new iMVC\Exceptions\AppException("The view has been rendered previously...");
            }
            $this->view_rendered = true;
	}

	/**
	 * Get current view's name
         * @var string  
	 */
	public function GetViewName()
	{
            return $this->request->view->full_name;
	}

	/**
	 * Get current view path
	 */
	public function GetViewPath()
	{
            # try RAM cache first
            if(isset($this->_view_path) && file_exists($this->_view_path))
                return $this->_view_path;
            $p = "";
            # create a caching signature based on provided request
            $cache_sig =__METHOD__."@{$this->request->module->full_name}::{$this->request->controller->full_name}
                ::{$this->request->action->full_name}::{$this->request->type}";
            
            # check session cache system
            $sc = new \iMVC\kernel\caching\sessionCache(__CLASS__);
            if($sc->isCached(sha1($cache_sig)))
            {
                $p = $sc->retrieve($cache_sig);
                # if catch is valid return it
                if(file_exists($p)) goto __RETURN;
                # if the file does not exist delete it from cache system and try to recover new one
                $sc->erase($cache_sig);
            }
            
            # check file cache system
            $fc = new \iMVC\kernel\caching\fileCache(__CLASS__);
            if($fc->isCached(sha1($cache_sig)))
            {
                $p = $fc->retrieve($cache_sig);
                # if catch is valid return it
                if(file_exists($p)) goto __RETURN;
                # if the file does not exist delete it from cache system and try to recover new one
                $fc->erase($cache_sig);
            }
            
            # view directory
            $p = $this->request->view->GetRootDirectory();
            
            # try straight locating 
            if(file_exists($p.$this->GetViewName().$this->request->view->GetExtention())) 
            {
                $p = $p.$this->GetViewName().$this->request->view->GetExtention();
                goto __END;
            }
            if(!file_exists($p))
                throw new \iMVC\exceptions\notFoundException("The view '".$this->GetViewName().$this->request->view->GetExtention()."' not found!");
            # if straight locating didn't work
            if (($handle = opendir($p))) 
            {
                # try normalized file search
                while (false !== ($file = readdir($handle))) 
                {
                    if(strtolower($file) == strtolower($this->GetViewName().$this->request->view->GetExtention()))
                    {
                        closedir($handle);
                        $p = $p.$file;
                        goto __END;
                    }
                }
                # if all file processed 
                # then the view didnt find in fs
                $fc->erase($cache_sig);
                closedir($handle);
                throw new \iMVC\exceptions\notFoundException("The view '".$this->GetViewName().$this->request->view->GetExtention()."' not found!");
            }
            else
                throw new \iMVC\exceptions\invalideOperationException("Could not open directory '$p'");
        __END:
            # catch the result
            $fc->store($cache_sig, $p);
        __RETURN:
            $this->_view_path = $p;
            return $p;
	}

	/**
	 * Partially loads a view
	 * 
	 * @param view_name
	 * @param partial_view_params
	 */
	public function RenderPartial(string $view_name, array $partial_view_params = array())
	{
            if($view_name == $this->request->view)
                throw new \iMVC\Exceptions\InvalideOperationException("Cannot partially load the currently loaded view...");

            // create a fake view handler
            $nv = new \iMVC\View\BaseView($this->request);
            // set view's name
            $nv->SetView($view_name);
            // is any args are set load it.
            if(isset($partial_view_params))
            {
                // import variables
                foreach($partial_view_params as $key => $value)
                {
                    $nv->$key = $value;
                }
            }
            // render fake view which is going to be our partial view
            $nv->Render();
            // dispose values
            $nv->Dispose();
	}

}