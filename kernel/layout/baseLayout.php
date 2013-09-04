<?php
require_once ('..\..\BaseiMVC.php');

namespace iMVC\kernel\layout;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:35:07
 */
class baseLayout extends BaseiMVC
{

	/**
	 * View object related to layout
	 */
	public $view;
	/**
	 * Options settings
	 */
	public $options;
	/**
	 * CSS tags collection
	 */
	protected static $CSSImports;
	/**
	 * JS tags collection
	 */
	protected static $JSImports;
	/**
	 * check if layout has rendered or not
	 */
	protected static $layout_rendered = 0;
	/**
	 * meta tags collection
	 */
	protected static $MetaImports;
	/**
	 * check if layout has suppressed
	 */
	protected static $suppress_layout = 0;

	function __construct()
	{
	}

	function __destruct()
	{
	}



	/**
	 * Add a css tags to html doc
	 * 
	 * @param URI    css uri address
	 * @param name    hash name of this css
	 * @param overwrite_on_existance    check if your want to overwrite on existance
	 * of current css' name
	 */
	public function AddCSS(string $URI, string $name = "", boolean $overwrite_on_existance = 0)
	{
	}

	/**
	 * Add a js tags to html doc
	 * 
	 * @param URI    js uri address
	 * @param name    hash name of this js
	 * @param overwrite_on_existance    check if your want to overwrite on existance
	 * of current js' name
	 */
	public function AddJS(string $URI, string $name = "", boolean $overwrite_on_existance = 0)
	{
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
	public function AddMeta(string $name, string $content, string $http_equiv = "", boolean $overwrite_on_existance = 0)
	{
	}

	/**
	 * Get layout's file's name
	 */
	public function GetLayoutName()
	{
	}

	/**
	 * Get layout full path
	 */
	public function GetLayoutPath()
	{
	}

	/**
	 * Check status of view suppression
	 */
	public function IsLayoutSuppressed()
	{
	}

	/**
	 * Remove a css tag by name
	 * 
	 * @param name
	 */
	public function RemoveCSS(string $name)
	{
	}

	/**
	 * Remove a js tag by name
	 * 
	 * @param name
	 */
	public function RemoveJS(string $name)
	{
	}

	/**
	 * Remove a meta tag by name
	 * 
	 * @param name
	 */
	public function RemoveMeta(string $name)
	{
	}

	/**
	 * Renders Layout and View
	 */
	public function Render()
	{
	}

	/**
	 * Renders CSS tags which has been imports to html doc.
	 */
	protected function RenderCSSImports()
	{
	}

	/**
	 * Renders CSS/JS/Meta tags which has been imports to html doc.
	 */
	protected function RenderImports()
	{
	}

	/**
	 * Renders JS tags which has been imports to html doc.
	 */
	protected function RenderJSImports()
	{
	}

	/**
	 * Renders Layout
	 */
	protected function RenderLayout()
	{
	}

	/**
	 * Renders Meta tags which has been imports to html doc.
	 */
	protected function RenderMetaImports()
	{
	}

	/**
	 * Renders View
	 */
	protected function RenderView()
	{
	}

	/**
	 * Set default layout
	 */
	public function SetDefaultLayout()
	{
	}

	/**
	 * set passed layout name as chosen layout
	 * 
	 * @param name
	 */
	public function SetLayout(string $name)
	{
	}

	/**
	 * change layout suppression status
	 * 
	 * @param should_suppress    check if layout should suppress
	 */
	public function SuppressLayout($should_suppress = 1)
	{
	}

}
?>