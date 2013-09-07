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
    /**
     *
     * @var \iMVC\kernel\mvc\view
     */
    public $metadata;

    
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
            $this->metadata =$request->view;
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
            if(!$this->view_rendered)
            {
                ob_start();
                    # invoking view's file
                    # we cannot use $this->metadata->Load(); 'cause then the view's 
                    # file would operate unser \iMVC\kernel\mvc\view instance!!
                    require $this->metadata->GetPath();
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