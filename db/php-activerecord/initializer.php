<?php
namespace ActiveRecord;

require_once 'vendor/ActiveRecord.php';

/**
 * Description of php-activerecord initializer
 * @author dariush
 * @version 1.0
 * @updated 04-Sep-2013 17:42:31
 */
class initializer
{

	function __construct()
	{
	}

	function __destruct()
	{
	}


	public function Initiate(){ }
    
    public function InitActiveRecord(\iMVC\Routing\Request &$request)
    { 
        if(!isset($GLOBALS[CONFIGS]['db']))
        {
            trigger_error("In configuration db setting did not found, ActiveRecord is not loaded ....");
            return;
        }
        if(isset($GLOBALS[CONFIGS]['db']['suppress']) && $GLOBALS[CONFIGS]['db']['suppress'])
        {
            trigger_error("Suppressed database active record loader.");
            return;
        }
        if(!isset($GLOBALS[CONFIGS]['db']['type']))
            $GLOBALS["CONFIGS"]['db']['type'] = "mysql";

        $db_type = $GLOBALS[CONFIGS]['db']['type'];
        $username = $GLOBALS[CONFIGS]['db']['username'];
        $password = $GLOBALS[CONFIGS]['db']['password'];
        $host = $GLOBALS[CONFIGS]['db']['host'];
        $db_name = $GLOBALS[CONFIGS]['db']['name'];

        # define a dynamic connection string according to incomming request
        $connections = array(
            RUNNING_ENV => "$db_type://{$username}:{$password}@{$host}/{$db_name}",
        );

        # must issue a "use" statement in your closure if passing variables
        \ActiveRecord\Config::initialize(function($cfg) use ($connections, $request)
        {
            foreach ($GLOBALS[CONFIGS]['imvc']['module'] as $key => $module) 
            {
                $db_m_path = isset($GLOBALS[CONFIGS]['module']['models']['db'])?$GLOBALS[CONFIGS]['module']['models']['db']:'Models/DB';
                
                $db_m_path = MODULE_PATH."$module/".$db_m_path;

                if(!file_exists($db_m_path))
                {
                    trigger_error("Database model's directory didn't found at '$db_m_path'");
                }

                \ActiveRecord\Config::instance()->set_model_directory($db_m_path);
            }

            $cfg->set_connections($connections);
            
            $cfg->set_default_connection(RUNNING_ENV);
            
        });
        # test database connection
        try
        {
             \ActiveRecord\Connection::instance($connections[RUNNING_ENV])->query("SHOW TABLES;");
        }
        catch (\Exception $pdoe)
        {
            trigger_error("Could not stablish a connection with database : '{$connections[RUNNING_ENV]}'.");
            echo "<br />Message: <br />";
            echo \iMVC\Tools\Debug::_var($pdoe->getMessage());
        }
    }

}
