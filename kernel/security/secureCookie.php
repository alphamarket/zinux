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
    * @param string $name <p>
    * The name of the cookie.
    * </p>
    * @param string $value [optional] <p>
    * The value of the cookie. This value is stored on the clients computer;
    * do not store sensitive information. Assuming the
    * <i>name</i> is 'cookiename', this
    * value is retrieved through $_COOKIE['cookiename']
    * </p>
    * @param int $expire_from_now [optional] <p>
    * The time the cookie expires. This is a Unix timestamp so is
    * in number of seconds since the epoch. In other words, you'll
    * most likely set this with the <b>time</b> function
    * plus the number of seconds before you want it to expire. Or
    * you might use <b>mktime</b>.
    * 60*60*24*30 will set the cookie to
    * expire in 30 days. If set to 0, or omitted, the cookie will expire at
    * the end of the session (when the browser closes).
    * </p>
    * <p>
    * <p>
    * You may notice the <i>expire</i> parameter takes on a
    * Unix timestamp, as opposed to the date format Wdy, DD-Mon-YYYY
    * HH:MM:SS GMT, this is because PHP does this conversion
    * internally.
    * </p>
    * </p>
    * @param string $path [optional] <p>
    * The path on the server in which the cookie will be available on.
    * If set to '/', the cookie will be available
    * within the entire <i>domain</i>. If set to
    * '/foo/', the cookie will only be available
    * within the /foo/ directory and all
    * sub-directories such as /foo/bar/ of
    * <i>domain</i>. The default value is the
    * current directory that the cookie is being set in.
    * </p>
    * @param string $domain [optional] <p>
    * The domain that the cookie is available to. Setting the domain to
    * 'www.example.com' will make the cookie
    * available in the www subdomain and higher subdomains.
    * Cookies available to a lower domain, such as
    * 'example.com' will be available to higher subdomains,
    * such as 'www.example.com'.
    * Older browsers still implementing the deprecated
    * RFC 2109 may require a leading
    * . to match all subdomains.
    * </p>
    * @param bool $secure [optional] <p>
    * Indicates that the cookie should only be transmitted over a
    * secure HTTPS connection from the client. When set to <b>TRUE</b>, the
    * cookie will only be set if a secure connection exists.
    * On the server-side, it's on the programmer to send this
    * kind of cookie only on secure connection (e.g. with respect to
    * $_SERVER["HTTPS"]).
    * </p>
    * @param bool $httponly [optional] <p>
    * When <b>TRUE</b> the cookie will be made accessible only through the HTTP
    * protocol. This means that the cookie won't be accessible by
    * scripting languages, such as JavaScript. It has been suggested that
    * this setting can effectively help to reduce identity theft through
    * XSS attacks (although it is not supported by all browsers), but that
    * claim is often disputed. Added in PHP 5.2.0.
    * <b>TRUE</b> or <b>FALSE</b>
    * </p>
    * @return bool If output exists prior to calling this function,
    * <b>setcookie</b> will fail and return <b>FALSE</b>. If
    * <b>setcookie</b> successfully runs, it will return <b>TRUE</b>.
    * This does not indicate whether the user accepted the cookie.
    */
    public static function set($name, $value = null, $expire_from_now = 0, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        // in testing env we do not want to set any cookie!!!
        if(defined(RUNNING_ENV)  && RUNNING_ENV == TEST) return;
        require_once 'hash.php';
        $return = setcookie("$name", $value, time()+$expire_from_now, $path, $domain, $secure, $httponly); 
        $return &= setcookie("h${name}", hash::Generate($name.$value.'53cUr3'.'hA5h'), time()+$expire_from_now, $path, $domain, $secure, $httponly);
        # online update of $_COOKIE array
        # if we are on delete ops?
        if($expire_from_now<0)
        {
            # unset them
            unset($_COOKIE[$name]);
            unset($_COOKIE["h{$name}"]);
        }
        # if we are on set ops?
        else
        {
            # set them
            $_COOKIE[$name] = $value;
            $_COOKIE["h${name}"] = hash::Generate($name.$value.'53cUr3'.'hA5h');
        }
        return $return;
    }
    /**
    * (PHP 5)<br/>
    * Send a cookie without urlencoding the cookie value
    * @link http://php.net/manual/en/function.setrawcookie.php
    * @param string $name
    * @param string $value [optional]
    * @param int $expire [optional]
    * @param string $path [optional]
    * @param string $domain [optional]
    * @param bool $secure [optional]
    * @param bool $httponly [optional]
    * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
    */
    public static function setrawcookie($name, $value = null, $expire_from_now = 0, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        // in testing env we do not want to set any cookie!!!
        if(defined(RUNNING_ENV)  && RUNNING_ENV == TEST) return;
        require_once 'hash.php';
        $return = setrawcookie("$name", $value, time()+$expire_from_now, $path, $domain, $secure, $httponly); 
        $return &= setrawcookie("h${name}", hash::Generate($name.$value.'53cUr3'.'hA5h'), time()+$expire_from_now, $path, $domain, $secure, $httponly);
        return $return;
    }

    /**
    * Delete a cookie
    * @param name
    */
    public static function delete($name, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        # set expire date to my birth day :)
        $expire = mktime(0, 0, 0, 10, 5, 1991);
        setcookie($name, "", $expire, $path, $domain, $secure, $httponly);
        setcookie("h{$name}", "", $expire, $path, $domain, $secure, $httponly);
        unset($_COOKIE[$name]);
        unset($_COOKIE["h{$name}"]);
    }

    /**
    * Check if a cookie exists or not
    * @param name
    */
    public static function contains($name)
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
            throw new \Exception("The cookie's data is corrupted! Please delete cookies and try again!!");
        }
        return true;
    }
    /**
     * Fetch a cookie with essential security steps
     * @param string $name the cookie's name
     * @return null if not exists, otherwise the secured item
     */
    public static function get($name)
    {
        if(!self::contains($name))
            return NULL;
        return $_COOKIE[$name];
    }
}