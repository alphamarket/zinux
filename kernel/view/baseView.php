<?php
namespace zinux\kernel\view;

require_once (dirname(__FILE__).'/../../baseZinux.php');

/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:24:30
 */
class baseView extends \zinux\baseZinux
{

    /**
    * Holds the current request instance 
    * @var \zinux\kernel\routing\request
    */
    public $request;
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
     * @var \zinux\kernel\mvc\view
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
	public function __construct(\zinux\kernel\routing\request $request)
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
	public function setView($view_name)
	{
            $this->metadata->SetViewName($view_name);
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
                if(!$this->metadata->GetPath()) 
                {
                    echo ("<div>Notice: View '<b>{$this->request->view->full_name}</b>' not found for '<b>{$this->request->module->full_name} / {$this->request->controller->full_name} / {$this->request->action->full_name}</b>'...!</div>");
                    return;
                }
                ob_start();
                    # invoking view's file
                    # we cannot use $this->metadata->Load(); 'cause then the view's 
                    # file would operate unser \zinux\kernel\mvc\view instance!!
                    require $this->metadata->GetPath();
                    $this->content = ob_get_contents();
                ob_end_clean();
                if($echo_ouput) echo $this->content;
                else return $this->content;
            }
            else
            {
                throw new zinux\kernel\exceptions\AppException("The view has been rendered previously...");
            }
            $this->view_rendered = true;
	}
    
	/**
	 * Partially loads a view
	 * 
	 * @param view_name
	 * @param partial_view_params
	 */
	public function RenderPartial($view_name, array $partial_view_params = array())
	{
            if($view_name == $this->metadata->name)
                throw new \zinux\kernel\exceptions\InvalideOperationException("Cannot partially load the currently loaded view...");
            // backup current view name
            $current_view_name = $this->metadata->name;
            // create a fake view handler
            $nv = $this;
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
            // reset the view name
            $this->setView($current_view_name);
	}

}