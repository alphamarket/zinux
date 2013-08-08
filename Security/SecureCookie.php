<?php

namespace iMVC\Security;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Set secure cookie
 *
 * @author dariush
 */
require_once 'Hash.php';
class SecureCookie 
{
    public function __get($name)
    {
        if(!self::Contains($name))
            return false;
        return $_COOKIE[$name];
    }
    
    /**
     * @param string $name
     * @param array $value_expire expected format { array('value'=>$value, 'expire'=>$expire) }
     */
    public function __set($name, array $value_expire) 
    {
        self::Set($name, $value_expire['value'], $value_expire['expire']);
    }

    /**
     * Set a secure cookie
     * @param string $name
     * @param string $value
     * @param integer $expire_from_now
     */
    public static function Set($name, $value, $expire_from_now)
    {
        // in testing env we do not want to set any cookie!!!
        if(RUNNING_ENV == TEST) return;
        
        setcookie("$name", $value, time()+$expire_from_now); 
        setcookie("h${name}", \iMVC\Security\Hash::Generate($name.$value.'53cUr3'.'hA5h'), time()+$expire_from_now);
    }
    /**
     * Delete a cookie
     * @param string $name
     */
    public static function Delete($name)
    {
        self::Set($name, $name, -(24*3600));
        self::Set("h${name}", $name, -(24*3600));
    }
    /**
     * Check if a cookie exists or not
     * @param string $name the cookie's name
     * @return boolean if cookie exist and its secure
     * @throws \iMVC\Exceptions\SecurityException if the expected cookie does not match with its hashed cookie.
     */
    public  static function Contains($name)
    {
        if(!isset($_COOKIE[$name]) || !isset($_COOKIE["h${name}"]))
        {
            # this causes un-intended header sending problem, we don't want it!
            # self::Delete($name);
            return false;
        }
        if(\iMVC\Security\Hash::Generate($name.$_COOKIE[$name].'53cUr3'.'hA5h')!=$_COOKIE["h${name}"])
        {
            # this causes un-intended header sending problem, we don't want it!
            # self::Delete($name);
            # return false;
            throw new \iMVC\Exceptions\SecurityException("The cookie's data is corrupted!");
        }
        return true;
    }
}

?>
