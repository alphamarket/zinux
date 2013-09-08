<?php
namespace zinux\kernel\utilities;

require_once dirname(__FILE__).'/../../baseZinux.php';
/**
 * @author dariush
 * @version 1.0
 * @created 04-Sep-2013 15:50:22
 */
class debug extends \zinux\baseZinux
{
	/**
	 * Debug passed varibale
	 * @param $mixed $param
	 * @param boolean $die die after debug
	 * @param boolean $var_dump var_dump the param
	 * 
	 * @param param
	 * @param die
	 * @param var_dump
	 */
	public static function _var($param, $die = 0, $var_dump = 0)
	{
            echo '<pre>';
            $var_dump?var_dump($param):print_r($param);
            echo '</pre>';
            if($die)
                die();
	}

	/**
	 * Print a stack_stace till before calling this function
	 * @param boolean $die die after stack trace
	 * 
	 * @param die
	 */
	public static function stack_trace($die = 0)
	{
            try
            {
                throw new \Exception();
            }
            catch(\Exception $e)
            {
                self::_var(preg_replace("/\n/i", "<br />", $e->getTraceAsString()),$die);
            }
	}

	/**
	 * Do do back trace.
	 */
	public static function backtrace()
	{
            self::_var(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),1);
	}

    public function Initiate(){}
}
?>