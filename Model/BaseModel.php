<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModelBase
 *
 * @author dariush
 */
require_once 'BaseMVC.php';
class ModelBase extends iMVC\BaseMVC
{
    /**
     * Hold request value
     * @var iMVC\Routing\Request
     */
    protected $request;
    
    public function Initiate(){ }
    
    public function __construct(iMVC\Routing\Request $request) 
    {
    }
    
    public function LoadModel($model_name, $module_name = NULL)
    {
        
    }
    
    public function LoadGlobalModel($model_name)
    {
        
    }
    
    public function IsModelLoaded()
    {
        
    }
}

?>
