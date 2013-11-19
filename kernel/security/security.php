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
     * @param boolean $has_expire_date the secure string should has expiration date
     * @param unsigned integer $second_to_expire_from_now the seconds to expire the secure string
     * @return string the secure string
     * @throws \InvalidArgumentException arises if condition <b>$has_expire_date && $seconds_to_expire_from_now<=0</b> satisfied
     */
    public static function GetHashString(array $based_upon = array(), $has_expire_date = 0, $seconds_to_expire_from_now = 0)
    {
            if($has_expire_date && $seconds_to_expire_from_now<=0)
                throw new \InvalidArgumentException("The '\$second_to_expire_from_now' didn't provide");
            $t = time();
            $based_upon[] = $t;
            $tn = "s_".substr(sha1('t'), 0, 5);
            $link ="&$tn=$t";
            if($has_expire_date)
            {
                $en = "s_".substr(sha1('e'), 0, 5);
                $et = time()+$seconds_to_expire_from_now;
                $based_upon[] = $et;
                $link .= "&$en=$et";
            }
            require_once 'hash.php';
            $h = hash::Generate(implode("", $based_upon));
            $hn = "s_".substr(sha1('h'), 0, 5);
            $link = $link."&$hn=$h";
            return $link;
    }

	/**
	 * Checks if URI is secure with provided parameters
	 * 
	 * @param array $target_array    Target array to examine parameters
	 * @param array $existance_array    array to check for item existance in target array
	 * @param array $assertion_array    do a operation like ` $key($value) `  foreach item in this array!
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
                $verbose_exception = 0
        )
	{
            # initializing expcetions message templates
            $exception_verbose_msg = "Invalid argument.";
            $exception_mini_msg = "Invalid argument.";
            # packing passed array arguments
            $args = array('$target_array' => $target_array, 
                    '$existance_array' => $existance_array, 
                    '$assertion_array' => $assertion_array, 
                    '$check_sum_array'=>$check_sum_array);
            # checking instance existance
            foreach($args as $name=> $array)
            {
                if(!isset($array))
               {
                   $exception_verbose_msg = "The '$name' array is NULL";
                   goto __THROW_EXCEPTION;
               }
            }
            # it is essentional for checksum opt. that the fields name should be in existance array
            if(count($check_sum_array) && !count($existance_array))
                throw new \InvalidArgumentException("\$existance_array is not supplied but demads operation on \$check_sum_array!!");
           
            # a forehead existance checking 
            if(count($existance_array) && !count($target_array))
            {
                $exception_verbose_msg = "The target array in empy!";
                goto __THROW_EXCEPTION;
            }
            # adding check sum items into existance items
            foreach($check_sum_array as $key=> $value)
            {
                if(!isset($existance_array[$key]))
                    $existance_array[] = $key;
            }
            # checking for existance
            foreach($existance_array as $value)
            {
                if(!isset($target_array[$value]))
                {
                     $exception_verbose_msg = "The argumen `$value` didn't supplied";
                     goto __THROW_EXCEPTION;
                }
            }
            # checking for checksums
            foreach($check_sum_array as $key=> $value)
            {
                if($target_array[$key] != $value)
                {
                     $exception_verbose_msg = "The `$key`'s value didn't match with `$value`";
                     goto __THROW_EXCEPTION;
                }
            }
             # backup assertion option values
             $asrt_bk_w = assert_options(ASSERT_WARNING);
             $asrt_bk_qe = assert_options(ASSERT_QUIET_EVAL);
             # suppressing assertion warnings
             assert_options(ASSERT_WARNING, 0);
             foreach($assertion_array as $func=> $arg)
             {
                 # checking for function existance
                 if(!function_exists($func) || !is_callable($func))
                 {
                     $exception_verbose_msg = "Unable to call `$func()`";
                     goto __THROW_EXCEPTION;
                 }
                 if(is_string($arg))
                     $arg = "'$arg'";
                 # asserting the function call
                 if(!assert("$func($arg)"))
                 {
                     $exception_verbose_msg = "assertion failed on calling `$func($args)`";
                     goto __THROW_EXCEPTION;
                 }
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
     * 
     * @param \zinux\kernel\security\type $target_string
     * @param array $based_upon
     * @param type $has_expire_date
     */
    public static function ArrayHashCheck(array $target_array, array $based_upon = array(), $has_expire_date = 0)
    {
        # generating security fields name
        $tn = "s_".substr(sha1('t'), 0,5);
        $hn = "s_".substr(sha1('h'), 0,5);
        $en = "s_".substr(sha1('e'), 0,5);
        # assertion array for security checking 
        $asserts = array();
        # final $based_upon array for asserting $target_array
        $isSecure_based_upon = array($tn, $hn);
        # essential component of secure Array
        self::IsSecure($target_array, $isSecure_based_upon);
        # add time value of array to $based_upon[] required for hash
        $based_upon[] = $target_array[$tn];
        # if array should has expiration value
        if($has_expire_date)
        {
            # check expiration field existance
            self::IsSecure($target_array, array($en));
            # if it does not exists we will never reach this line be cause of exception arising in above line
        }
        # if we make here and $has_expire_date is enabled? OR naturally $target_array contains an expiration field
        if($has_expire_date || isset($target_array[$en]))
        {
            # then for sure the exipration field is exists
            # adding expiration value of array to $based_upon[] required for hash
            $based_upon[] = $target_array[$en]; 
            # adding expiration value of array to $isSecure_based_upon[] required for hash
            $isSecure_based_upon[] = $target_array[$en]; 
            # anonymous function for asserting expiration value
            $expire_checkFunc = function($en){
                    # check if the expiration value is less than current time or not
                    return $en <= time();
                };
            # register expiration assertion function/value
            $asserts[$expire_checkFunc]= $target_array[$en]; 
        }
        require_once 'hash.php';
        # final checking of $target_array via its assertions
        self::IsSecure($target_array, $isSecure_based_upon, $asserts , array($hn=>hash::Generate(implode("", $based_upon))));
        # if we reach this line its all OK
        return true;
    }
}