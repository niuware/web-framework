<?php 

namespace Niuware\WebFramework {

    class Debug {
        
        private $time;

        function __construct() {

            $this->startTimer();
        }

        private function startTimer() {
            
            set_time_limit(120);

            $this->time = $this->microtimeFloat();
        }

        private function microtimeFloat() {
            
            list($usec, $sec) = explode(" ", microtime());
            
            return ((float) $usec + (float) $sec);
        }
        
        public function output() {
            
            echo "Load time: " . ($this->microtimeFloat() - $this->time);

        }
    }
}