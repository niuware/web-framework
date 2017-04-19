<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework;

/**
* Provide debugging tools
*/
class Debug {

    private static $time;
    private static $logs = [];

    /**
     * Starts the debugger timer
     */
    public static function start() {

        self::startTimer();
    }
    
    /**
     * Adds an entry to the debugger log
     * @param array $message Message(s) to append
     */
    public static function log(array $message) {
        
        self::$logs[] = $message;
    }

    /**
    * Starts the debugging timer
    */
    private static function startTimer() {

        set_time_limit(120);

        self::$time = self::microtimeFloat();
    }

    /**
    * Converts the time to a float number
    * @return float Time that has passed
    */
    private static function microtimeFloat() {

        list($usec, $sec) = explode(" ", microtime());

        return ((float) $usec + (float) $sec);
    }

    /**
    * Prints the debug output (log and render time)
    */
    public static function output() {

        echo "Load time: " . (self::microtimeFloat() - self::$time);
        
        foreach (self::$logs as $log) {
            
            var_dump($log);
        }
    }
}