<?php
namespace iMVC\Model\DB;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModelBase
 *
 * @author dariush
 */
require_once 'Model/BaseModel.php';

class DBModel extends \iMVC\BaseMVC
{
    public function Initiate() {}
    
    public static function LoadDBModel($model_name, $module_name = NULL)
    {
        $mb = new \iMVC\Model\BaseModel;
        
        if(!isset($model_name))
            throw new InvalidArgumentException("\$model_name is not setted!");
        
        if(!isset($module_name))
            $module_name = $mb->GetRequest()->module;
        
        $model_name = str_replace(".php", "", $model_name);
        $model_name = str_replace("Model", "", $model_name);
        $model_name = str_replace("DB", "", $model_name);
        $model_name = "{$model_name}DBModel";
        
        if(self::IsDBModelLoaded($model_name, $module_name))
        {
            return;
        }
        $mp = MODULE_PATH."/{$module_name}/Models/DB/$model_name.php";
        
        if(!file_exists($mp))
            throw new \iMVC\Exceptions\NotFoundException("Model '{$model_name}.php' not found at '/{$module_name}/Models/DB'");
        
        require_once $mp;
        
        if(!class_exists("{$model_name}"))
            throw new \ErrorException("Model '{$model_name}.php' found but class '{$model_name}' does not exists.");
            
        self::MarkAsLoaded($model_name, $module_name);
    }
    
    public static function LoadGlobalDBModel($model_name, $module_name = "__GLOBAL")
    {
        if(!\iMVC\Tools\String::startsWith($module_name, '__'))
                throw new InvalidArgumentException("Global module names should 
                    start with '__' indicator, but provided module name 
                    '$module_name' does not have such signature!");
        
        self::LoadDBModel($model_name, $module_name);
    }
    
    public static function IsGlobalDBModelLoaded($model_name, $module_name = "__GLOBAL")
    {
        if(!\iMVC\Tools\String::startsWith($module_name, '__'))
                throw new InvalidArgumentException("Global module names should 
                    start with '__' indicator, but provided module name 
                    '$module_name' does not have such signature!");
        return self::IsDBModelLoaded($model_name, $module_name);
    }
    public static function IsDBModelLoaded($model_name, $module_name = NULL)
    {
        $mb = new \iMVC\Model\BaseModel;
        
        $model_name = str_replace(".php", "", $model_name);
        $model_name = str_replace("Model", "", $model_name);
        $model_name = str_replace("DB", "", $model_name);
        $model_name = "{$model_name}DBModel";
        
        if(!isset($module_name))
            $module_name = $mb->GetRequest()->module;
        
        return $mb->IsModelLoaded($model_name, $module_name);
    }
    
    protected static function MarkAsLoaded($model_name, $module_name)
    {
        $model_name = str_replace(".php", "", $model_name);
        $model_name = str_replace("Model", "", $model_name);
        $model_name = str_replace("DB", "", $model_name);
        $model_name = "{$model_name}DBModel";
        \iMVC\Model\BaseModel::MarkAsLoaded($model_name, $module_name);
    }
}

?>
