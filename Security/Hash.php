<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HashGenerator
 *
 * @author dariush
 */
class Hash 
{
    public static function Generate($content)
    {
        return md5(sha1('@hasanpoor'.md5($content).'dariush~#'));
    }
}
