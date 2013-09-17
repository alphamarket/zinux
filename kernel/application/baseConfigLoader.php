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
abstract class baseConfigLoader extends \zinux\kernel\application\baseInitializer
{
    /**
     * config file's address
     * @var string
     */
    public $file_address;
    public abstract function __construct($config_file_address, array $options = array());
}