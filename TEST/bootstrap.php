<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * @author dariush
 */
// TODO: check include path
ini_set('include_path', implode(PATH_SEPARATOR, array(ini_get('include_path'),  realpath(dirname(__FILE__)."/../"))));

chdir(realpath(dirname(__FILE__)));

spl_autoload_register(function ($class) {
        if(strpos($class, "iMVC")===false) return;
        $r = explode("\\", $class);
        unset($r[0]);
        $c = implode(DIRECTORY_SEPARATOR, $r);
        require_once "../$c.php";
});