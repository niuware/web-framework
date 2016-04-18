<?php 

/**
* This class is part of the core of Niuware WebFramework 
* and is not particularly intended to be modified.
* For information about the license please visit the 
* GIT repository at:
* https://github.com/niuware/web-framework
*/
namespace Niuware\WebFramework {
    
    require_once 'lib/htmlpurifier/HTMLPurifier.auto.php';
    
    /**
    * Enables deep user input filtering using the HTMLPurifier
    * external library 
    */
    class Filter  {
        
        private $config;
        
        private $purifier;
        
        function __construct() {
            
            $this->purifier = null;
            $this->config = \HTMLPurifier_Config::createDefault();
            $this->config->set('Core.Encoding', 'UTF-8');
        }
        
        /**
        * Creates a new instances of the HTMLPurifier library
        */
        public function configure() {
            
            $this->purifier = new \HTMLPurifier($this->config);
        }
        
        /**
        * Filters an input string
        * @param string $html String to filter
        * @return string The filtered string
        */
        public function filter($html) {
            
            return $this->purifier->purify($html);
        }
    }
}