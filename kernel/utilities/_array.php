<?php
namespace iMVC\kernel\utilities;

require_once dirname(__FILE__).'/../../baseiMVC.php';

class _array extends \iMVC\baseiMVC
{
    /**
     * normalizes the array items
     * @param array $array
     */
    public static function array_normalize(array &$array)
    {
        $array = array_filter($array, 'strlen');
        $array = count($array)? array_chunk($array, count($array)) : array();
        $array = count($array)? $array[0] : array();
        return $array;
    }

    public function Initiate(){}
}