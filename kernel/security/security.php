<?php
namespace zinux\kernel\security;

require_once (dirname(__FILE__).'/../../baseZinux.php');


/**
 * This will provide operations on security in request with will provide the
 * secure GET/POST in target destination
 * @author dariush
 * @version 1.0
 * @updated 04-Sep-2013 17:23:16
 */
class security
{
    /* 
     * Get a secure GET/POST compatible string
     * @param array $based_upon an array to create secure string based upon it
     * @param string $for_uri Explicitly define the target uri(default: @$_SERVER['REQUEST_URI'])
     * @param boolean $has_expire_date the secure string should has expiration date
     * @param unsigned integer $second_to_expire_from_now the seconds to expire the secure string
     * @return string the secure string
     * @throws \InvalidArgumentException arises if condition <b>$has_expire_date && $seconds_to_expire_from_now<=0</b> satisfied
     */
    public static function __get_uri_hash_string(array $based_upon = array(), $has_expire_date = 0, $seconds_to_expire_from_now = 0) {
        $hash = "";
        foreach(self::__get_uri_hash_array($based_upon, $has_expire_date, $seconds_to_expire_from_now) as $key => $value) {
            $hash = "$hash&$key=$value";
        }
        return $hash;
    }
    /* 
     * Get a secure GET/POST compatible array
     * @param array $based_upon an array to create secure string based upon it
     * @param string $for_uri Explicitly define the target uri(default: @$_SERVER['REQUEST_URI'])
     * @param boolean $has_expire_date the secure string should has expiration date
     * @param unsigned integer $second_to_expire_from_now the seconds to expire the secure string
     * @return array the secure string array
     * @throws \InvalidArgumentException arises if condition <b>$has_expire_date && $seconds_to_expire_from_now<=0</b> satisfied
     */
    public static function __get_uri_hash_array(array $based_upon = array(), $has_expire_date = 0, $seconds_to_expire_from_now = 0) {
        # validate if expire date's args has no confilicts with each others
        if($has_expire_date && $seconds_to_expire_from_now<=0)
            throw new \InvalidArgumentException("The '\$second_to_expire_from_now' didn't provide");
        # generating security fields name
        $tn  = "__s_".substr(sha1('t'), 0,5);
        $hn = "__s_".substr(sha1('h'), 0,5);
        $en = "__s_".substr(sha1('e'), 0,5);
        $rn  = "__s_".substr(sha1('r'), 0,5);
        # get current timestamp
        $t = time();
        # push the timestamp into based upon args
        $based_upon[] = $t;
        # push session ID if any exists into based upon args
        $based_upon[] = @session_id();
        # create and init the return link URI component with generated timestamp
        $link =array($tn => $t);
        # if we have an expiration on link
        if($has_expire_date) {
            # compute expire timestamp
            $et = time() + $seconds_to_expire_from_now;
            # push expiration time into based upon args
            $based_upon[] = $et;
            # set expiration time into return link URL component
            $link[$en] = $et;
        }
        # invoke hash module
        require_once 'hash.php';
        # generate has from based upon args and set hash value into return link URL component
        $link[$hn] = hash::Generate(implode("", $based_upon));
        # set requested URI into return link URL component
        $link[$rn] = self::getURIHash($t.self::getRequestURI());
        # return the link URI component
        return $link;
    }
    /**
     * Get proper request URI for security purposes
     * @return string
     */
    protected static function getRequestURI() {
        return @$_SERVER["REQUEST_SCHEME"]."://".@$_SERVER["SERVER_NAME"].":".@$_SERVER["SERVER_PORT"];
    }
    /**
     * Get proper referer URI for security purposes
     * @return string
     */
    protected static function getRefererURI() {
        $pu = @parse_url(@$_SERVER["HTTP_REFERER"]);
        if(!@isset($pu["port"])) { switch(strtolower(@$pu["scheme"])) { case "http": $pu["port"] = 80; break; case "https": $pu["port"] = 443; break; } }
        return @$pu["scheme"]."://".@$pu["host"].":".@$pu["port"];
    }
    /**
     * Get a proper hash for a URI
     * @param string $uri the URI
     * @return string The hash
     */
    protected static function getURIHash($uri) { return sha1("__R3F3R__{$uri}__53CUR!TY__"); }
    /**
     * Checks if URI is secure with provided parameters
     * @param array $target_array    Target array to examine parameters
     * @param array $existance_array    array to check for item existance in target array
     * @param array $assertion_array    do a operation like ` $value($target_array[$key]) `  foreach item in this array! Example:<br />
     * <pre><code>
     * $assertion_array = array(
     *      "messages" => array("\is_array", "\count", "!\is_null"),
     *      "item"          => array("some_func", function(data) { 
     *                                                                          return strlen(data);
     *                                                                    }
     * )
     * </code></pre>
     * @param array $check_sum_array    do a checksum on elements like ` $key =C= $value `
     * @param boolean $throw_exception    Throw exception if any error occures while processing $target_array
     * @param boolean $verbose_exception    check if when throwing exceptions the message should be verbose or not
     */
    public static function IsSecure(
                array $target_array, 
                array $existance_array = array(), 
                array $assertion_array = array(), 
                array $check_sum_array = array(), 
                $throw_exception = 1,
                $verbose_exception = 1
        ) {
            # initializing expcetions message templates
            $exception_verbose_msg = "Invalid argument.";
            $exception_mini_msg = "Invalid argument.";
            # packing passed array arguments
            $args = array('$target_array' => $target_array, 
                    '$existance_array' => $existance_array, 
                    '$assertion_array' => $assertion_array, 
                    '$check_sum_array'=>$check_sum_array);
            # checking instance existance
            foreach($args as $name=> $array) {
                if(!isset($array)) {
                   $exception_verbose_msg = "The '$name' array is NULL";
                   goto __THROW_EXCEPTION;
               }
            }
            # it is essentional for checksum opt. that the fields name should be in existance array
            if(count($check_sum_array) && !count($existance_array))
                throw new \InvalidArgumentException("\$existance_array is not supplied but demads operation on \$check_sum_array!!");

            # a forehead existance checking 
            if(count($existance_array) && !count($target_array)) {
                $exception_verbose_msg = "The target array in empy!";
                goto __THROW_EXCEPTION;
            }
            # adding check sum items into existance items
            foreach($check_sum_array as $key=> $value) {
                if(!isset($existance_array[$key]))
                    $existance_array[] = $key;
            }
            # checking for existance
            foreach($existance_array as $key) {
                if(!key_exists($key, $target_array)) {
                     $exception_verbose_msg = "The argument `$key` didn't supplied";
                     goto __THROW_EXCEPTION;
                }
            }
            # checking for checksums
            foreach($check_sum_array as $key=> $value) {
                if($target_array[$key] != $value) {
                     $exception_verbose_msg = "The `$key`'s value didn't match with `$value`";
                     goto __THROW_EXCEPTION;
                }
            }
             # backup assertion option values
             $asrt_bk_w = assert_options(ASSERT_WARNING);
             $asrt_bk_qe = assert_options(ASSERT_QUIET_EVAL);
             # suppressing assertion warnings
             assert_options(ASSERT_WARNING, 0);
             # Backup function collection container 
             $__FUNCS = array();
             # check if there are any valid element in given arg?
             $_is__FUNC_avail = function($__FUNCS) { return $__FUNCS && is_array($__FUNCS) && count($__FUNCS); };
             foreach($assertion_array as $arg => $func) {
__ASSERT_FUNCS:
                # check if this is a collection of functions
                if(is_array($func))
                    # if so? back it up.
                    $__FUNCS  = $func;
                # if there are any backed-up function collection?
                if($_is__FUNC_avail($__FUNCS))
                    # dequeue a function from collection
                    $func = array_shift($__FUNCS);
                # check if should use NEG validation?
                $should_neg = is_string($func) && (@$func[0] === "!");
                # if we are using NEG validation
                if($should_neg)
                    # fetch NEG-free function's name
                    $func = substr($func, 1);
                 # checking for function existance
                 if(!is_callable($func)) {
                     $exception_verbose_msg = "Unable to call `$func()`";
                     goto __THROW_EXCEPTION;
                 }
                 # if we are using NEGATIVE validation?
                 if($should_neg && !assert(!$func($target_array[$arg]))) {
                     $exception_verbose_msg = "assertion failed on calling `[!]".($func instanceof \Closure ? "Anonymous" : "$func")."(".json_encode($target_array[$arg]).")`";
                     goto __THROW_EXCEPTION;  
                 }
                 # if we are using POSIVITIVE validtion?
                 elseif(!$should_neg && !assert($func($target_array[$arg]))) {
                     $exception_verbose_msg = "assertion failed on calling `".($func instanceof \Closure ? "__Anonymous__Func__" : "$func")."(".json_encode($target_array[$arg]).")`";
                     goto __THROW_EXCEPTION;
                 }
__ASSERT_FUNCS_ARRAY:
                if($_is__FUNC_avail($__FUNCS))
                    goto __ASSERT_FUNCS;
             }
             #restore assertion option values
             assert_options(ASSERT_WARNING, $asrt_bk_w);
             assert_options(ASSERT_QUIET_EVAL, $asrt_bk_qe);
             # if we reach here its all OK
             return true;
             # throw exception section
    __THROW_EXCEPTION:
            # if throw expection is enabled
            if($throw_exception)
                # throw invalid argument exception using provided msg
                throw new \InvalidArgumentException($verbose_exception ? $exception_verbose_msg : $exception_mini_msg);
            # otherwise return false
            else return false;
    }

    /**
     * check if passed string is secured compatible operations in `GetSecureString()` function
     * @param \zinux\kernel\security\type $target_string
     * @param array $based_upon
     * @param string $for_uri Explicitly define the uri that request has come from(default: @$_SERVER['HTTP_REFERER'])
     * @param type $has_expire_date
     */
    public static function __validate_request(array $target_array, array $based_upon = array(), $has_expire_date = 0) {
        # generating security fields name
        $tn  = "__s_".substr(sha1('t'), 0,5);
        $hn = "__s_".substr(sha1('h'), 0,5);
        $en = "__s_".substr(sha1('e'), 0,5);
        $rn  = "__s_".substr(sha1('r'), 0,5);
        # assertion array for security checking 
        $asserts = array();
        # final $based_upon array for asserting $target_array
        $isSecure_based_upon = array($tn, $hn, $rn);
        # essential component of secure Array
        self::IsSecure($target_array, $isSecure_based_upon);
        # add time value of array to $based_upon[] required for hash
        $based_upon[] = $target_array[$tn];
        $based_upon[] = @session_id();
        # if array should has expiration value
        if($has_expire_date) {
            # check expiration field existance
            self::IsSecure($target_array, array($en));
            # if it does not exists we will never reach this line be cause of exception arising in above line
        }
        # if we make here and $has_expire_date is enabled? OR naturally $target_array contains an expiration field
        if($has_expire_date || isset($target_array[$en])) {
            # then for sure the exipration field is exists
            # adding expiration value of array to $based_upon[] required for hash
            $based_upon[] = $target_array[$en]; 
            # adding expiration value of array to $isSecure_based_upon[] required for hash
            $isSecure_based_upon[] = $target_array[$en]; 
            # anonymous function for asserting expiration value
            $expire_checkFunc = function($en) {
                # check if the expiration value is less than current time or not
                return $en <= time();
            };
            # register expiration assertion function/value
            $asserts[$expire_checkFunc]= $target_array[$en]; 
        }
        # get referer URI
        $refer = self::getRefererURI();
        # require hashing module
        require_once 'hash.php';
        # final checking of $target_array via its assertions
        self::IsSecure($target_array, $isSecure_based_upon, $asserts , array($hn => hash::Generate(implode("", $based_upon)), $rn => self::getURIHash(@$target_array[$tn].$refer)));
        # if we reach this line its all OK
        return true;
    }
}