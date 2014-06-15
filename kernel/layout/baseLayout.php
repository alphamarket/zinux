<?php
namespace zinux\kernel\layout;
require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:10:15
 */
class baseLayout extends \zinux\baseZinux
{
    /**
     * The current layout's meta data
     * @var \zinux\kernel\mvc\layout
     */
    public $metadata;
    /**
    * View object related to layout
    * @var \zinux\kernel\view\baseView  
    */
    public $view;
    /**
    * Holds the current request instance 
    * @var \zinux\kernel\routing\request
    */
    public $request;
    /**
     *  holds layout instance
     * @var \zinux\kernel\mvc\layout 
     */
    public $meta;
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
     * layout title string
     */
    protected $title;
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

    function __construct(\zinux\kernel\view\baseView $view)
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
        $this->AddTitle("");
        $this->SetDefaultLayout();
    }
    public function Dispose()
    {
        parent::Dispose();
    }
    /**
     * Add a title to layout
     * @param type $title
     * @throws \zinux\kernel\exceptions\invalidArgumentException
     */
    public function AddTitle($title = "")
    {
        if(!is_string($title))
            throw new \zinux\kernel\exceptions\invalidArgumentException
                ("The title should be a string");
        $this->title = $title;
    }
    /**
     * Returns title
     */
    public function  GetTitle()
    {
        return $this->title;
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
            if(!file_exists($this->metadata->GetPath()))
            {
                echo "<div>Notice: The layout '<b>".$this->metadata->full_name."</b>' not found!<div>";
                $this->SuppressLayout();
            }
            if(!$this->IsLayoutSuppressed())
            {
                # we cannot use $this->metadata->Load(); 'cause then the layout's 
                # file would operate under \zinux\kernel\mvc\layout instance!!
                require $this->metadata->GetPath();
            }
            else
                echo $this->content;
            $this->layout_rendered = true;
        }
        else
        {
            throw new \zinux\kernel\exceptions\appException("The view has been rendered already...");
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
            echo "<meta name='$name' content=\"".str_replace('"', "'", $meta['content'])."\"";
            foreach($meta['options'] as $key=> $value)
            {
                echo " $key='$value'";
            }
            echo ">";
        }
    }
    /**
     * Render the title
     */
    protected function RenderTitle()
    {
        echo "<title>{$this->title}</title>";
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
        $this->metadata =  new \zinux\kernel\mvc\layout($name, $this->request->module);
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