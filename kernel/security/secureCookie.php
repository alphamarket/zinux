<?php
namespace zinux\kernel\security;


/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 17:21:13
 */
class secureCookie
{

        public function __get($name)
        {
            if(!self::Contains($name))
                return false;
            return $_COOKIE[$name];
        }

        /**
         * @param $name
         * @param array $value_expire expected format { array('value'=>$value, 'expire'=>$expire) }
         */
        public function __set($name, array $value_expire) 
        {
            self::Set($name, $value_expire['value'], $value_expire['expire']);
        }



	/**
	 * Set a secure cookie
	 * 
	 * @param name
	 * @param value
	 * @param expire_from_now
	 * @param path
	 */
	public static function set($name, $value, integer $expire_from_now, $path = "/")
	{
            // in testing env we do not want to set any cookie!!!
            if(defined(RUNNING_ENV)  && RUNNING_ENV == TEST) return;
            require_once 'hash.php';
            setcookie("$name", $value, time()+$expire_from_now, $path); 
            setcookie("h${name}", hash::Generate($name.$value.'53cUr3'.'hA5h'), time()+$expire_from_now, $path);
	}

	/**
	 * Delete a cookie
	 * 
	 * @param name
	 */
	public function delete($name)
	{
            # set expire date to my birth day :)
            self::Set($name, $name, mktime(0, 0, 0, 10, 5, 1991));
	}

	/**
	 * Check if a cookie exists or not
	 * 
	 * @param name
	 */
	public function contains($name)
	{
            if(!isset($_COOKIE[$name]) || !isset($_COOKIE["h${name}"]))
            {
                # this causes un-intended header sending problem, we don't want it!
                # self::Delete($name);
                return false;
            }
            require_once 'hash.php';
            if(hash::Generate($name.$_COOKIE[$name].'53cUr3'.'hA5h')!=$_COOKIE["h${name}"])
            {
                # this causes un-intended header sending problem, we don't want it!
                # self::Delete($name);
                # return false;
                throw new \Exception("The cookie's data is corrupted!");
            }
            return true;
	}

}