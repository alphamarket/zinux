<?php
namespace iMVC\kernel\layout;
require_once (dirname(__FILE__).'/../../baseiMVC.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:10:15
 */
class baseLayout extends \iMVC\baseiMVC
{

	/**
	 * View object related to layout
         * @var \iMVC\kernel\view\baseView  
	 */
	public $view;
	/**
	 * Holds the current request instance 
         * @var \iMVC\kernel\routing\request
         */
	public $request;
        /**
         *  holds layout instance
         * @var \iMVC\kernel\mvc\layout 
         */
        public $layout;
	/**
	 * Options settings
	 */
	public $options;
	/**
	 * CSS tags collection
	 */
	protected $CSSImports;
	/**
	 * JS tags collection
	 */
	protected $JSImports;
	/**
	 * check if layout has rendered or not
	 */
	protected $layout_rendered = 0;
	/**
	 * meta tags collection
	 */
	protected $MetaImports;
	/**
	 * check if layout has suppressed
	 */
	protected $suppress_layout = 0;

	function __construct(\iMVC\kernel\view\baseView $view)
	{
            $this->view = $view;
            $this->request = $view->request;
            $this->Initiate();
	}

        public function Initiate()
        {
            $this->options = new \stdClass();
            $this->CSSImports = array();
            $this->JSImports = array();
            $this->MetaImports = array();
            $this->SetDefaultLayout();
        }
        public function Dispose()
        {
            parent::Dispose();
        }


	/**
	 * Add a css tags to html doc
	 * 
	 * @param URI    css uri address
	 * @param name    hash name of this css
	 * @param overwrite_on_existance    check if your want to overwrite on existance
	 * of current css' name
	 */
	public function AddCSS($URI, $name = NULL, $options = array(), $overwrite_on_existance = 0)
	{
            if(!isset($name))
                $name = sha1($URI);
            if(isset($this->CSSImports[$name]) && !$overwrite_on_existance) return;
            $this->CSSImports[$name] = array('uri' => $URI, 'options' => $options);
	}

	/**
	 * Add a js tags to html doc
	 * 
	 * @param URI    js uri address
	 * @param name    hash name of this js
	 * @param overwrite_on_existance    check if your want to overwrite on existance
	 * of current js' name
	 */
	public function AddJS($URI, $name = NULL, $options = array(), $overwrite_on_existance = 0)
	{
            if(!isset($name))
                $name = sha1($URI);
            if(isset($this->JSImports[$name]) && !$overwrite_on_existance) return;
            $this->JSImports[$name] = array('uri' => $URI, 'options' => $options);
	}

	/**
	 * Add a meta tags to html doc
	 * 
	 * @param name
	 * @param content    hash name of this js
	 * @param http_equiv
	 * @param overwrite_on_existance    check if your want to overwrite on existance
	 * of current meta' name
	 */
	public function AddMeta($name, $content, $options = array(), $overwrite_on_existance = 0)
	{
            if(isset($this->MetaImports[$name]) && !$overwrite_on_existance) return;
            $this->MetaImports[$name] = array('content' =>$content, 'options' => $options);
	}
	/**
	 * Get layout full path
	 */
	public function GetLayoutPath()
	{
            return $this->layout->GetPath();
	}

	/**
	 * Check status of view suppression
	 */
	public function IsLayoutSuppressed()
	{
            return $this->suppress_layout;
	}

	/**
	 * Remove a css tag by name
	 * 
	 * @param name
	 */
	public function RemoveCSS($name)
	{
            unset($this->CSSImports[$name]);
	}

	/**
	 * Remove a js tag by name
	 * 
	 * @param name
	 */
	public function RemoveJS($name)
	{
            unset($this->JSImports[$name]);
	}

	/**
	 * Remove a meta tag by name
	 * 
	 * @param name
	 */
	public function RemoveMeta($name)
	{
            unset($this->MetaImports[$name]);
	}

	/**
	 * Renders Layout and View
	 */
	public function Render()
	{
            $this->RenderView();
            $this->RenderLayout();
	}

	/**
	 * Renders CSS tags which has been imports to html doc.
	 */
	protected function RenderCSSImports()
	{
            foreach($this->CSSImports as $css)
            {
                if(!isset($css['options']) || !is_array($css['options'])) $css['options'] = array(); 
                echo "<link rel='stylesheet' type='text/css' href='${css['uri']}'";
                foreach($css['options'] as $key=> $value)
                {
                    echo " $key='$value'";
                }
                echo ">";
            }
	}

	/**
	 * Renders CSS/JS/Meta tags which has been imports to html doc.
	 */
	protected function RenderImports()
	{
           $this->RenderMetaImports();
           $this->RenderJSImports();
           $this->RenderCSSImports();
	}

	/**
	 * Renders JS tags which has been imports to html doc.
	 */
	protected function RenderJSImports()
	{ 
            foreach($this->JSImports as $js)
            {
                if(!isset($js['options']) || !is_array($js['options'])) $js['options'] = array(); 
                echo "<script type='text/javascript' src='${js['uri']}'";
                foreach($js['options'] as $key=> $value)
                {
                    echo " $key='$value'";
                }
                echo "></script>";
            }
	}

	/**
	 * Renders Layout
	 */
	protected function RenderLayout()
	{
            if(!$this->layout_rendered)
            {
                if(!file_exists($this->GetLayoutPath()))
                {
                    echo "<center><h2>Layout not loaded ...<br />The layout '".$this->GetLayoutName ()."' not found!</center></h2>";
                    $this->SuppressLayout();
                }
                if(!$this->view->IsViewSuppressed() && !$this->IsLayoutSuppressed())
                {
                    require $this->GetLayoutPath();
                }
                else
                    echo $this->content;
                $this->layout_rendered = true;
            }
            else
            {
                throw new \Exceptions\AppException("The view has been rendered already...");
            }
	}

	/**
	 * Renders Meta tags which has been imports to html doc.
	 */
	protected function RenderMetaImports()
	{
            foreach($this->MetaImports as $name => $meta)
            {
                if(!isset($meta['options']) || !is_array($meta['options'])) $meta['options'] = array(); 
                echo "<meta name='$name' content='${meta['content']}'";
                foreach($meta['options'] as $key=> $value)
                {
                    echo " $key='$value'";
                }
                echo ">";
            }
	}

	/**
	 * Renders View
	 */
	protected function RenderView()
	{
            $this->content = $this->view->Render(0);
	}

	/**
	 * Set default layout
	 */
	public function SetDefaultLayout()
	{
            $this->SetLayout('default');
	}

	/**
	 * set passed layout name as chosen layout
	 * 
	 * @param name
	 */
	public function SetLayout($name)
	{
            $this->layout = new \iMVC\kernel\mvc\layout($name, $this->request->module);
	}
        public function GetLayoutName ()
        {
            return $this->layout->full_name;
        }

	/**
	 * change layout suppression status
	 * 
	 * @param should_suppress    check if layout should suppress
	 */
	public function SuppressLayout($should_suppress = 1)
	{
            $this->suppress_layout = $should_suppress;
	}

}