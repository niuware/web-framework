<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {

    /**
    * Provide debugging tools
    */
    class Debug {
        
        private $time;

        function __construct() {

            $this->startTimer();
        }

        /**
        * Starts the debugging timer
        */
        private function startTimer() {
            
            set_time_limit(120);

            $this->time = $this->microtimeFloat();
        }

        /**
        * Converts the time to a float number
        * @return float Time that has passed
        */
        private function microtimeFloat() {
            
            list($usec, $sec) = explode(" ", microtime());
            
            return ((float) $usec + (float) $sec);
        }
        
        /**
        * Shows the ellapsed time during the debugging session
        */
        public function output() {
            
            echo "Load time: " . ($this->microtimeFloat() - $this->time);

        }
    }
}