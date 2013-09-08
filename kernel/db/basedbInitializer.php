<?php
namespace zinux\kernel\db;

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
abstract class basedbInitializer extends \zinux\baseZinux
{
    public abstract function Execute($request);
}