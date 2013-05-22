<?php
namespace iMVC\Tools;

class Debug
{
    /**
     * Debug passed varibale 
     * @param $mixed $param
     * @param boolean $die die after debug
     * @param boolean $var_dump var_dump the param
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
     */
    public static function stack_trace($die = 0)
    {
        try
        {
            throw new \Exception();
        }
        catch(\Exception $e)
        {
            $p = explode("\n", $e->getTraceAsString());
            $s = "";
            for($i=1;$i<count($p);$i++)
                $s .= $p[$i].'<br />';
            self::_var($s,$die);
        }
    }
    /**
     * Do do back trace.
     */
    public function backtrace()
    {
        self::_var(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),1);
    }
}