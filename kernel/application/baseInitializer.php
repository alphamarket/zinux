<?php
namespace zinux\kernel\application;

require_once (dirname(__FILE__).'/../../baseZinux.php');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of basedbInitializer
 *
 * @author dariush
 */
abstract class baseInitializer extends \zinux\baseZinux
{
    /**
     * @return array Configurations as array
     */
    public abstract function Execute();
}