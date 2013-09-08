<?php
namespace iMVC\kernel\db;

require_once (dirname(__FILE__).'/../../baseiMVC.php');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of basedbInitializer
 *
 * @author dariush
 */
abstract class basedbInitializer extends \iMVC\baseiMVC
{
    public abstract function Execute($request);
}